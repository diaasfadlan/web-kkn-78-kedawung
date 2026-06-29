@extends('layouts.admin')

@section('title', 'Tambah Galeri')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Tambah Foto Galeri Baru</h1>
    </div>
</section>

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
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card">
                    <div class="card-body p-4">
                        <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Foto *</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="edukasi">Edukasi</option>
                                    <option value="sosialisasi">Sosialisasi</option>
                                    <option value="gotong-royong">Gotong Royong</option>
                                    <option value="dokumentasi">Dokumentasi</option>
                                    <option value="penutupan">Penutupan</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Pilih Gambar *</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                <small class="text-muted">Max 5MB. Format: JPG, PNG, GIF</small>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('gallery.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
