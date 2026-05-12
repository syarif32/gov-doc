@extends('layouts.admin')

@section('content')

{{-- MESIN PENGURUTAN HIERARKI FOLDER (Ditambahkan di awal agar bisa diakses semua bagian) --}}
@php
    if (!function_exists('buildFolderTree')) {
        function buildFolderTree($folders, $parentId = null, $depth = 0) {
            $result = [];
            $children = $folders->where('parent_id', $parentId)->sortBy('name');
            foreach ($children as $f) {
                $f->depth = $depth; 
                $result[] = $f;
                $result = array_merge($result, buildFolderTree($folders, $f->id, $depth + 1));
            }
            return $result;
        }
    }
    // Susun semua folder menjadi struktur pohon
    $allSortedFolders = buildFolderTree($folders);
@endphp

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
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
            
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary d-flex align-items-center px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createBlankModal">
                    <i class="bi bi-file-earmark-plus me-2 fs-5"></i> <span class="fw-semibold">{{ __('Buat Dokumen') }}</span>
                </button>
                
                <button class="btn btn-primary d-flex align-items-center px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-cloud-arrow-up-fill me-2 fs-5"></i> <span class="fw-semibold">{{ __('Upload File') }}</span>
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4 stagger-2">
            <div class="card-body p-3">
                <form action="{{ route('docs.index') }}" method="GET" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control border-0 bg-light" placeholder="{{ __('Cari nama dokumen...') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tanggal" class="form-control border-0 bg-light" value="{{ request('tanggal') }}" title="{{ __('Pilih Tanggal Upload') }}">
                    </div>
                    <div class="col-md-3 position-relative">
                        <!-- Hidden input untuk menampung ID folder yang dipilih -->
                        <input type="hidden" name="folder_id" id="filterFolderId" value="{{ request('folder_id') }}">
                        
                        <!-- Tombol Dropdown Pengganti Select -->
                        <button class="form-select border-0 bg-light text-start d-flex justify-content-between align-items-center" type="button" id="filterFolderBtn" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px;">
                            <span id="filterFolderText" class="text-truncate" style="max-width: 90%;">
                                @php
                                    $selectedName = __('Semua Kategori/Folder');
                                    if(request('folder_id')) {
                                        $selectedFolder = $folders->firstWhere('id', request('folder_id'));
                                        if($selectedFolder) {
                                            $selectedName = '[' . ($selectedFolder->department->name ?? 'Umum') . '] ' . $selectedFolder->name;
                                        }
                                    }
                                @endphp
                                {{ $selectedName }}
                            </span>
                        </button>
                        
                        <!-- Isi Dropdown dengan Live Search & Visual Akar -->
                        <div class="dropdown-menu w-100 p-2 shadow-lg border-0 rounded-4" aria-labelledby="filterFolderBtn" style="max-height: 350px; overflow-y: auto; z-index: 1050;">
                            
                            <!-- Input Search (Menempel di atas saat di-scroll) -->
                            <div class="position-sticky top-0 bg-white pb-2" style="z-index: 10;">
                                <div class="position-relative">
                                    <span class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);"><i class="bi bi-search" style="font-size: 0.8rem; color: #6c757d;"></i></span>
                                    <input type="text" class="form-control form-control-sm bg-light border-0" id="filterFolderSearch" placeholder="Cari folder atau divisi..." style="padding-left: 30px; border-radius: 8px;" autocomplete="off">
                                </div>
                            </div>
                            
                            <div id="filterFolderList">
                                <!-- Opsi Default: Semua Folder -->
                                <a class="dropdown-item rounded-3 py-2 filter-folder-opt {{ request('folder_id') == '' ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-dark' }} d-flex align-items-center" href="#" data-id="" data-text="{{ __('Semua Kategori/Folder') }}">
                                    <i class="bi bi-grid-fill me-2 opacity-50"></i> {{ __('Semua Kategori/Folder') }}
                                </a>
                                <hr class="dropdown-divider my-1">
                                
                                <!-- Looping Data Folder dengan Visual Akar (Tree) -->
                                @foreach($allSortedFolders as $f)
                                    @php
                                        $fullText = '[' . ($f->department->name ?? 'Umum') . '] ' . $f->name;
                                        $isSelected = request('folder_id') == $f->id;
                                    @endphp
                                    
                                    <a class="dropdown-item rounded-3 py-2 filter-folder-opt {{ $isSelected ? 'bg-primary bg-opacity-10 text-primary fw-bold' : 'text-dark' }} d-flex align-items-center" href="#" data-id="{{ $f->id }}" data-text="{{ $fullText }}" style="{{ $f->depth > 0 ? 'padding-left: '.($f->depth * 20 + 16).'px !important;' : '' }}">
                                        @if($f->depth > 0)
                                            <i class="bi bi-arrow-return-right text-muted fs-6 me-2 flex-shrink-0 opacity-50"></i>
                                        @else
                                            <i class="bi bi-folder-fill text-warning fs-5 me-2 flex-shrink-0"></i>
                                        @endif
                                        <div class="overflow-hidden">
                                            <div class="text-truncate small fw-medium">{{ $f->name }}</div>
                                            <div class="text-uppercase" style="font-size: 0.6rem; opacity: 0.6;">{{ $f->department->name ?? 'Umum' }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            
                            <!-- Pesan Jika Data Tidak Ditemukan -->
                            <div id="filterNoFolderFound" class="text-center text-muted py-3 d-none small">
                                <i class="bi bi-folder-x fs-4 d-block mb-1 opacity-50"></i>
                                Folder tidak ditemukan
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100"><i class="bi bi-search me-1"></i> {{ __('Filter') }}</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('docs.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-counterclockwise"></i> {{ __('Reset') }}</a>
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

        <div class="card md-card border-0 stagger-3 mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0" id="documentTable">
                        <thead class="border-bottom border-light bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide" style="width: 30%;">{{ __('File Name') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Folder') }}</th>
                                
                                <!-- KOLOM BARU: FOLDER INDUK -->
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Folder Induk') }}</th>
                                
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
                                    elseif (in_array($ext, ['ppt', 'pptx'])) { $icon = 'bi-file-earmark-slides-fill'; $colorClass = 'bg-warning text-warning'; }
                                @endphp
                                <tr class="table-row-hover">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon-box {{ $colorClass }} bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                                                <i class="{{ $icon }} fs-4"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-semibold text-dark text-truncate" title="{{ $doc->title }}">{{ $doc->title }}
                                                    
                                                </div>
                                                
                                                <div class="d-flex align-items-center mt-1">
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-0 text-uppercase" style="font-size: 0.65rem;">{{ $ext }}</span>
                                                    @if($doc->file_size == 0)
                                                        <span class="ms-1 badge bg-info bg-opacity-10 text-info border border-info-subtle px-2 py-0" style="font-size: 0.65rem;">Baru Dibuat</span>
                                                    @endif
                                                </div>
                                                <div class="mt-1">
                                                @if($doc->is_public)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle" style="font-size: 0.65rem;" data-bs-toggle="tooltip" title="Dapat dilihat oleh semua orang">
                                                        <i class="bi bi-globe me-1"></i> Publik
                                                    </span>
                                                @elseif($doc->permissions->count() == 0)
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle" style="font-size: 0.65rem;" data-bs-toggle="tooltip" title="Hanya Anda yang bisa melihat ini">
                                                        <i class="bi bi-lock-fill me-1"></i> Privat
                                                    </span>
                                                @else
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle" style="font-size: 0.65rem;" data-bs-toggle="tooltip" title="Dibagikan ke {{ $doc->permissions->count() }} entitas">
                                                        <i class="bi bi-people-fill me-1"></i> Dibagikan ({{ $doc->permissions->count() }})
                                                    </span>
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border fw-normal text-truncate d-inline-block" style="max-width: 150px;">
                                            <i class="bi bi-folder2 me-1"></i> {{ $doc->folder->name ?? 'Unsorted' }}
                                        </span>
                                    </td>
                                    
                                    <!-- DATA BARU: FOLDER INDUK -->
                                    <td class="py-3">
                                        <span class="text-secondary small fw-medium">
                                            @if($doc->folder && $doc->folder->parent)
                                                <i class="bi bi-diagram-3 me-1"></i> {{ $doc->folder->parent->name }}
                                            @else
                                                <i class="bi bi-hdd-network me-1"></i> Folder Utama
                                            @endif
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
                                        {{ $doc->file_size == 0 ? '-' : number_format($doc->file_size / 1024, 1) . ' KB' }}
                                    </td>
                                    
                                    <td class="py-3">
                                        <div class="fw-medium text-dark small mb-1">{{ $doc->created_at->format('d M Y') }}</div>
                                        
                                        @if(str_contains(strtolower($doc->file_path), 'gagal') || $doc->status == 'failed')
                                            <span class="badge bg-danger border border-danger-subtle shadow-sm" style="font-size: 0.7rem; padding: 5px 8px;">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal Upload
                                            </span>
                                        @elseif($doc->google_file_id && $doc->file_path == 'Cloud/GoogleDrive')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle shadow-sm" style="font-size: 0.7rem; padding: 5px 8px;">
                                                <i class="bi bi-cloud-check-fill me-1 fs-6"></i> successfully synced to the cloud 
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark border border-warning-subtle shadow-sm syncing-indicator" style="font-size: 0.7rem; padding: 5px 8px;">
                                                <span class="spinner-grow spinner-grow-sm me-1 text-danger" style="width: 0.6rem; height: 0.6rem;" role="status"></span>
                                                Menyinkronkan...
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end pe-4 py-3">
    <div class="d-flex justify-content-end gap-1">
        
        @php
            $editableExts = ['doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx'];
            $isEditable = in_array(strtolower($doc->extension), $editableExts);
            
            // 1. Cek  Pemilik
            $isOwner = $doc->owner_id == auth()->id();
            
            // 2. Cek  Admin
            $isAdmin = auth()->user()->role_level === 'admin';
            
            // 3. Cek  file  Publik
            $isPublic = $doc->is_public;
            $hasWriteAccess = $doc->permissions->where('access_level', 'write')
                ->filter(function($p) {
                    return $p->user_id == auth()->id() || $p->department_id == auth()->user()->department_id;
                })->isNotEmpty();
        @endphp

        @if($doc->google_file_id && $isEditable)
            {{-- PERBAIKAN: Admin, Owner, Izin Write, dan Publik bisa masuk Editor --}}
            @if($isOwner || $isAdmin || $hasWriteAccess || $isPublic)
                <a href="{{ route('docs.editor', $doc->id) }}" class="btn btn-sm btn-primary text-white hover-elevate shadow-sm px-3 d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Buka di Editor (Live)">
                    <i class="bi bi-pencil-square me-2"></i> Buka Editor
                </a>
            @else
                <a href="{{ route('docs.editor', $doc->id) }}" class="btn btn-sm btn-success text-white hover-elevate shadow-sm px-3 d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Hanya Lihat (View Only)">
                    <i class="bi bi-eye-fill me-2"></i> View Only
                </a>
            @endif
        @elseif($doc->google_file_id && !$isEditable)
            <a href="https://drive.google.com/file/d/{{ $doc->google_file_id }}/view" target="_blank" class="btn btn-sm btn-info text-white hover-elevate shadow-sm px-3 d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Lihat File">
                <i class="bi bi-eye-fill me-2"></i> Lihat
            </a>
        @else
            <form action="{{ route('docs.retrySync', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm(' Sinkronisasi ulang file ini ke Google Drive?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-icon btn-light text-warning shadow-sm hover-elevate" data-bs-toggle="tooltip" title="Pancing Ulang Sinkronisasi">
                    <i class="bi bi-arrow-clockwise fs-6"></i>
                </button>
            </form>
        @endif

        @if ($doc->owner_id == auth()->id())
            <button type="button" class="btn btn-sm btn-icon btn-light text-info hover-elevate shadow-sm" data-bs-toggle="modal" data-bs-target="#shareModal{{ $doc->id }}" title="{{ __('Share Access') }}">
                <i class="bi bi-share-fill"></i>
            </button>
        @endif

        @if ($doc->owner_id == auth()->id() || auth()->user()->role_level == 'admin')
            <a href="{{ route('docs.edit', $doc->id) }}" class="btn btn-sm btn-icon btn-light text-secondary hover-primary hover-elevate shadow-sm" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                <i class="bi bi-pencil-square"></i>
            </a>
            <form action="{{ route('docs.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-light text-danger hover-elevate shadow-sm" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                    <i class="bi bi-trash3-fill"></i>
                </button>
            </form>
        @endif
    </div>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-secondary mb-2"><i class="bi bi-folder-x fs-1"></i></div>
                                        <div class="fw-medium">{{ __('Tidak ada dokumen ditemukan') }}</div>
                                        <div class="small">Silakan ubah filter pencarian atau unggah dokumen baru.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center justify-content-md-end mb-5 stagger-4">
            {{ $documents->links() }}
        </div>
    </div>

    {{-- MODAL UPLOAD --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('docs.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4" id="uploadForm">
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
                        <div class="input-group-custom position-relative">
                            <span class="input-icon" style="z-index: 1040;"><i class="bi bi-folder-symlink"></i></span>
                            
                            <input type="hidden" name="folder_id" id="selectedFolderId" required>
                            
                            <button class="form-select md-input text-start d-flex justify-content-between align-items-center bg-white" type="button" id="folderDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="padding-right: 15px;">
                                <span id="folderDropdownText" class="text-muted text-truncate" style="max-width: 90%;">{{ __('Cari & Pilih Folder Tujuan...') }}</span>
                            </button>
                            
                            <div class="dropdown-menu w-100 p-2 shadow-lg border-0 rounded-4" aria-labelledby="folderDropdownBtn" style="max-height: 300px; overflow-y: auto;">
                                
                                <div class="position-sticky top-0 bg-white pb-2" style="z-index: 10;">
                                    <div class="input-group-custom">
                                        <span class="input-icon" style="left: 10px;"><i class="bi bi-search" style="font-size: 0.8rem;"></i></span>
                                        <input type="text" class="form-control form-control-sm bg-light border-0" id="folderSearchInput" placeholder="Ketik nama folder atau bidang..." style="padding-left: 30px; border-radius: 8px;" autocomplete="off">
                                    </div>
                                </div>
                                
                                <!-- Daftar Folder dengan Hierarki -->
                                <div id="folderOptionsList">
                                    @foreach($allSortedFolders as $f)
                                        <a class="dropdown-item rounded-3 py-2 folder-option d-flex align-items-center" href="#" data-id="{{ $f->id }}">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle me-2 flex-shrink-0" style="font-size: 0.65rem;">[{{ $f->department->name ?? 'Umum' }}]</span>
                                            <span class="folder-name text-truncate text-dark small fw-medium" style="padding-left: {{ $f->depth * 15 }}px;">
                                                @if($f->depth > 0)
                                                    <i class="bi bi-arrow-return-right text-muted me-1 opacity-50"></i>
                                                @endif
                                                <i class="bi bi-folder-fill text-warning me-2"></i>{{ $f->name }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                                
                                <div id="noFolderFound" class="text-center text-muted py-3 d-none small">
                                    <i class="bi bi-folder-x fs-4 d-block mb-1 opacity-50"></i>
                                    Folder tidak ditemukan
                                </div>
                            </div>
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

    {{-- MODAL CREATE BLANK --}}
    <div class="modal fade" id="createBlankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('docs.storeBlank') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-file-earmark-plus"></i>
                        </div>
                        {{ __('Buat Dokumen Baru') }}
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Nama Dokumen') }}</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-fonts"></i></span>
                            <input type="text" name="title" class="form-control md-input" placeholder="e.g. Surat Keputusan 2026" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Jenis Dokumen') }}</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-window-stack"></i></span>
                            <select name="type" class="form-select md-input" required>
                                <option value="doc" selected>📄 Google Docs (Word)</option>
                                <option value="xls">📊 Google Sheets (Excel)</option>
                                <option value="ppt">📽️ Google Slides (PowerPoint)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Simpan di Folder') }}</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-folder-symlink"></i></span>
                            <select name="folder_id" class="form-select md-input" required>
                                <option value="" disabled selected>{{ __('Pilih Folder Tujuan...') }}</option>
                                {{-- Hierarki visual dengan spasi HTML --}}
                                @foreach($allSortedFolders as $f)
                                    <option value="{{ $f->id }}">
                                        [{{ $f->department->name ?? 'Umum' }}] {!! str_repeat('&nbsp;&nbsp;&nbsp;', $f->depth) !!} {!! $f->depth > 0 ? '&#x21B3; ' : '' !!} {{ $f->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-success md-btn px-5" onclick="this.innerHTML='<i class=\'bi bi-hourglass-split me-2\'></i> Memproses...';"><i class="bi bi-plus-lg me-2"></i> {{ __('Buat Sekarang') }}</button>
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
                            <!-- List Akses Saat Ini -->
                            @if($doc->permissions->count() > 0 || $doc->is_public)
                                <div class="mb-4 p-3 bg-light rounded-3 border">
                                    <label class="form-label small fw-bold text-secondary mb-2 text-uppercase tracking-wide"><i class="bi bi-info-circle me-1"></i> {{ __('Currently Shared With') }}</label>
                                    <ul class="list-group list-group-flush mb-0">
                                        
                                        @if($doc->is_public)
                                            <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-2 py-2 border-bottom border-light">
                                                <div>
                                                    <i class="bi bi-globe text-success me-2"></i> <span class="fw-medium text-dark small">{{ __('Public (Everyone)') }}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">
                                                        {{ __('Akses Terbuka') }}
                                                    </span>
                                                    <form action="{{ route('docs.unshare_public', $doc->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('{{ __('Cabut akses publik dari dokumen ini?') }}')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 m-0 border-0 text-decoration-none shadow-none" title="{{ __('Cabut Akses Publik') }}">
                                                            <i class="bi bi-x-circle-fill fs-6"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endif

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
                                        <option value="public">{{ __('Public (Everyone)') }}</option>
                                    </select>
                                </div>

                                <div id="user_field_{{ $doc->id }}" class="mb-4">
                                    <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Select Colleague') }}</label>
                                    <div class="position-relative">
                                        <input type="hidden" name="user_id" id="selectedUser_{{ $doc->id }}">
                                        <button class="form-select md-input text-start d-flex justify-content-between align-items-center bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="userText_{{ $doc->id }}" class="text-muted text-truncate">{{ __('Cari & Pilih Pegawai...') }}</span>
                                        </button>
                                        <div class="dropdown-menu w-100 p-2 shadow-lg border-0 rounded-4" style="max-height: 250px; overflow-y: auto;">
                                            <div class="position-sticky top-0 bg-white pb-2" style="z-index: 10;">
                                                <input type="text" class="form-control form-control-sm bg-light border-0 search-user-input" data-target="userList_{{ $doc->id }}" placeholder="{{ __('Ketik nama pegawai...') }}" onclick="event.stopPropagation();">
                                            </div>
                                            <div id="userList_{{ $doc->id }}">
                                                @foreach ($users as $u)
                                                    <a class="dropdown-item rounded-3 py-2" href="#" onclick="event.preventDefault(); selectShareOption('user', {{ $doc->id }}, {{ $u->id }}, '{{ addslashes($u->full_name) }}')">
                                                        <i class="bi bi-person me-2 opacity-50"></i>{{ $u->full_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="dept_field_{{ $doc->id }}" class="mb-4" style="display:none;">
                                    <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Select Department') }}</label>
                                    <div class="position-relative">
                                        <input type="hidden" name="department_id" id="selectedDept_{{ $doc->id }}">
                                        <button class="form-select md-input text-start d-flex justify-content-between align-items-center bg-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="deptText_{{ $doc->id }}" class="text-muted text-truncate">{{ __('Cari & Pilih Departemen...') }}</span>
                                        </button>
                                        <div class="dropdown-menu w-100 p-2 shadow-lg border-0 rounded-4" style="max-height: 250px; overflow-y: auto;">
                                            <div class="position-sticky top-0 bg-white pb-2" style="z-index: 10;">
                                                <input type="text" class="form-control form-control-sm bg-light border-0 search-dept-input" data-target="deptList_{{ $doc->id }}" placeholder="{{ __('Ketik nama departemen...') }}" onclick="event.stopPropagation();">
                                            </div>
                                            <div id="deptList_{{ $doc->id }}">
                                                @foreach ($departments as $d)
                                                    <a class="dropdown-item rounded-3 py-2" href="#" onclick="event.preventDefault(); selectShareOption('dept', {{ $doc->id }}, {{ $d->id }}, '{{ addslashes($d->name) }}')">
                                                        <i class="bi bi-building me-2 opacity-50"></i>{{ $d->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2" id="access_level_field_{{ $doc->id }}">
                                    <label class="form-label small fw-semibold text-secondary mb-2">{{ __('Access Privilege') }}</label>
                                    <select name="access_level" class="form-select md-input">
                                        <option value="read">{{ __('Viewer') }}</option>
                                        <option value="write">{{ __('Editor') }}</option>
                                    </select>
                                </div>
                        </div>
                        
                        <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                            <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-info text-white md-btn px-5"><i class="bi bi-send me-2"></i> {{ __('Grant Access') }}</button>
                        </div>
                            </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <div id="progressOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center" style="background-color: rgba(0, 0, 0, 0.6); z-index: 9999; backdrop-filter: blur(4px);">
        <div class="p-4 bg-white rounded-4 shadow-lg border-0" style="width: 90%; max-width: 400px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Mengunggah File...
                </h6>
                <span id="uploadProgressText" class="fw-bold text-primary small">0%</span>
            </div>
            <div class="progress" style="height: 12px; border-radius: 8px;">
                <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="text-secondary small mt-3 mb-0 text-center" id="uploadStatusMessage">Mentransfer file ke server...</p>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

@include('documents.script')
@endsection