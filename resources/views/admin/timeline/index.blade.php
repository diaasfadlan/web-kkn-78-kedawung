@extends('layouts.admin')

@section('title', 'Kelola Timeline')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Kelola Timeline</h1>
        <p class="lead">Manajemen Linimasa Kegiatan KKN</p>
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
                <h3>Daftar Rencana / Kegiatan Timeline</h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('timeline.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kegiatan
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Judul Kegiatan</th>
                        <th>Icon</th>
                        <th>Warna</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($timelines as $timeline)
                    <tr>
                        <td>{{ $timeline['date'] ?? '-' }}</td>
                        <td><strong>{{ $timeline['title'] ?? '-' }}</strong></td>
                        <td><i class="fas {{ $timeline['icon'] ?? 'fa-circle' }}"></i></td>
                        <td>
                            <span class="badge" style="background-color: {{ $timeline['color'] ?? '#3498db' }}">
                                {{ $timeline['color'] ?? '#3498db' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('timeline.edit', $timeline['id'] ?? '#') }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('timeline.destroy', $timeline['id'] ?? '#') }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kegiatan ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada timeline kegiatan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
