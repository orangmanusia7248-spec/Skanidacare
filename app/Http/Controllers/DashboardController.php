<?php

namespace App\Http\Controllers;

use App\Models\DiseaseCategory;
use App\Models\Patient;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPatients = Patient::count();
        $categories = DiseaseCategory::withCount('patients')->orderBy('name')->get();
        $monthlyPatients = collect(range(5, 0))->map(function (int $offset): array {
            $month = now()->startOfMonth()->subMonths($offset);

            return [
                'label' => $month->locale('id')->translatedFormat('M'),
                'total' => Patient::whereBetween('diagnosis_date', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])->count(),
            ];
        });

        return view('dashboard', [
            'totalPatients' => $totalPatients,
            'totalCategories' => $categories->count(),
            'newPatients' => Patient::whereBetween('diagnosis_date', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'categories' => $categories,
            'monthlyPatients' => $monthlyPatients,
            'recentPatients' => Patient::with('diseaseCategory')->latest('diagnosis_date')->limit(5)->get(),
        ]);
    }

    public function statistics(): View
    {
        $categories = DiseaseCategory::withCount('patients')->orderBy('name')->get();

        return view('statistics', [
            'categories' => $categories,
            'totalPatients' => Patient::count(),
            'monthlyPatients' => collect(range(5, 0))->map(function (int $offset): array {
                $month = now()->startOfMonth()->subMonths($offset);

                return [
                    'label' => $month->locale('id')->translatedFormat('M Y'),
                    'total' => Patient::whereBetween('diagnosis_date', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth(),
                    ])->count(),
                ];
            }),
        ]);
    }
}
