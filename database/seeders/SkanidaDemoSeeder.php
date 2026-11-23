<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\DiseaseCategory;
use App\Models\HospitalInformation;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class SkanidaDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('SKANIDA demo admin and patient records are only seeded in local/testing environments.');

            return;
        }

        Admin::firstOrCreate(
            ['email' => 'admin@skanida.id'],
            [
                'name' => 'Admin SKANIDA',
                'phone' => '+62 812-3456-7890',
                'password' => 'AdminSkanida123!',
            ],
        );

        HospitalInformation::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'RS Skanida Care',
                'address' => 'Jl. Mawar No. 18, Bandung, Indonesia',
                'phone' => '+62 22 4567 890',
                'email' => 'care@skanida.id',
                'description' => 'Rumah sakit umum yang berfokus pada pelayanan kesehatan terpadu.',
                'facilities' => ['ICU', 'Poliklinik', 'Laboratorium', 'Farmasi', 'Radiologi', 'Rehab Medik'],
            ],
        );

        $categories = [
            ['slug' => 'onkologi', 'name' => 'Onkologi', 'description' => 'Kohort pasien kanker dan penatalaksanaan terapi onkologi.'],
            ['slug' => 'hiv', 'name' => 'HIV/AIDS & IMS', 'description' => 'Kohort HIV dan infeksi menular seksual dengan pemantauan berkala.'],
            ['slug' => 'autoimun', 'name' => 'Penyakit Autoimun', 'description' => 'Pasien dengan proses autoimun kronis dan perawatan jangka panjang.'],
            ['slug' => 'kardiovaskular', 'name' => 'Kardiovaskular', 'description' => 'Penyakit jantung dan pembuluh darah.'],
        ];

        foreach ($categories as $category) {
            DiseaseCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }

        if (Patient::exists()) {
            return;
        }

        $categoryIds = DiseaseCategory::pluck('id', 'slug');
        $demoPatients = [
            ['Pasien Demo 01', 'onkologi', 'Perempuan', -1],
            ['Pasien Demo 02', 'hiv', 'Laki-laki', -2],
            ['Pasien Demo 03', 'autoimun', 'Perempuan', -3],
            ['Pasien Demo 04', 'kardiovaskular', 'Laki-laki', -4],
            ['Pasien Demo 05', 'onkologi', 'Perempuan', -5],
            ['Pasien Demo 06', 'hiv', 'Laki-laki', -6],
            ['Pasien Demo 07', 'autoimun', 'Perempuan', -7],
            ['Pasien Demo 08', 'kardiovaskular', 'Laki-laki', 0],
        ];

        foreach ($demoPatients as $index => [$name, $category, $gender, $monthOffset]) {
            Patient::create([
                'name' => $name,
                'medical_record_number' => sprintf('DEMO-%04d', $index + 1),
                'date_of_birth' => now()->subYears(25 + $index)->subDays($index * 17)->toDateString(),
                'gender' => $gender,
                'address' => 'Kota Bandung (data fiktif)',
                'phone' => sprintf('080000000%02d', $index + 1),
                'disease_category_id' => $categoryIds[$category],
                'diagnosis_date' => now()->startOfMonth()->addMonths($monthOffset)->addDays($index)->toDateString(),
                'notes' => 'Data demonstrasi fiktif untuk pengujian aplikasi.',
            ]);
        }
    }
}
