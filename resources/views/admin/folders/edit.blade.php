@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Folder: {{ $folder->name }}</h1>
        <a href="{{ route('admin.folders.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.folders.update', $folder->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Folder</label>
                    <input type="text" name="name" class="form-control" value="{{ $folder->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pindahkan ke Bidang/Departemen</label>
                    <select name="department_id" class="form-select" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $folder->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ubah Folder Induk (Parent)</label>
                    <select name="parent_id" class="form-select">
                        <option value="">Folder Utama (Root)</option>
                        @foreach($allFolders as $f)
                            <option value="{{ $f->id }}" {{ $folder->parent_id == $f->id ? 'selected' : '' }}>
                                {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection