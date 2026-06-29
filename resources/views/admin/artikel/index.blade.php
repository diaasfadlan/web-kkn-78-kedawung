@extends('layouts.admin')

@section('title', 'Kelola Artikel')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Kelola Artikel</h1>
        <p class="lead">Manajemen artikel kegiatan KKN</p>
    </div>
</section>

<!-- Content -->
<section class="py-5">
    <div class="container">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <h3>Daftar Artikel</h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('artikel.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Artikel
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                    <tr>
                        <td>
                            <strong>{{ $article['title'] ?? 'Artikel' }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $article['category'] ?? 'Umum' }}</span>
                        </td>
                        <td>{{ $article['author'] ?? '-' }}</td>
                        <td>
                            {{ isset($article['published_at']) ? \Carbon\Carbon::parse($article['published_at'])->format('d M Y') : '-' }}
                        </td>
                        <td>
                            <a href="{{ route('artikel.edit', $article['id'] ?? '#') }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('artikel.destroy', $article['id'] ?? '#') }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            <p>Belum ada artikel</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
