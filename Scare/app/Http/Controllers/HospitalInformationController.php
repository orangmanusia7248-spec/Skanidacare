<?php

namespace App\Http\Controllers;

use App\Models\HospitalInformation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HospitalInformationController extends Controller
{
    public function edit(): View
    {
        return view('hospital', [
            'hospital' => HospitalInformation::query()->firstOrFail(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'facilities' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['facilities'] = collect(explode(',', $validated['facilities'] ?? ''))
            ->map(fn (string $facility) => trim($facility))
            ->filter()
            ->values()
            ->all();

        HospitalInformation::query()->firstOrFail()->update($validated);

        return back()->with('success', 'Informasi rumah sakit berhasil diperbarui.');
    }
}
