<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('pageTitle', 'SKANIDA CARE') · SKANIDA CARE</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-layout">
            <aside class="sidebar">
                <a class="brand-block" href="{{ route('dashboard') }}" style="text-decoration:none;color:inherit">
                    <div class="brand-icon">S</div>
                    <div>
                        <div class="brand-title">SKANIDA</div>
                        <div class="brand-subtitle">CARE ADMIN</div>
                    </div>
                </a>
                <nav class="menu-area" aria-label="Navigasi utama">
                    <a class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>▣</span><span>Dashboard</span></a>
                    <a class="menu-item {{ request()->routeIs('hospital.*') ? 'active' : '' }}" href="{{ route('hospital.edit') }}"><span>⌂</span><span>Informasi Rumah Sakit</span></a>
                    <a class="menu-item {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}"><span>👤</span><span>Data Pasien</span></a>
                    <a class="menu-item {{ request()->routeIs('diseases.*') ? 'active' : '' }}" href="{{ route('diseases.index') }}"><span>🩺</span><span>Kategori Penyakit</span></a>
                    <a class="menu-item {{ request()->routeIs('cohorts.*') ? 'active' : '' }}" href="{{ route('cohorts.index') }}"><span>📚</span><span>Direktori Kohort</span></a>
                    <a class="menu-item {{ request()->routeIs('statistics') ? 'active' : '' }}" href="{{ route('statistics') }}"><span>📊</span><span>Statistik</span></a>
                    <a class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><span>📝</span><span>Laporan</span></a>
                    <a class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}"><span>⚙</span><span>Pengaturan</span></a>
                </nav>
                <div class="sidebar-footer">
                    <div class="admin-pill">
                        <div class="avatar">{{ mb_substr(auth('admin')->user()->name, 0, 1) }}</div>
                        <div><strong>{{ auth('admin')->user()->name }}</strong><small>Admin</small></div>
                    </div>
                </div>
            </aside>
            <div class="main-panel">
                <header class="topbar">
                    <div>
                        <p class="eyebrow">Selamat datang</p>
                        <h1>@yield('pageTitle', 'Dashboard Rumah Sakit')</h1>
                    </div>
                    <div class="header-actions">
                        <a class="ghost-btn" href="{{ route('dashboard') }}">Lihat Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="danger-btn">Logout</button>
                        </form>
                    </div>
                </header>
                <main class="page-content">
                    @if (session('success'))
                        <div class="flash-message success" role="status">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="flash-message error" role="alert">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="flash-message error" role="alert">
                            <strong>Periksa kembali formulir:</strong>
                            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
