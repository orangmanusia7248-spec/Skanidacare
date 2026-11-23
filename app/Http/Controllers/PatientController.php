<?php

namespace App\Http\Controllers;

use App\Models\DiseaseCategory;
use App\Models\Patient;
use App\Services\MedicalRecordNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:disease_categories,id'],
            'gender' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'diagnosis_date' => ['nullable', 'date'],
        ]);

        $patients = Patient::query()
            ->with('diseaseCategory')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->trim()->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('medical_record_number', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('disease_category_id', $request->integer('category')))
            ->when($request->filled('diagnosis_date'), fn ($query) => $query->whereDate('diagnosis_date', $request->date('diagnosis_date')))
            ->when($request->filled('gender'), fn ($query) => $query->where('gender', $request->string('gender')->toString()))
            ->latest('diagnosis_date')
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', [
            'patients' => $patients,
            'categories' => DiseaseCategory::orderBy('name')->get(),
        ]);
    }

    public function create(MedicalRecordNumberGenerator $recordNumbers): View
    {
        $patient = new Patient;
        $patient->medical_record_number = $recordNumbers->preview();

        return view('patients.form', [
            'patient' => $patient,
            'categories' => DiseaseCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, MedicalRecordNumberGenerator $recordNumbers): RedirectResponse
    {
        $attributes = $this->validatedPatient($request);

        $recordNumbers->synchronized(fn () => DB::transaction(function () use ($attributes, $recordNumbers): void {
            $attributes['medical_record_number'] = $recordNumbers->next();
            Patient::create($attributes);
        }, attempts: 5));

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil ditambahkan.');
    }

    public function show(Patient $patient): View
    {
        $patient->load('diseaseCategory');

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        return view('patients.form', [
            'patient' => $patient,
            'categories' => DiseaseCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $patient->update($this->validatedPatient($request, $patient));

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil dihapus.');
    }

    private function validatedPatient(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'disease_category_id' => ['required', 'exists:disease_categories,id'],
            'diagnosis_date' => ['required', 'date', 'after_or_equal:date_of_birth', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
