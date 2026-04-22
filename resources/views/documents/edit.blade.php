@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-0">
        <div class="mb-4 stagger-1">
            <a href="{{ route('docs.index') }}" class="text-decoration-none text-secondary hover-primary d-inline-flex align-items-center fw-medium">
                <i class="bi bi-arrow-left me-2"></i> {{ __('Back to Documents') }}
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 stagger-2">
                <div class="card md-card border-0 overflow-hidden">
                    <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 4px;"></div>
                    
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 px-md-5 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-pencil-square fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">{{ __('Edit Document Properties') }}</h4>
                            <p class="text-secondary small font-monospace mb-0 mt-1">{{ $document->title }}</p>
                        </div>
                    </div>

                    <div class="card-body px-4 px-md-5 pb-5 pt-3">
                        <form action="{{ route('docs.update', $document->id) }}" method="POST">
                            @csrf @method('PUT')

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Document Title') }}</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-type"></i></span>
                                    <input type="text" name="title" class="form-control md-input" value="{{ $document->title }}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Description / Notes') }}</label>
                                <textarea name="description" class="form-control md-input-textarea" rows="4" placeholder="{{ __('Add some context about this file...') }}">{{ $document->description }}</textarea>
                            </div>
                            <div class="mb-4">
    <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Target Folder / Division') }}</label>
    <div class="input-group-custom">
        <span class="input-icon"><i class="bi bi-folder2-open"></i></span>
        <select name="folder_id" class="form-select md-input" required>
            @foreach($folders as $f)
                <option value="{{ $f->id }}" {{ $document->folder_id == $f->id ? 'selected' : '' }}>
                    [{{ $f->department->name ?? 'Umum' }}] {{ $f->name }}
                </option>
            @endforeach
        </select>
    </div>
    <small class="text-muted">{{ __('Pindahkan dokumen ini ke folder bidang lain jika diperlukan.') }}</small>
</div>

                            <div class="p-3 bg-info bg-opacity-10 border border-info-subtle rounded-3 mb-4 d-flex align-items-start">
                                <i class="bi bi-info-circle-fill text-info mt-1 me-3 fs-5"></i>
                                <div class="small text-dark fw-medium lh-base">
                                    {{ __('You are modifying the metadata (name and description) only. To update the actual file content, please delete this entry and upload the new version.') }}
                                </div>
                            </div>

                            <hr class="my-4 text-light">
                            
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('docs.index') }}" class="btn btn-light fw-medium px-4">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn btn-primary md-btn px-5 d-flex align-items-center">
                                    <i class="bi bi-check2-circle me-2"></i> {{ __('Save Changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Component: Textarea */
.md-input-textarea {
    border-radius: 10px;
    border: 1px solid #dadce0;
    font-size: 14px;
    color: #202124;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background-color: #fff;
    padding: 12px 16px;
    resize: vertical;
}
.md-input-textarea:focus {
    border-color: var(--google-blue);
    box-shadow: 0 0 0 4px var(--google-blue-focus);
    outline: none;
}

/* Component: File Input (Google Style) */
.md-file-input {
    border-radius: 10px;
    border: 2px dashed #dadce0;
    padding: 10px 14px;
    background-color: #f8f9fa;
    color: #5f6368;
    transition: all 0.2s ease;
    cursor: pointer;
}
.md-file-input:hover {
    background-color: #f1f3f4;
    border-color: #bdc1c6;
}
.md-file-input:focus {
    border-color: var(--google-blue);
    background-color: #e8f0fe;
    box-shadow: 0 0 0 4px var(--google-blue-focus);
    outline: none;
}
.md-file-input::file-selector-button {
    background-color: #fff;
    border: 1px solid #dadce0;
    border-radius: 6px;
    padding: 6px 12px;
    color: #1a73e8;
    font-weight: 600;
    margin-right: 16px;
    transition: all 0.2s ease;
    cursor: pointer;
}
.md-file-input::file-selector-button:hover {
    background-color: #f8f9fa;
}

/* Box Ikon Dokumen */
.file-icon-box {
    width: 48px;
    height: 48px;
}
    </style>
@endsection