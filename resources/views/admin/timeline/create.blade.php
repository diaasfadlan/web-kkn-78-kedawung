@extends('layouts.admin')

@section('title', isset($timeline) ? 'Edit Timeline' : 'Tambah Timeline')

@section('content')
<section class="hero">
    <div class="container">
        <h1>{{ isset($timeline) ? 'Edit Kegiatan Timeline' : 'Tambah Kegiatan Timeline Baru' }}</h1>
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

                <div class="card">
                    <div class="card-body p-4">
                        <form action="{{ isset($timeline) ? route('timeline.update', $id) : route('timeline.store') }}" method="POST">
                            @csrf
                            @if(isset($timeline))
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label for="title" class="form-label">Nama Kegiatan *</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $timeline['title'] ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Tanggal Kegiatan *</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $timeline['date'] ?? '') }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="icon" class="form-label">FontAwesome Icon Class (Opsional)</label>
                                    <input type="text" class="form-control" id="icon" name="icon" placeholder="fa-circle" value="{{ old('icon', $timeline['icon'] ?? '') }}">
                                    <small class="text-muted">Contoh: fa-users, fa-book, fa-school</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="color" class="form-label">Warna Aksen (Hex Color)</label>
                                    <input type="color" class="form-control form-control-color w-100" id="color" name="color" value="{{ old('color', $timeline['color'] ?? '#3498db') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Kegiatan *</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $timeline['description'] ?? '') }}</textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('timeline.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">{{ isset($timeline) ? 'Update' : 'Simpan' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
