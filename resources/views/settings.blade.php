@extends('app')

@section('pageTitle', 'Pengaturan Akun Admin')

@section('content')
    <div class="page-header"><div><p class="eyebrow">Akun administrator</p><h2>Pengaturan Akun Admin</h2></div></div>
    <div class="content-grid two-col">
        <section class="panel">
            <div class="panel-heading"><h3>Profil Admin</h3></div>
            <form method="POST" action="{{ route('settings.profile') }}" class="two-col-form">
                @csrf @method('PUT')
                <div class="field-group"><label for="admin-name">Nama Lengkap</label><input id="admin-name" name="name" value="{{ old('name', $admin->name) }}" required></div>
                <div class="field-group"><label for="admin-email">Email</label><input id="admin-email" name="email" type="email" value="{{ old('email', $admin->email) }}" required></div>
                <div class="field-group full-width"><label for="admin-phone">Nomor Telepon</label><input id="admin-phone" name="phone" value="{{ old('phone', $admin->phone) }}"></div>
                <div class="form-actions full-width"><button class="primary-btn" type="submit">Simpan Profil</button></div>
            </form>
        </section>
        <section class="panel">
            <div class="panel-heading"><h3>Ubah Password</h3></div>
            <form method="POST" action="{{ route('settings.password') }}" class="two-col-form">
                @csrf @method('PUT')
                <div class="field-group full-width"><label for="current-password">Password Lama</label><input id="current-password" type="password" name="current_password" autocomplete="current-password" required></div>
                <div class="field-group full-width"><label for="new-password">Password Baru</label><input id="new-password" type="password" name="password" autocomplete="new-password" minlength="8" required></div>
                <div class="field-group full-width"><label for="confirm-password">Konfirmasi Password Baru</label><input id="confirm-password" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></div>
                <div class="form-actions full-width"><button class="primary-btn" type="submit">Ubah Password</button></div>
            </form>
        </section>
    </div>
@endsection
