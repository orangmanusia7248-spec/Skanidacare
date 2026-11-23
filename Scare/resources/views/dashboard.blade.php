@extends('app')

@section('pageTitle', 'Dashboard Rumah Sakit')

@section('content')
    @php($maxPatients = max(1, $monthlyPatients->max('total')))
    <div class="page-header">
        <div><p class="eyebrow">Ringkasan operasional</p><h2>Dashboard Rumah Sakit</h2></div>
        <span class="header-badge">Data diperbarui {{ now()->locale('id')->translatedFormat('d M Y') }}</span>
    </div>
    <div class="stats-grid">
        <div class="stat-card accent-blue"><div class="stat-icon">👥</div><div><p>Total Pasien</p><h3>{{ number_format($totalPatients) }}</h3></div></div>
        <div class="stat-card accent-purple"><div class="stat-icon">🧬</div><div><p>Total Kategori</p><h3>{{ number_format($totalCategories) }}</h3></div></div>
        <div class="stat-card accent-green"><div class="stat-icon">📈</div><div><p>Diagnosis Bulan Ini</p><h3>{{ number_format($newPatients) }}</h3></div></div>
    </div>
    <div class="content-grid two-col">
        <section class="panel">
            <div class="panel-heading"><h3>Persentase Pasien per Kategori</h3></div>
            <div class="category-list">
                @forelse ($categories as $category)
                    @php($percent = $totalPatients ? round($category->patients_count / $totalPatients * 100, 1) : 0)
                    <div class="category-row">
                        <div class="category-meta"><strong>{{ $category->name }}</strong><small>{{ $category->patients_count }} pasien</small></div>
                        <div class="progress-wrap"><div class="progress-bar"><span style="width: {{ $percent }}%"></span></div><strong>{{ $percent }}%</strong></div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada kategori penyakit.</div>
                @endforelse
            </div>
        </section>
        <section class="panel">
            <div class="panel-heading"><h3>Grafik Diagnosis 6 Bulan Terakhir</h3></div>
            <div class="chart-bars">
                @foreach ($monthlyPatients as $month)
                    <div class="bar-stack"><span class="bar" style="height: {{ max(24, $month['total'] / $maxPatients * 100) }}%">{{ $month['total'] }}</span><small>{{ $month['label'] }}</small></div>
                @endforeach
            </div>
        </section>
    </div>
    <section class="panel">
        <div class="panel-heading"><h3>Pasien Terbaru</h3><a href="{{ route('patients.index') }}">Lihat semua</a></div>
        <div class="table-wrap">
            <table><thead><tr><th>Nama</th><th>No. Rekam Medis</th><th>Kategori</th><th>Tanggal Diagnosis</th></tr></thead>
                <tbody>
                    @forelse ($recentPatients as $patient)
                        <tr><td>{{ $patient->name }}</td><td>{{ $patient->medical_record_number }}</td><td>{{ $patient->diseaseCategory->name }}</td><td>{{ $patient->diagnosis_date->locale('id')->translatedFormat('d M Y') }}</td></tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state">Belum ada data pasien.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
