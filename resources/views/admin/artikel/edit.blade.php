@extends('layouts.admin')

@section('title', 'Edit Artikel')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Edit Artikel</h1>
    </div>
</section>

<!-- Form -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
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

                <div class="card">
                    <div class="card-body p-4">
                        <form action="{{ route('artikel.update', $id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Artikel *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" 
                                       value="{{ old('title', $article['title'] ?? '') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Kategori *</label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="edukasi" {{ old('category', $article['category'] ?? '') == 'edukasi' ? 'selected' : '' }}>Edukasi</option>
                                        <option value="sosialisasi" {{ old('category', $article['category'] ?? '') == 'sosialisasi' ? 'selected' : '' }}>Sosialisasi</option>
                                        <option value="gotong-royong" {{ old('category', $article['category'] ?? '') == 'gotong-royong' ? 'selected' : '' }}>Gotong Royong</option>
                                        <option value="dokumentasi" {{ old('category', $article['category'] ?? '') == 'dokumentasi' ? 'selected' : '' }}>Dokumentasi</option>
                                        <option value="acara" {{ old('category', $article['category'] ?? '') == 'acara' ? 'selected' : '' }}>Acara Khusus</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="author" class="form-label">Penulis *</label>
                                    <input type="text" class="form-control @error('author') is-invalid @enderror" 
                                           id="author" name="author" 
                                           value="{{ old('author', $article['author'] ?? '') }}" required>
                                    @error('author')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail (Opsional)</label>
                                <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                       id="thumbnail" name="thumbnail" accept="image/*">
                                <small class="text-muted">Max 2MB. Format: JPG, PNG, GIF</small>
                                @error('thumbnail')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Isi Artikel *</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="8" required>{{ old('content', $article['content'] ?? '') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('artikel.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Artikel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
