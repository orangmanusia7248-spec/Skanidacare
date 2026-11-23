<?php

namespace App\Http\Controllers;

use App\Models\DiseaseCategory;
use Illuminate\View\View;

class CohortController extends Controller
{
    public function index(): View
    {
        $cohorts = DiseaseCategory::query()
            ->whereIn('slug', ['onkologi', 'hiv', 'autoimun'])
            ->with(['patients' => fn ($query) => $query->with('diseaseCategory')->latest('diagnosis_date')])
            ->withCount('patients')
            ->orderBy('name')
            ->get()
            ->keyBy('slug');

        return view('cohorts', compact('cohorts'));
    }
}
