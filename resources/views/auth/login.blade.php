<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login · SKANIDA CARE</title>
        @vite('resources/css/app.css')
    </head>
    <body>
        <main class="login-shell">
            <section class="login-panel">
                <div class="login-brand">
                    <div class="brand-icon large">S</div>
                    <div><div class="brand-title">SKANIDA CARE</div><div class="brand-subtitle">Hospital Administration</div></div>
                </div>
                <h1>Selamat Datang</h1>
                <p class="login-copy">Masuk untuk mengelola data pasien, kategori penyakit, dan laporan klinis.</p>
                @if (session('success'))<div class="flash-message success" role="status">{{ session('success') }}</div>@endif
                @if ($errors->any())
                    <div class="flash-message error" role="alert">
                        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('login.store') }}" class="login-form">
                    @csrf
                    <div class="field-group">
                        <label for="login-email">Email</label>
                        <input id="login-email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                    </div>
                    <div class="field-group">
                        <label for="login-password">Password</label>
                        <input id="login-password" name="password" type="password" autocomplete="current-password" required>
                    </div>
                    <label class="remember-option"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                    <button type="submit" class="primary-btn full-width">Masuk ke Dashboard</button>
                </form>
            </section>
        </main>
    </body>
</html>
