@extends('app')

@section('pageTitle', 'Informasi Rumah Sakit')

@section('content')
    <div class="page-header"><div><p class="eyebrow">Informasi institusi</p><h2>Informasi Rumah Sakit</h2></div></div>
    <section class="panel">
        <form method="POST" action="{{ route('hospital.update') }}" class="two-col-form">
            @csrf
            @method('PUT')
            <div class="field-group"><label for="hospital-name">Nama Rumah Sakit</label><input id="hospital-name" name="name" value="{{ old('name', $hospital->name) }}" required></div>
            <div class="field-group"><label for="hospital-phone">Nomor Telepon</label><input id="hospital-phone" name="phone" value="{{ old('phone', $hospital->phone) }}" required></div>
            <div class="field-group full-width"><label for="hospital-address">Alamat</label><input id="hospital-address" name="address" value="{{ old('address', $hospital->address) }}" required></div>
            <div class="field-group"><label for="hospital-email">Email</label><input id="hospital-email" name="email" type="email" value="{{ old('email', $hospital->email) }}" required></div>
            <div class="field-group full-width"><label for="hospital-description">Deskripsi</label><textarea id="hospital-description" name="description" rows="4" required>{{ old('description', $hospital->description) }}</textarea></div>
            <div class="field-group full-width"><label for="hospital-facilities">Fasilitas (pisahkan dengan koma)</label><textarea id="hospital-facilities" name="facilities" rows="3">{{ old('facilities', implode(', ', $hospital->facilities ?? [])) }}</textarea></div>
            <div class="form-actions full-width"><button type="submit" class="primary-btn">Simpan Perubahan</button></div>
        </form>
    </section>
@endsection
