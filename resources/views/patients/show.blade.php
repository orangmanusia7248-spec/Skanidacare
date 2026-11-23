@extends('app')

@section('pageTitle', 'Detail Pasien')

@section('content')
    <div class="page-header"><div><p class="eyebrow">Informasi pasien</p><h2>Detail Pasien</h2></div><div class="header-actions"><a class="ghost-btn" href="{{ route('patients.index') }}">Kembali</a><a class="primary-btn" href="{{ route('patients.edit', $patient) }}">Edit Data</a></div></div>
    <section class="panel">
        <div class="content-grid two-col">
            <div class="field-group"><label>Nama Lengkap</label><strong>{{ $patient->name }}</strong></div>
            <div class="field-group"><label>Nomor Rekam Medis</label><strong>{{ $patient->medical_record_number }}</strong></div>
            <div class="field-group"><label>Tanggal Lahir</label><strong>{{ $patient->date_of_birth->locale('id')->translatedFormat('d M Y') }}</strong></div>
            <div class="field-group"><label>Jenis Kelamin</label><strong>{{ $patient->gender }}</strong></div>
            <div class="field-group"><label>Alamat</label><strong>{{ $patient->address }}</strong></div>
            <div class="field-group"><label>Nomor Telepon</label><strong>{{ $patient->phone }}</strong></div>
            <div class="field-group"><label>Kategori Penyakit</label><strong>{{ $patient->diseaseCategory->name }}</strong></div>
            <div class="field-group"><label>Tanggal Diagnosis</label><strong>{{ $patient->diagnosis_date->locale('id')->translatedFormat('d M Y') }}</strong></div>
            <div class="field-group full-width"><label>Catatan</label><p>{{ $patient->notes ?: 'Tidak ada catatan.' }}</p></div>
        </div>
    </section>
@endsection
