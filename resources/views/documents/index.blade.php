@extends('layouts.admin')

@section('content')
    <div class="dashboard-container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 stagger-1">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-folder2-open fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ __('Document Center') }}</h2>
                    <p class="text-secondary small mb-0">{{ __('Securely manage, store, and share enterprise files') }}</p>
                </div>
            </div>
            <div>
                <button class="btn btn-primary md-btn d-flex align-items-center px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-cloud-arrow-up-fill me-2 fs-5"></i> <span class="fw-semibold">{{ __('Upload File') }}</span>
                </button>
            </div>
        </div>

        @if (auth()->user()->role_level === 'admin')
            <div class="alert bg-dark text-white border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4 stagger-2" role="alert">
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                    <i class="bi bi-shield-lock-fill fs-5"></i>
                </div>
                <div>
                    <strong class="tracking-wide text-uppercase small">{{ __('Administrator Privilege') }}</strong>
                    <div class="small opacity-75">{{ __('Global override active. You have full visibility and control over all departmental documents.') }}</div>
                </div>
            </div>
        @endif

        <div class="card md-card border-0 stagger-3 h-100">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="border-bottom border-light bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide" style="width: 40%;">{{ __('File Name') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Owner') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Size') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Uploaded') }}</th>
                                <th class="text-end pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                                @php
                                    // Smart Icon & Color Logic
                                    $ext = strtolower($doc->extension);
                                    $icon = 'bi-file-earmark-text-fill';
                                    $colorClass = 'bg-primary text-primary';
                                    
                                    if ($ext == 'pdf') {
                                        $icon = 'bi-file-earmark-pdf-fill';
                                        $colorClass = 'bg-danger text-danger';
                                    } elseif (in_array($ext, ['jpg', 'png', 'jpeg', 'svg', 'gif'])) {
                                        $icon = 'bi-file-earmark-image-fill';
                                        $colorClass = 'bg-success text-success';
                                    } elseif (in_array($ext, ['zip', 'rar', 'tar', 'gz'])) {
                                        $icon = 'bi-file-earmark-zip-fill';
                                        $colorClass = 'bg-warning text-warning';
                                    } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
                                        $icon = 'bi-file-earmark-spreadsheet-fill';
                                        $colorClass = 'bg-success text-success';
                                    }
                                @endphp
                                <tr class="table-row-hover">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon-box {{ $colorClass }} bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                                                <i class="{{ $icon }} fs-4"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-semibold text-dark text-truncate" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                                <div class="d-flex align-items-center mt-1">
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-0 text-uppercase" style="font-size: 0.65rem;">{{ $ext }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle-sm bg-secondary bg-opacity-10 text-secondary fw-bold rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 11px;">
                                                {{ strtoupper(substr($doc->owner->full_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="text-dark small fw-medium">{{ $doc->owner->full_name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-secondary font-monospace small">
                                        {{ number_format($doc->file_size / 1024, 1) }} KB
                                    </td>
                                    <td class="py-3 text-secondary small">
                                        <div class="fw-medium text-dark">{{ $doc->created_at->format('d M Y') }}</div>
                                        <div class="opacity-75" style="font-size: 0.75rem;">{{ $doc->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('docs.download', $doc->id) }}" class="btn btn-sm btn-icon btn-light text-primary hover-elevate" data-bs-toggle="tooltip" title="{{ __('Download File') }}">
                                                <i class="bi bi-cloud-arrow-down-fill"></i>
                                            </a>

                                            @if ($doc->owner_id == auth()->id())
                                                <button class="btn btn-sm btn-icon btn-light text-info hover-elevate" data-bs-toggle="modal" data-bs-target="#shareModal{{ $doc->id }}" title="{{ __('Share Access') }}">
                                                    <i class="bi bi-share-fill"></i>
                                                </button>
                                            @endif

                                            @if ($doc->owner_id == auth()->id() || auth()->user()->role_level == 'admin')
                                                <a href="{{ route('docs.edit', $doc->id) }}" class="btn btn-sm btn-icon btn-light text-secondary hover-primary hover-elevate" data-bs-toggle="tooltip" title="{{ __('Edit Properties') }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('docs.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this document?') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-light text-danger hover-elevate" data-bs-toggle="tooltip" title="{{ __('Delete Document') }}">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted d-flex flex-column align-items-center justify-content-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                <i class="bi bi-folder-x fs-1 text-secondary opacity-50"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark">{{ __('Workspace is Empty') }}</h6>
                                            <p class="small mb-0">{{ __('There are no documents uploaded in this directory yet.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($documents->hasPages())
                <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
                    <div class="pagination-custom d-flex justify-content-end">
                        {{ $documents->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('docs.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-cloud-upload-fill"></i>
                        </div>
                        {{ __('Upload Document') }}
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body px-4 py-4">
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Document Title') }}</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-fonts"></i></span>
                            <input type="text" name="title" class="form-control md-input" placeholder="e.g. Q3 Financial Report 2026" required autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Select File') }}</label>
                        <div class="position-relative">
                            <input type="file" name="file" class="form-control md-file-input" required>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-muted small mt-2">
                        <i class="bi bi-shield-check text-success me-1"></i> {{ __('Encrypted transfer. Max 50MB (PDF, DOCX, XLSX, Images).') }}
                    </div>
                </div>
                
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary md-btn px-5"><i class="bi bi-upload me-2"></i> {{ __('Start Upload') }}</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($documents as $doc)
        @if ($doc->owner_id == auth()->id())
            <div class="modal fade" id="shareModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('docs.share', $doc->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                        @csrf
                        <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                            <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-lines-fill"></i>
                                </div>
                                {{ __('Share Access') }}
                            </h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body px-4 py-4">
                            <div class="bg-light border rounded-3 p-3 mb-4 d-flex align-items-center">
                                <i class="bi bi-file-earmark-text text-secondary fs-4 me-3"></i>
                                <div class="overflow-hidden">
                                    <div class="fw-semibold text-dark text-truncate">{{ $doc->title }}</div>
                                    <div class="small text-muted text-uppercase">{{ strtolower($doc->extension) }} file</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Select Colleague') }}</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-search"></i></span>
                                    <select name="user_id" class="form-select md-input select2" required>
                                        <option value="" disabled selected>{{ __('Search by name or username...') }}</option>
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->full_name }} (@{{ $u->username }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Access Privilege') }}</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                    <select name="access_level" class="form-select md-input">
                                        <option value="read">{{ __('Viewer (Download Only)') }}</option>
                                        <option value="write">{{ __('Editor (Can Modify Metadata)') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                            <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-info text-white md-btn px-5"><i class="bi bi-send me-2"></i> {{ __('Grant Access') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
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