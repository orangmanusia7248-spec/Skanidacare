<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DiseaseCategory;
use App\Models\HospitalInformation;
use App\Models\Patient;
use App\Services\MedicalRecordNumberGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SkanidaCareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_protects_the_dashboard_and_logout_clears_the_session(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'password-test-123',
        ]);

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password-test-123',
        ])->assertRedirect(route('dashboard'));
        $this->get(route('dashboard'))->assertOk()->assertSee('Total Pasien');
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_patient_creation_and_duplicate_medical_record_validation(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'password-test-123',
        ]);
        $category = DiseaseCategory::create([
            'slug' => 'onkologi',
            'name' => 'Onkologi',
            'description' => 'Kategori untuk pengujian.',
        ]);
        $payload = [
            'name' => 'Pasien Fiktif',
            'medical_record_number' => 'RM-2099-999999',
            'date_of_birth' => '1990-01-01',
            'gender' => 'Perempuan',
            'address' => 'Alamat fiktif',
            'phone' => '08000000000',
            'disease_category_id' => $category->id,
            'diagnosis_date' => '2024-01-01',
            'notes' => 'Data pengujian.',
        ];

        $this->actingAs($admin, 'admin')
            ->get(route('patients.create'))
            ->assertOk()
            ->assertSee('Nomor rekam medis dibuat otomatis oleh sistem.')
            ->assertSee('readonly', false)
            ->assertSee('RM-'.now()->year.'-000001');
        $this->get(route('patients.create'))->assertSee('RM-'.now()->year.'-000001');
        $this->assertDatabaseMissing('patient_record_sequences', ['year' => now()->year]);

        $this->actingAs($admin, 'admin')
            ->post(route('patients.store'), $payload)
            ->assertRedirect(route('patients.index'));
        $firstRecordNumber = sprintf('RM-%04d-000001', now()->year);
        $this->assertDatabaseHas('patients', ['medical_record_number' => $firstRecordNumber]);

        $duplicatePayload = $payload;
        $this->actingAs($admin, 'admin')
            ->post(route('patients.store'), $duplicatePayload)
            ->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['medical_record_number' => sprintf('RM-%04d-000002', now()->year)]);

        $patient = Patient::where('medical_record_number', $firstRecordNumber)->firstOrFail();
        $payload['name'] = 'Pasien Fiktif Diperbarui';
        $payload['medical_record_number'] = 'RM-2099-000001';
        $this->actingAs($admin, 'admin')
            ->put(route('patients.update', $patient), $payload)
            ->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'name' => 'Pasien Fiktif Diperbarui',
            'medical_record_number' => $firstRecordNumber,
        ]);

        $this->delete(route('patients.destroy', $patient))->assertRedirect(route('patients.index'));
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
        $this->post(route('patients.store'), $payload)->assertRedirect(route('patients.index'));
        $this->assertDatabaseHas('patients', ['medical_record_number' => sprintf('RM-%04d-000003', now()->year)]);
    }

    public function test_failed_patient_transaction_does_not_consume_a_record_number(): void
    {
        $generator = app(MedicalRecordNumberGenerator::class);
        $year = now()->year;

        try {
            $generator->synchronized(fn () => DB::transaction(function () use ($generator): void {
                $generator->next();
                throw new \RuntimeException('Simulated patient save failure.');
            }, attempts: 5));
            $this->fail('The simulated save should fail.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated patient save failure.', $exception->getMessage());
        }

        $this->assertDatabaseMissing('patient_record_sequences', ['year' => $year]);
        $this->assertSame(sprintf('RM-%04d-000001', $year), $generator->preview());
    }

    public function test_record_number_sequence_restarts_for_a_new_year(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'password-test-123',
        ]);
        $category = DiseaseCategory::create([
            'slug' => 'onkologi',
            'name' => 'Onkologi',
            'description' => 'Kategori untuk pengujian.',
        ]);
        $payload = [
            'name' => 'Pasien Tahun Baru',
            'date_of_birth' => '1990-01-01',
            'gender' => 'Perempuan',
            'address' => 'Alamat fiktif',
            'phone' => '08000000000',
            'disease_category_id' => $category->id,
            'diagnosis_date' => '2024-01-01',
        ];

        Carbon::setTestNow(now()->setDate(2027, 1, 1));
        try {
            $this->actingAs($admin, 'admin')
                ->post(route('patients.store'), $payload)
                ->assertRedirect(route('patients.index'));
            $this->assertDatabaseHas('patients', ['medical_record_number' => 'RM-2027-000001']);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_category_used_by_patients_cannot_be_deleted(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'password-test-123',
        ]);
        $category = DiseaseCategory::create([
            'slug' => 'autoimun',
            'name' => 'Autoimun',
            'description' => 'Kategori untuk pengujian.',
        ]);
        Patient::create([
            'name' => 'Pasien Fiktif',
            'medical_record_number' => 'TEST-0002',
            'date_of_birth' => '1990-01-01',
            'gender' => 'Laki-laki',
            'address' => 'Alamat fiktif',
            'phone' => '08000000000',
            'disease_category_id' => $category->id,
            'diagnosis_date' => '2024-01-01',
        ]);

        $this->actingAs($admin, 'admin')
            ->delete(route('diseases.destroy', $category))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('disease_categories', ['id' => $category->id]);

        $category->patients()->delete();
        $this->delete(route('diseases.destroy', $category))
            ->assertRedirect(route('diseases.index'));
        $this->assertDatabaseMissing('disease_categories', ['id' => $category->id]);
    }

    public function test_admin_can_update_hospital_information_and_download_pdf_report(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'password-test-123',
        ]);
        HospitalInformation::create([
            'name' => 'RS Uji',
            'address' => 'Alamat lama',
            'phone' => '08000000000',
            'email' => 'rs@example.test',
            'description' => 'Deskripsi awal.',
            'facilities' => ['Poliklinik'],
        ]);
        $category = DiseaseCategory::create([
            'slug' => 'onkologi',
            'name' => 'Onkologi',
            'description' => 'Kategori untuk pengujian.',
        ]);
        Patient::create([
            'name' => 'Pasien PDF Fiktif',
            'medical_record_number' => 'TEST-PDF-0001',
            'date_of_birth' => '1990-01-01',
            'gender' => 'Perempuan',
            'address' => 'Alamat fiktif',
            'phone' => '08000000000',
            'disease_category_id' => $category->id,
            'diagnosis_date' => '2024-01-01',
        ]);

        $this->actingAs($admin, 'admin')
            ->put(route('hospital.update'), [
                'name' => 'RS Uji Baru',
                'address' => 'Alamat baru',
                'phone' => '08000000001',
                'email' => 'rs@example.test',
                'description' => 'Deskripsi baru.',
                'facilities' => 'ICU, Farmasi',
            ])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('hospital_information', ['name' => 'RS Uji Baru']);

        $this->get(route('reports.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('laporan-medis-skanida-care.pdf');
    }

    public function test_admin_can_change_password_after_entering_the_current_password(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'password-test-123',
        ]);

        $this->actingAs($admin, 'admin')
            ->put(route('settings.profile'), [
                'name' => 'Admin Baru',
                'email' => 'admin-baru@example.test',
                'phone' => '08000000000',
            ])
            ->assertSessionHas('success');

        $this->put(route('settings.password'), [
            'current_password' => 'password-test-123',
            'password' => 'password-test-456',
            'password_confirmation' => 'password-test-456',
        ])
            ->assertSessionHas('success');

        $this->post(route('logout'));
        $this->post(route('login.store'), [
            'email' => 'admin-baru@example.test',
            'password' => 'password-test-456',
        ])->assertRedirect(route('dashboard'));
    }
}
