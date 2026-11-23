@extends('app')

@section('pageTitle', $patient->exists ? 'Edit Pasien' : 'Tambah Pasien')

@section('content')
    <div class="page-header"><div><p class="eyebrow">Data pasien</p><h2>{{ $patient->exists ? 'Edit Pasien' : 'Tambah Pasien Baru' }}</h2></div><a class="ghost-btn" href="{{ route('patients.index') }}">Kembali</a></div>
    <section class="panel">
        <form method="POST" action="{{ $patient->exists ? route('patients.update', $patient) : route('patients.store') }}" class="two-col-form">
            @csrf
            @if ($patient->exists) @method('PUT') @endif
            <div class="field-group"><label for="patient-name">Nama Lengkap</label><input id="patient-name" name="name" value="{{ old('name', $patient->name) }}" required></div>
            <div class="field-group"><label for="medical-record-number">Nomor Rekam Medis</label><input id="medical-record-number" name="medical_record_number" value="{{ $patient->medical_record_number }}" readonly><small class="field-hint">Nomor rekam medis dibuat otomatis oleh sistem.</small></div>
            <div class="field-group"><label for="date-of-birth">Tanggal Lahir</label><input id="date-of-birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" max="{{ today()->toDateString() }}" required></div>
            <div class="field-group"><label for="patient-gender">Jenis Kelamin</label><select id="patient-gender" name="gender" required><option value="">Pilih jenis kelamin</option><option value="Laki-laki" @selected(old('gender', $patient->gender) === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected(old('gender', $patient->gender) === 'Perempuan')>Perempuan</option></select></div>
            <div class="field-group"><label for="patient-address">Alamat</label><input id="patient-address" name="address" value="{{ old('address', $patient->address) }}" required></div>
            <div class="field-group"><label for="patient-phone">Nomor Telepon</label><input id="patient-phone" name="phone" value="{{ old('phone', $patient->phone) }}" required></div>
            <div class="field-group"><label for="disease-category">Kategori Penyakit</label><select id="disease-category" name="disease_category_id" required><option value="">Pilih kategori</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('disease_category_id', $patient->disease_category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="field-group"><label for="diagnosis-date">Tanggal Diagnosis</label><input id="diagnosis-date" type="date" name="diagnosis_date" value="{{ old('diagnosis_date', $patient->diagnosis_date?->format('Y-m-d')) }}" max="{{ today()->toDateString() }}" required></div>
            <div class="field-group full-width"><label for="patient-notes">Catatan</label><textarea id="patient-notes" name="notes" rows="4">{{ old('notes', $patient->notes) }}</textarea></div>
            <div class="form-actions full-width"><button type="submit" class="primary-btn">{{ $patient->exists ? 'Simpan Perubahan' : 'Tambah Pasien' }}</button><a class="ghost-btn" href="{{ route('patients.index') }}">Batal</a></div>
        </form>
    </section>
@endsection
