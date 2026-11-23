@extends('app')

@section('pageTitle', 'Statistik & Analitik Pasien')

@section('content')
    @php($maxPatients = max(1, $monthlyPatients->max('total')))
    <div class="page-header"><div><p class="eyebrow">Analitik pasien</p><h2>Statistik &amp; Analitik Pasien</h2></div></div>
    <div class="stats-grid">
        @forelse ($categories as $category)
            @php($percent = $totalPatients ? round($category->patients_count / $totalPatients * 100, 1) : 0)
            <article class="stat-card"><div class="stat-icon">🩺</div><div><p>{{ $category->name }}</p><h3>{{ $category->patients_count }}</h3><small>{{ $percent }}% dari total pasien</small></div></article>
        @empty
            <div class="empty-state">Belum ada kategori untuk ditampilkan.</div>
        @endforelse
    </div>
    <section class="panel">
        <div class="panel-heading"><h3>Distribusi Pasien per Kategori</h3><span>Total {{ $totalPatients }} pasien</span></div>
        <div class="category-list">
            @foreach ($categories as $category)
                @php($percent = $totalPatients ? round($category->patients_count / $totalPatients * 100, 1) : 0)
                <div class="category-row"><div class="category-meta"><strong>{{ $category->name }}</strong><small>{{ $category->patients_count }} pasien</small></div><div class="progress-wrap"><div class="progress-bar"><span style="width: {{ $percent }}%"></span></div><strong>{{ $percent }}%</strong></div></div>
            @endforeach
        </div>
    </section>
    <section class="panel">
        <div class="panel-heading"><h3>Perkembangan Diagnosis 6 Bulan Terakhir</h3></div>
        <div class="chart-bars">@foreach ($monthlyPatients as $month)<div class="bar-stack"><span class="bar" style="height: {{ max(24, $month['total'] / $maxPatients * 100) }}%">{{ $month['total'] }}</span><small>{{ $month['label'] }}</small></div>@endforeach</div>
    </section>
@endsection
