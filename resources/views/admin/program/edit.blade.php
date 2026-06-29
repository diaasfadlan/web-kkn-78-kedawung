@extends('layouts.admin')

@section('title', isset($program) ? 'Edit Program Kerja' : 'Tambah Program Kerja')

@section('content')
<section class="hero">
    <div class="container">
        <h1>{{ isset($program) ? 'Edit Program Kerja' : 'Tambah Program Kerja Baru' }}</h1>
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
                        <form action="{{ isset($program) ? route('program.update', $id) : route('program.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($program))
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label for="title" class="form-label">Nama Program *</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $program['title'] ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="planned" {{ old('status', $program['status'] ?? '') == 'planned' ? 'selected' : '' }}>Direncanakan (Planned)</option>
                                    <option value="ongoing" {{ old('status', $program['status'] ?? '') == 'ongoing' ? 'selected' : '' }}>Berjalan (Ongoing)</option>
                                    <option value="completed" {{ old('status', $program['status'] ?? '') == 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail Program</label>
                                @if($program['thumbnail_url'] ?? false)
                                    <img src="{{ $program['thumbnail_url'] }}" alt="Thumbnail {{ $program['title'] ?? 'program' }}" class="admin-program-thumbnail mb-3">
                                @endif
                                <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" accept="image/*">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti. Maksimal 2 MB.</small>
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai *</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $program['start_date'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">Tanggal Selesai *</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $program['end_date'] ?? '') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="objective" class="form-label">Tujuan Program *</label>
                                <input type="text" class="form-control" id="objective" name="objective" value="{{ old('objective', $program['objective'] ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi *</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $program['description'] ?? '') }}</textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('program.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">{{ isset($program) ? 'Update' : 'Simpan' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
