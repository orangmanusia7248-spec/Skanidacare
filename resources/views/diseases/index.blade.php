@extends('app')

@section('pageTitle', 'Kategori Penyakit')

@section('content')
    <div class="page-header"><div><p class="eyebrow">Klasifikasi penyakit</p><h2>Kategori Penyakit</h2></div><a class="primary-btn" href="{{ route('diseases.create') }}">+ Tambah Kategori</a></div>
    <div class="cards-grid four-col">
        @forelse ($categories as $category)
            <article class="info-card">
                <div class="card-head"><h4>{{ $category->name }}</h4><span class="badge">{{ $category->patients_count }} pasien</span></div>
                <p>{{ $category->description }}</p>
                <small>ID: {{ $category->slug }}</small>
                <div class="card-actions"><a class="table-btn warning" href="{{ route('diseases.edit', $category) }}">Edit</a><form method="POST" action="{{ route('diseases.destroy', $category) }}" data-confirm="Hapus kategori ini?">@csrf @method('DELETE')<button class="table-btn danger" type="submit">Hapus</button></form></div>
            </article>
        @empty
            <div class="empty-state">Belum ada kategori penyakit.</div>
        @endforelse
    </div>
@endsection
