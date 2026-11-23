@extends('app')

@section('pageTitle', 'Manajemen Data Pasien')

@section('content')
    <div class="page-header"><div><p class="eyebrow">Operasional pasien</p><h2>Data Pasien</h2></div><a class="primary-btn" href="{{ route('patients.create') }}">+ Tambah Pasien</a></div>
    <section class="panel filter-panel">
        <form method="GET" action="{{ route('patients.index') }}" class="filter-grid">
            <div class="field-group"><label for="patient-search">Pencarian</label><input id="patient-search" name="search" value="{{ request('search') }}" placeholder="Cari nama / nomor rekam medis"></div>
            <div class="field-group"><label for="patient-category">Kategori</label><select id="patient-category" name="category"><option value="">Semua kategori</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="field-group"><label for="patient-gender">Jenis Kelamin</label><select id="patient-gender" name="gender"><option value="">Semua</option><option value="Laki-laki" @selected(request('gender') === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected(request('gender') === 'Perempuan')>Perempuan</option></select></div>
            <div class="field-group"><label for="patient-date">Tanggal Diagnosis</label><input id="patient-date" type="date" name="diagnosis_date" value="{{ request('diagnosis_date') }}"></div>
            <div class="form-actions full-width"><button type="submit" class="primary-btn">Terapkan Filter</button><a class="ghost-btn" href="{{ route('patients.index') }}">Reset</a></div>
        </form>
    </section>
    <section class="panel table-panel">
        <div class="table-wrap"><table>
            <thead><tr><th>Nama</th><th>No. Rekam Medis</th><th>Jenis Kelamin</th><th>Kategori</th><th>Tanggal Diagnosis</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr>
                        <td>{{ $patient->name }}</td><td>{{ $patient->medical_record_number }}</td><td>{{ $patient->gender }}</td><td>{{ $patient->diseaseCategory->name }}</td><td>{{ $patient->diagnosis_date->locale('id')->translatedFormat('d M Y') }}</td>
                        <td class="action-col"><a class="table-btn info" href="{{ route('patients.show', $patient) }}">Detail</a><a class="table-btn warning" href="{{ route('patients.edit', $patient) }}">Edit</a><form method="POST" action="{{ route('patients.destroy', $patient) }}" data-confirm="Hapus data pasien ini? Tindakan ini tidak dapat dibatalkan.">@csrf @method('DELETE')<button class="table-btn danger" type="submit">Hapus</button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state">Belum ada data pasien yang sesuai dengan filter.</div></td></tr>
                @endforelse
            </tbody>
        </table></div>
        <div class="pagination-row"><span>Menampilkan {{ $patients->firstItem() ?? 0 }}–{{ $patients->lastItem() ?? 0 }} dari {{ $patients->total() }} data</span>{{ $patients->links() }}</div>
    </section>
@endsection
