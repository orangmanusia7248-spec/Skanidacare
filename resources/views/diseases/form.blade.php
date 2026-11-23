@extends('app')

@section('pageTitle', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
    <div class="page-header"><div><p class="eyebrow">Klasifikasi penyakit</p><h2>{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h2></div><a class="ghost-btn" href="{{ route('diseases.index') }}">Kembali</a></div>
    <section class="panel">
        <form method="POST" action="{{ $category->exists ? route('diseases.update', $category) : route('diseases.store') }}" class="two-col-form">
            @csrf
            @if ($category->exists) @method('PUT') @endif
            <div class="field-group"><label for="category-name">Nama Kategori</label><input id="category-name" name="name" value="{{ old('name', $category->name) }}" required></div>
            <div class="field-group"><label for="category-slug">ID Kategori</label><input id="category-slug" name="slug" value="{{ old('slug', $category->slug) }}" pattern="[A-Za-z0-9_-]+" required></div>
            <div class="field-group full-width"><label for="category-description">Deskripsi</label><textarea id="category-description" name="description" rows="4" required>{{ old('description', $category->description) }}</textarea></div>
            <div class="form-actions full-width"><button class="primary-btn" type="submit">Simpan Kategori</button><a class="ghost-btn" href="{{ route('diseases.index') }}">Batal</a></div>
        </form>
    </section>
@endsection
