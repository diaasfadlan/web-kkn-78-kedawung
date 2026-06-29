@extends('layouts.admin')

@section('title', 'Kelola Anggota')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Kelola Anggota Kelompok</h1>
        <p class="lead">Manajemen Profil Anggota KKN</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <h3>Daftar Anggota</h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('group.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Anggota
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Foto</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Program Studi</th>
                        <th>Jabatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <td>
                            @if($member['photo_url'] ?? false)
                                <img src="{{ $member['photo_url'] }}" alt="{{ $member['name'] ?? 'Anggota' }}" class="admin-member-avatar" loading="lazy">
                            @else
                                <span class="admin-member-avatar avatar-placeholder"><i class="fa-solid fa-user"></i></span>
                            @endif
                        </td>
                        <td>{{ $member['nim'] ?? '-' }}</td>
                        <td><strong>{{ $member['name'] ?? '-' }}</strong></td>
                        <td>{{ $member['prodi'] ?? '-' }}</td>
                        <td>{{ $member['position'] ?? '-' }}</td>
                        <td>
                            <a href="{{ route('group.edit', $member['id'] ?? '#') }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('group.destroy', $member['id'] ?? '#') }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus anggota ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada anggota terdaftar</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
