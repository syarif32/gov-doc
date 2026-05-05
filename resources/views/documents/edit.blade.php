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
                                
                                {{-- MESIN PENGURUTAN HIERARKI --}}
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
                                    
                                    $allSortedFolders = buildFolderTree($folders);
                                    
                                    // Cari nama folder saat ini
                                    $currentFolderName = "-- Pilih Folder Tujuan --";
                                    if ($document->folder_id) {
                                        $currentFolderObj = $folders->firstWhere('id', $document->folder_id);
                                        if ($currentFolderObj) {
                                            $currentFolderName = '[' . ($currentFolderObj->department->name ?? 'Umum') . '] ' . $currentFolderObj->name;
                                        }
                                    }
                                @endphp

                                <!-- CUSTOM SEARCHABLE DROPDOWN -->
                                <div class="input-group-custom position-relative">
                                    <span class="input-icon" style="z-index: 1040;"><i class="bi bi-folder2-open"></i></span>
                                    
                                    <input type="hidden" name="folder_id" id="hiddenFolderId" value="{{ $document->folder_id }}" required>
                                    
                                    <button class="form-select md-input text-start d-flex justify-content-between align-items-center bg-white" type="button" id="folderDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="padding-right: 15px;">
                                        <span id="folderDropdownText" class="fw-bold text-dark text-truncate" style="max-width: 90%;">{{ $currentFolderName }}</span>
                                    </button>
                                    
                                    <div class="dropdown-menu w-100 p-2 shadow-lg border-0 rounded-4" aria-labelledby="folderDropdownBtn" style="max-height: 300px; overflow-y: auto;">
                                        
                                        <div class="position-sticky top-0 bg-white pb-2" style="z-index: 10;">
                                            <div class="input-group-custom">
                                                <span class="input-icon" style="left: 10px; z-index: 10;"><i class="bi bi-search" style="font-size: 0.8rem;"></i></span>
                                                <input type="text" class="form-control form-control-sm bg-light border-0" id="folderSearchInput" placeholder="Ketik nama folder atau bidang..." style="padding-left: 30px; border-radius: 8px;" autocomplete="off">
                                            </div>
                                        </div>
                                        
                                        <div id="folderOptionsList">
                                            @foreach($allSortedFolders as $f)
                                                <a class="dropdown-item rounded-3 py-2 folder-opt d-flex align-items-center" href="#" data-id="{{ $f->id }}">
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle me-2 flex-shrink-0" style="font-size: 0.65rem;">[{{ $f->department->name ?? 'Umum' }}]</span>
                                                    
                                                    <span class="text-truncate text-dark small fw-medium" style="padding-left: {{ $f->depth * 15 }}px;">
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
                                <small class="text-muted mt-2 d-block">{{ __('Pindahkan dokumen ini ke folder bidang lain jika diperlukan.') }}</small>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // LOGIKA SEARCH DROPDOWN
            const hiddenFolderId = document.getElementById('hiddenFolderId');
            const folderDropdownText = document.getElementById('folderDropdownText');
            const folderSearchInput = document.getElementById('folderSearchInput');
            const folderOptions = document.querySelectorAll('.folder-opt');
            const noFolderFound = document.getElementById('noFolderFound');

            // Mencegah dropdown tertutup saat mengetik di kolom pencarian
            folderSearchInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            folderOptions.forEach(opt => {
                opt.addEventListener('click', function(e) {
                    e.preventDefault();
                    const folderId = this.getAttribute('data-id');
                    
                    // Ambil teks dari badge dan nama folder
                    const deptBadge = this.querySelector('.badge').innerText;
                    const folderName = this.querySelector('.fw-medium').innerText.trim();
                    
                    hiddenFolderId.value = folderId;
                    folderDropdownText.innerText = deptBadge + ' ' + folderName;
                    
                    folderSearchInput.value = '';
                    folderSearchInput.dispatchEvent(new Event('input'));
                });
            });

            folderSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let visibleCount = 0;

                folderOptions.forEach(opt => {
                    const text = opt.innerText.toLowerCase();
                    if(text.includes(searchTerm)) {
                        opt.style.setProperty('display', 'flex', 'important');
                        visibleCount++;
                    } else {
                        opt.style.setProperty('display', 'none', 'important');
                    }
                });

                if(visibleCount === 0) {
                    noFolderFound.classList.remove('d-none');
                } else {
                    noFolderFound.classList.add('d-none');
                }
            });
        });
    </script>

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
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
            outline: none;
        }

        /* Component: Custom Input & Dropdown */
        .input-group-custom { position: relative; }
        .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); z-index: 10; color: #6c757d; }
        .md-input { padding-left: 42px !important; border-radius: 10px; border: 1px solid #dee2e6; height: 46px; font-size: 0.95rem; transition: all 0.2s; }
        .md-input:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); }
        select.md-input { appearance: none; padding-right: 36px; }
    </style>
@endsection