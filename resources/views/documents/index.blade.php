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

        <div class="card border-0 shadow-sm rounded-4 mb-4 stagger-2">
            <div class="card-body p-3">
                <form action="{{ route('docs.index') }}" method="GET" class="row g-2">
                    <div class="col-md-4">
                        <select name="folder_id" class="form-select border-0 bg-light">
                            <option value="">{{ __('Semua Folder') }}</option>
                            @foreach($folders as $f)
                                <option value="{{ $f->id }}" {{ request('folder_id') == $f->id ? 'selected' : '' }}>
                                    [{{ $f->department->name ?? 'Umum' }}] {{ $f->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="year" class="form-select border-0 bg-light">
                            <option value="">{{ __('Semua Tahun') }}</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">{{ __('Filter') }}</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('docs.index') }}" class="btn btn-outline-secondary w-100">{{ __('Reset') }}</a>
                    </div>
                </form>
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
                                <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide" style="width: 35%;">{{ __('File Name') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Folder') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Owner') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Size') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Uploaded') }}</th>
                                <th class="text-end pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                                @php
                                    $ext = strtolower($doc->extension);
                                    $icon = 'bi-file-earmark-text-fill'; $colorClass = 'bg-primary text-primary';
                                    if ($ext == 'pdf') { $icon = 'bi-file-earmark-pdf-fill'; $colorClass = 'bg-danger text-danger'; }
                                    elseif (in_array($ext, ['jpg', 'png', 'jpeg'])) { $icon = 'bi-file-earmark-image-fill'; $colorClass = 'bg-success text-success'; }
                                    elseif (in_array($ext, ['zip', 'rar'])) { $icon = 'bi-file-earmark-zip-fill'; $colorClass = 'bg-warning text-warning'; }
                                    elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) { $icon = 'bi-file-earmark-spreadsheet-fill'; $colorClass = 'bg-success text-success'; }
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
                                        <span class="badge bg-light text-dark border fw-normal">
                                            <i class="bi bi-folder2 me-1"></i> {{ $doc->folder->name ?? 'Unsorted' }}
                                        </span>
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
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if($doc->google_file_id)
        <a href="{{ route('docs.editor', $doc->id) }}" class="btn btn-sm btn-icon btn-primary text-white hover-elevate shadow-sm" data-bs-toggle="tooltip" title="Buka di Editor (Live)">
            <i class="bi bi-pencil-square"></i> Live Edit
        </a>
    @else
        <a href="{{ route('docs.download', $doc->id) }}" class="btn btn-sm btn-icon btn-light text-primary hover-elevate" data-bs-toggle="tooltip" title="{{ __('Download File') }}">
            <i class="bi bi-cloud-arrow-down-fill"></i>
        </a>
    @endif

                                            @if ($doc->owner_id == auth()->id())
                                                <button class="btn btn-sm btn-icon btn-light text-info hover-elevate" data-bs-toggle="modal" data-bs-target="#shareModal{{ $doc->id }}" title="{{ __('Share Access') }}">
                                                    <i class="bi bi-share-fill"></i>
                                                </button>
                                            @endif

                                            @if ($doc->owner_id == auth()->id() || auth()->user()->role_level == 'admin')
                                                <a href="{{ route('docs.edit', $doc->id) }}" class="btn btn-sm btn-icon btn-light text-secondary hover-primary hover-elevate" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('docs.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-light text-danger hover-elevate" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-5">Empty</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Document Title') }}</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-fonts"></i></span>
                            <input type="text" name="title" class="form-control md-input" placeholder="e.g. Laporan Bidang 4" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Target Folder') }}</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-folder-symlink"></i></span>
                            <select name="folder_id" class="form-select md-input" required>
                                <option value="" disabled selected>{{ __('Pilih Folder Tujuan...') }}</option>
                                @foreach($folders as $f)
                                    <option value="{{ $f->id }}">[{{ $f->department->name ?? 'Umum' }}] {{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Select File') }}</label>
                        <input type="file" name="file" class="form-control md-file-input" required>
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
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        
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
                            @if($doc->permissions->count() > 0)
                                <div class="mb-4 p-3 bg-light rounded-3 border">
                                    <label class="form-label small fw-bold text-secondary mb-2 text-uppercase tracking-wide"><i class="bi bi-info-circle me-1"></i> {{ __('Currently Shared With') }}</label>
                                    <ul class="list-group list-group-flush mb-0">
                                        @foreach($doc->permissions as $perm)
                                            @php
                                                $shareName = '';
                                                $iconStr = '';
                                                if($perm->user_id) {
                                                    $u = $users->where('id', $perm->user_id)->first();
                                                    $shareName = $u ? $u->full_name : 'User ID: '.$perm->user_id;
                                                    $iconStr = '<i class="bi bi-person-fill text-primary me-2"></i>';
                                                } elseif($perm->department_id) {
                                                    $d = $departments->where('id', $perm->department_id)->first();
                                                    $shareName = $d ? $d->name : 'Dept ID: '.$perm->department_id;
                                                    $iconStr = '<i class="bi bi-people-fill text-success me-2"></i>';
                                                }
                                            @endphp
                                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-2 py-2 border-bottom border-light">
                                                <div>
                                                    {!! $iconStr !!} <span class="fw-medium text-dark small">{{ $shareName }}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">
                                                        {{ $perm->access_level == 'write' ? __('Editor') : __('Viewer') }}
                                                    </span>
                                                    <form action="{{ route('docs.unshare', $perm->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('{{ __('Cabut akses dari pengguna/grup ini?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 m-0 border-0 text-decoration-none shadow-none" title="{{ __('Cabut Akses') }}">
                                                            <i class="bi bi-x-circle-fill fs-6"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <hr class="text-light mb-4">
                            @endif

                            <form action="{{ route('docs.share', $doc->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Add New Share') }} / {{ __('Share Type') }}</label>
                                    <select name="share_type" class="form-select md-input" onchange="toggleShareUI(this.value, {{ $doc->id }})">
                                        <option value="user">{{ __('Individual User') }}</option>
                                        <option value="department">{{ __('Whole Department (Group)') }}</option>
                                    </select>
                                </div>

                                <div id="user_field_{{ $doc->id }}" class="mb-4">
                                    <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Select Colleague') }}</label>
                                    <select name="user_id" class="form-select md-input">
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="dept_field_{{ $doc->id }}" class="mb-4" style="display:none;">
                                    <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Select Department') }}</label>
                                    <select name="department_id" class="form-select md-input">
                                        @foreach ($departments as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Access Privilege') }}</label>
                                    <select name="access_level" class="form-select md-input">
                                        <option value="read">{{ __('Viewer') }}</option>
                                        <option value="write">{{ __('Editor') }}</option>
                                    </select>
                                </div>
                        </div> <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                            <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-info text-white md-btn px-5"><i class="bi bi-send me-2"></i> {{ __('Grant Access') }}</button>
                        </div>
                        </form> </div>
                </div>
            </div>
        @endif
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });

        function toggleShareUI(val, id) {
            if(val === 'user') {
                document.getElementById('user_field_' + id).style.display = 'block';
                document.getElementById('dept_field_' + id).style.display = 'none';
            } else {
                document.getElementById('user_field_' + id).style.display = 'none';
                document.getElementById('dept_field_' + id).style.display = 'block';
            }
        }
    </script>

    <style>
        .input-group-custom { position: relative; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); z-index: 10; color: #5f6368; }
        .md-input { padding-left: 40px !important; border-radius: 10px; border: 1px solid #dadce0; }
        .md-file-input { border-radius: 10px; border: 2px dashed #dadce0; padding: 10px; background: #f8f9fa; }
        .file-icon-box { width: 48px; height: 48px; }
    </style>
@endsection