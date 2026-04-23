@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0 h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('docs.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h4 class="mb-0 text-dark fw-bold">{{ $document->title }}</h4>
        </div>
        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-3 py-2">
            <i class="bi bi-cloud-check-fill me-1"></i> Tersimpan Otomatis di Cloud
        </span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="height: calc(100vh - 150px);">
        <iframe src="{{ $document->google_editor_url }}" 
                width="100%" 
                height="100%" 
                frameborder="0" 
                allowfullscreen>
        </iframe>
    </div>
</div>
@endsection