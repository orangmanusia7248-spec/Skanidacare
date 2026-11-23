@extends('app')

@section('pageTitle', 'Laporan Medis')

@section('content')
    <div class="page-header">
        <div><p class="eyebrow">Laporan klinis</p><h2>Laporan Medis</h2></div>
        <div class="header-actions"><a class="ghost-btn" href="{{ route('reports.csv', request()->query()) }}">Unduh CSV</a><a class="primary-btn" href="{{ route('reports.pdf', request()->query()) }}">Unduh PDF</a><button class="ghost-btn" type="button" onclick="window.print()">Cetak Laporan</button></div>
    </div>
    <section class="panel filter-panel">
        <form method="GET" action="{{ route('reports.index') }}" class="filter-grid">
            <div class="field-group"><label for="report-category">Kategori Penyakit</label><select id="report-category" name="category"><option value="">Semua kategori</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="field-group"><label for="report-start">Dari Tanggal Diagnosis</label><input id="report-start" type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"></div>
            <div class="field-group"><label for="report-end">Sampai Tanggal Diagnosis</label><input id="report-end" type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"></div>
            <div class="form-actions full-width"><button type="submit" class="primary-btn">Terapkan Filter</button><a class="ghost-btn" href="{{ route('reports.index') }}">Reset</a></div>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-wrap"><table><thead><tr><th>Nama</th><th>No. Rekam Medis</th><th>Kategori</th><th>Jenis Kelamin</th><th>Tanggal Diagnosis</th></tr></thead><tbody>
            @forelse ($patients as $patient)
                <tr><td>{{ $patient->name }}</td><td>{{ $patient->medical_record_number }}</td><td>{{ $patient->diseaseCategory->name }}</td><td>{{ $patient->gender }}</td><td>{{ $patient->diagnosis_date->locale('id')->translatedFormat('d M Y') }}</td></tr>
            @empty
                <tr><td colspan="5"><div class="empty-state">Tidak ada laporan sesuai filter.</div></td></tr>
            @endforelse
        </tbody></table></div>
        <div class="pagination-row"><span>{{ $patients->total() }} data laporan</span>{{ $patients->links() }}</div>
    </section>
@endsection
