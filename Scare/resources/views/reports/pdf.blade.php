<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Laporan Medis SKANIDA CARE</title>
        <style>
            body { font-family: DejaVu Sans, sans-serif; color: #14253d; font-size: 11px; }
            h1 { margin-bottom: 4px; color: #0f3c9f; }
            p { margin-top: 0; color: #6680a6; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #dfeaf9; padding: 8px; text-align: left; }
            th { background: #eef5fe; }
        </style>
    </head>
    <body>
        <h1>SKANIDA CARE — Laporan Medis</h1>
        <p>Dicetak pada {{ $generatedAt->locale('id')->translatedFormat('d M Y H:i') }} · {{ $patients->count() }} data</p>
        <table>
            <thead><tr><th>Nama</th><th>No. Rekam Medis</th><th>Kategori</th><th>Jenis Kelamin</th><th>Tanggal Diagnosis</th></tr></thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr><td>{{ $patient->name }}</td><td>{{ $patient->medical_record_number }}</td><td>{{ $patient->diseaseCategory->name }}</td><td>{{ $patient->gender }}</td><td>{{ $patient->diagnosis_date->locale('id')->translatedFormat('d M Y') }}</td></tr>
                @empty
                    <tr><td colspan="5">Tidak ada data untuk filter yang dipilih.</td></tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
