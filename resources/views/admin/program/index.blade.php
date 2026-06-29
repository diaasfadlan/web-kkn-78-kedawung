@extends('layouts.admin')

@section('title', 'Kelola Program Kerja')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Kelola Program Kerja</h1>
        <p class="lead">Manajemen Program Kerja KKN</p>
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
                <h3>Daftar Program Kerja</h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('program.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Program
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nama Program</th>
                        <th>Status</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $program)
                    <tr>
                        <td><strong>{{ $program['title'] ?? '-' }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $program['status'] == 'completed' ? 'success' : ($program['status'] == 'ongoing' ? 'warning' : 'info') }}">
                                {{ ucfirst($program['status'] ?? 'planned') }}
                            </span>
                        </td>
                        <td>{{ $program['start_date'] ?? '-' }}</td>
                        <td>{{ $program['end_date'] ?? '-' }}</td>
                        <td>
                            <a href="{{ route('program.edit', $program['id'] ?? '#') }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('program.destroy', $program['id'] ?? '#') }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus program ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada program kerja</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
