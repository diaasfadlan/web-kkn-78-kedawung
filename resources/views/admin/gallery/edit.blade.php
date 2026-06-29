@extends('layouts.admin')

@section('title', 'Edit Galeri')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Edit Foto Galeri</h1>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Periksa kembali data berikut:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card">
                    <div class="card-body p-4">
                        <form action="{{ route('gallery.update', $gallery['id']) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Foto *</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $gallery['title'] ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori *</label>
                                <select class="form-select" id="category" name="category" required>
                                    @foreach([
                                        'edukasi' => 'Edukasi',
                                        'sosialisasi' => 'Sosialisasi',
                                        'gotong-royong' => 'Gotong Royong',
                                        'dokumentasi' => 'Dokumentasi',
                                        'penutupan' => 'Penutupan',
                                    ] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category', $gallery['category'] ?? '') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gambar Saat Ini</label>
                                <div>
                                    @if($gallery['image_url'] ?? false)
                                        <img src="{{ $gallery['image_url'] }}" alt="{{ $gallery['title'] ?? 'Foto galeri' }}" class="img-thumbnail" style="max-height: 240px; object-fit: cover;">
                                    @else
                                        <p class="text-muted">Belum ada gambar.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Ganti Gambar</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti. Maksimal 5 MB.</small>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $gallery['description'] ?? '') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('gallery.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
