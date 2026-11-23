<?php

namespace App\Http\Controllers;

use App\Models\DiseaseCategory;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('reports.index', [
            'patients' => $this->filteredPatients($request)->paginate(15)->withQueryString(),
            'categories' => DiseaseCategory::orderBy('name')->get(),
            'filters' => $request->only(['category', 'start_date', 'end_date']),
        ]);
    }

    public function pdf(Request $request)
    {
        $patients = $this->filteredPatients($request)->get();

        return Pdf::loadView('reports.pdf', [
            'patients' => $patients,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->download('laporan-medis-skanida-care.pdf');
    }

    public function csv(Request $request): StreamedResponse
    {
        $patients = $this->filteredPatients($request)->get();
        $callback = static function () use ($patients): void {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama', 'No. Rekam Medis', 'Kategori', 'Jenis Kelamin', 'Tanggal Diagnosis']);

            foreach ($patients as $patient) {
                fputcsv($file, [
                    $patient->name,
                    $patient->medical_record_number,
                    $patient->diseaseCategory->name,
                    $patient->gender,
                    $patient->diagnosis_date->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return Response::streamDownload($callback, 'laporan-medis-skanida-care.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function filteredPatients(Request $request)
    {
        $validated = $request->validate([
            'category' => ['nullable', 'integer', 'exists:disease_categories,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        return Patient::query()
            ->with('diseaseCategory')
            ->when($validated['category'] ?? null, fn ($query, $category) => $query->where('disease_category_id', $category))
            ->when($validated['start_date'] ?? null, fn ($query, $date) => $query->whereDate('diagnosis_date', '>=', $date))
            ->when($validated['end_date'] ?? null, fn ($query, $date) => $query->whereDate('diagnosis_date', '<=', $date))
            ->orderBy('diagnosis_date');
    }
}
