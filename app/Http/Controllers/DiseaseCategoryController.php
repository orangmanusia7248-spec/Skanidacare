<?php

namespace App\Http\Controllers;

use App\Models\DiseaseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DiseaseCategoryController extends Controller
{
    public function index(): View
    {
        return view('diseases.index', [
            'categories' => DiseaseCategory::withCount('patients')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('diseases.form', ['category' => new DiseaseCategory]);
    }

    public function store(Request $request): RedirectResponse
    {
        DiseaseCategory::create($this->validatedCategory($request));

        return redirect()->route('diseases.index')->with('success', 'Kategori penyakit berhasil ditambahkan.');
    }

    public function edit(DiseaseCategory $disease): View
    {
        return view('diseases.form', ['category' => $disease]);
    }

    public function update(Request $request, DiseaseCategory $disease): RedirectResponse
    {
        $disease->update($this->validatedCategory($request, $disease));

        return redirect()->route('diseases.index')->with('success', 'Kategori penyakit berhasil diperbarui.');
    }

    public function destroy(DiseaseCategory $disease): RedirectResponse
    {
        if ($disease->patients()->exists()) {
            return back()->with('error', 'Kategori masih terhubung dengan pasien dan tidak dapat dihapus.');
        }

        $disease->delete();

        return redirect()->route('diseases.index')->with('success', 'Kategori penyakit berhasil dihapus.');
    }

    private function validatedCategory(Request $request, ?DiseaseCategory $disease = null): array
    {
        $request->merge([
            'slug' => Str::lower(trim((string) $request->input('slug'))),
        ]);

        return $request->validate([
            'slug' => [
                'required',
                'string',
                'max:80',
                'alpha_dash',
                Rule::unique('disease_categories', 'slug')->ignore($disease?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
        ]);
    }
}
