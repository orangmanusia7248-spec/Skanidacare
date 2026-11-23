@extends('app')

@section('pageTitle', 'Direktori Kohort Penyakit')

@section('content')
    <div class="page-header"><div><p class="eyebrow">Direktori kohort</p><h2>Direktori Kohort Penyakit</h2></div></div>
    <div class="cards-grid three-col">
        @foreach (['onkologi' => 'Kohort Onkologi / Kanker', 'hiv' => 'Kohort HIV/AIDS / IMS', 'autoimun' => 'Kohort Penyakit Autoimun'] as $slug => $title)
            @php($category = $cohorts->get($slug))
            <section class="cohort-card">
                <div class="card-head"><h4>{{ $title }}</h4><span class="badge">{{ $category?->patients_count ?? 0 }} pasien</span></div>
                <p>{{ $category?->description ?? 'Kategori kohort belum dibuat.' }}</p>
                <ul class="mini-list">
                    @forelse ($category?->patients ?? [] as $patient)
                        <li><a href="{{ route('patients.show', $patient) }}">{{ $patient->name }} · {{ $patient->medical_record_number }}</a></li>
                    @empty
                        <li>Belum ada pasien pada kohort ini.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    </div>
@endsection
