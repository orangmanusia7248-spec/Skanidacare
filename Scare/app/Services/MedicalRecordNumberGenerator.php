<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MedicalRecordNumberGenerator
{
    public function preview(): string
    {
        $year = now()->year;
        $sequence = DB::table('patient_record_sequences')
            ->where('year', $year)
            ->value('last_number');

        return $this->format($year, max((int) $sequence, $this->highestStoredSequence($year)) + 1);
    }

    public function next(): string
    {
        $year = now()->year;
        $initialSequence = $this->highestStoredSequence($year);

        DB::table('patient_record_sequences')->insertOrIgnore([
            'year' => $year,
            'last_number' => $initialSequence,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = DB::table('patient_record_sequences')
            ->where('year', $year)
            ->lockForUpdate()
            ->first();

        if ($sequence === null) {
            throw new RuntimeException('Could not lock the medical record sequence.');
        }

        $nextNumber = max((int) $sequence->last_number, $this->highestStoredSequence($year)) + 1;
        if ($nextNumber > 999999) {
            throw new RuntimeException("The medical record sequence for {$year} is exhausted.");
        }

        DB::table('patient_record_sequences')
            ->where('year', $year)
            ->update([
                'last_number' => $nextNumber,
                'updated_at' => now(),
            ]);

        return $this->format($year, $nextNumber);
    }

    public function synchronized(Closure $callback): mixed
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            return $callback();
        }

        $lockPath = sys_get_temp_dir()
            .DIRECTORY_SEPARATOR
            .'skanida-care-patient-sequence-'
            .hash('sha256', (string) config('database.connections.sqlite.database'))
            .'.lock';
        $lock = fopen($lockPath, 'c');

        if ($lock === false || ! flock($lock, LOCK_EX)) {
            if (is_resource($lock)) {
                fclose($lock);
            }

            throw new RuntimeException('Could not acquire the medical record sequence lock.');
        }

        try {
            return $callback();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function highestStoredSequence(int $year): int
    {
        $prefix = sprintf('RM-%04d-', $year);
        $highest = 0;

        foreach (DB::table('patients')
            ->where('medical_record_number', 'like', $prefix.'%')
            ->pluck('medical_record_number') as $recordNumber) {
            if (preg_match('/^RM-'.preg_quote((string) $year, '/').'-([0-9]{6})$/', $recordNumber, $matches)) {
                $highest = max($highest, (int) $matches[1]);
            }
        }

        return $highest;
    }

    private function format(int $year, int $sequence): string
    {
        if ($sequence > 999999) {
            throw new RuntimeException("The medical record sequence for {$year} is exhausted.");
        }

        return sprintf('RM-%04d-%06d', $year, $sequence);
    }
}
