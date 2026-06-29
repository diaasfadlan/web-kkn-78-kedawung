@extends('layouts.admin')

@section('title', 'Kelola Galeri')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Kelola Galeri</h1>
        <p class="lead">Manajemen Foto Kegiatan KKN</p>
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
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <h3>Daftar Foto Galeri</h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('gallery.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Foto
                </a>
            </div>
        </div>

        <div class="row g-4">
            @forelse($galleries as $gallery)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    @if($gallery['image_url'] ?? false)
                    <img src="{{ $gallery['image_url'] }}" class="card-img-top" alt="{{ $gallery['title'] }}" style="height: 200px; object-fit: cover;" loading="lazy" decoding="async">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $gallery['title'] ?? '-' }}</h5>
                        <p class="card-text text-muted">{{ $gallery['description'] ?? '-' }}</p>
                        <span class="badge bg-secondary">{{ ucfirst($gallery['category'] ?? '-') }}</span>
                    </div>
                    <div class="card-footer bg-white d-flex gap-2">
                        <a href="{{ route('gallery.edit', $gallery['id']) }}" class="btn btn-sm btn-warning flex-fill">
                            <i class="fas fa-pen"></i> Edit
                        </a>
                        <form action="{{ route('gallery.destroy', $gallery['id'] ?? '#') }}" method="POST" class="flex-fill">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Hapus foto ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted">Belum ada foto galeri</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
