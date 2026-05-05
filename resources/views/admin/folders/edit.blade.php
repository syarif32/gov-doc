@extends('layouts.admin')

@section('content')
<div class="dashboard-container animate__animated animate__fadeIn">
    
    @if(session('error'))
        <div class="alert alert-danger mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i> {{ session('error') }}
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                style="width: 48px; height: 48px;">
                <i class="bi bi-pencil-square fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Edit Folder</h2>
                <p class="text-secondary small mb-0">Perbarui informasi dan lokasi hierarki folder</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.folders.index') }}" class="btn btn-light border shadow-sm d-flex align-items-center px-4 py-2 hover-elevate">
                <i class="bi bi-arrow-left me-2"></i> <span class="fw-semibold">Kembali</span>
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light p-4 d-flex align-items-center">
                    <i class="bi bi-folder-fill text-warning fs-4 me-3"></i>
                    <h5 class="fw-bold text-dark mb-0 text-truncate">{{ $folder->name }}</h5>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.folders.update', $folder->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">Nama Folder</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-fonts"></i></span>
                                <input type="text" name="name" class="form-control md-input" value="{{ $folder->name }}" required autocomplete="off">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">Pindahkan ke Bidang/Departemen</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-building"></i></span>
                                <select name="department_id" id="deptSelect" class="form-select md-input" required>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $folder->department_id == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text small mt-2 text-warning"><i class="bi bi-info-circle me-1"></i> Mengubah bidang akan me-reset pilihan Sub-Folder Induk di bawah.</div>
                        </div>

                        <div class="mb-2 bg-light p-4 border border-light rounded-4">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-3 d-flex align-items-center">
                                <i class="bi bi-diagram-3 text-primary me-2"></i> Ubah Sub-Folder Induk (Parent)
                            </label>

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
                                $allSortedFolders = buildFolderTree($allFolders);
                                
                                // Cari nama parent saat ini untuk ditampilkan di tombol
                                $currentParentName = "-- Folder Utama (Root) --";
                                if ($folder->parent_id) {
                                    $parentObj = $allFolders->firstWhere('id', $folder->parent_id);
                                    if ($parentObj) $currentParentName = $parentObj->name;
                                }
                            @endphp

                            <!-- CUSTOM SEARCHABLE DROPDOWN UNTUK SUB FOLDER -->
                            <div class="input-group-custom position-relative">
                                <input type="hidden" name="parent_id" id="hiddenSubFolderId" value="{{ $folder->parent_id }}">
                                
                                <button class="form-select md-input text-start d-flex justify-content-between align-items-center bg-white shadow-sm" type="button" id="subFolderDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="padding-right: 15px;">
                                    <span id="subFolderDropdownText" class="fw-bold text-dark text-truncate" style="max-width: 90%;">
                                        {{ $currentParentName }}
                                    </span>
                                </button>
                                
                                <div class="dropdown-menu w-100 p-2 shadow-lg border-0 rounded-4" aria-labelledby="subFolderDropdownBtn" style="max-height: 300px; overflow-y: auto;">
                                    
                                    <div class="position-sticky top-0 bg-white pb-2" style="z-index: 10;">
                                        <div class="input-group-custom">
                                            <span class="input-icon" style="left: 10px;"><i class="bi bi-search" style="font-size: 0.8rem;"></i></span>
                                            <input type="text" class="form-control form-control-sm bg-light border-0" id="subFolderSearchInput" placeholder="Ketik nama folder induk..." style="padding-left: 30px; border-radius: 8px;" autocomplete="off">
                                        </div>
                                    </div>
                                    
                                    <div id="subFolderOptionsList">
                                        <a class="dropdown-item rounded-3 py-2 sub-folder-opt default-opt d-flex align-items-center" href="#" data-id="" style="display: flex;">
                                            <span class="fw-bold text-primary"><i class="bi bi-hdd-network me-2"></i>-- Jadikan Folder Utama (Root) --</span>
                                        </a>
                                        
                                        @foreach($allSortedFolders as $f)
                                            {{-- Mencegah folder memilih dirinya sendiri sebagai induk --}}
                                            @if($f->id != $folder->id)
                                                <a class="dropdown-item rounded-3 py-2 sub-folder-opt d-flex align-items-center" href="#" data-id="{{ $f->id }}" data-dept="{{ $f->department_id }}" style="display: none;">
                                                    <span class="text-truncate text-dark small fw-medium" style="padding-left: {{ $f->depth * 15 }}px;">
                                                        @if($f->depth > 0)
                                                            <i class="bi bi-arrow-return-right text-muted me-1 opacity-50"></i>
                                                        @endif
                                                        <i class="bi bi-folder-fill text-warning me-2"></i>{{ $f->name }}
                                                    </span>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                    <div id="noSubFolderFound" class="text-center text-muted py-3 d-none small">
                                        <i class="bi bi-folder-x fs-4 d-block mb-1 opacity-50"></i>
                                        Folder tidak ditemukan
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5 pt-3 border-top border-light">
                            <button type="submit" class="btn btn-primary md-btn px-5 py-2 shadow-sm hover-elevate">
                                <i class="bi bi-save2 me-2"></i> Simpan Perubahan
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
        // LOGIKA DINAMIS & SEARCH UNTUK SUB-FOLDER
        const deptSelect = document.getElementById('deptSelect');
        const hiddenSubFolderId = document.getElementById('hiddenSubFolderId');
        const subFolderDropdownText = document.getElementById('subFolderDropdownText');
        const subFolderSearchInput = document.getElementById('subFolderSearchInput');
        const subFolderOptions = document.querySelectorAll('.sub-folder-opt:not(.default-opt)');
        const defaultRootOpt = document.querySelector('.sub-folder-opt.default-opt');
        const noSubFolderFound = document.getElementById('noSubFolderFound');
        
        // Fungsi untuk menyaring opsi berdasarkan Bidang
        function filterFoldersByDept(deptId) {
            subFolderOptions.forEach(opt => {
                if(opt.getAttribute('data-dept') === deptId) {
                    opt.style.display = 'flex';
                    opt.classList.remove('d-none-dept');
                } else {
                    opt.style.display = 'none';
                    opt.classList.add('d-none-dept');
                }
            });
        }

        // 1. Inisialisasi awal saat halaman diload (Buka opsi sesuai departemen saat ini)
        filterFoldersByDept(deptSelect.value);

        // 2. Saat Bidang diubah oleh user
        deptSelect.addEventListener('change', function() {
            // Reset text & hidden value jika ganti departemen
            hiddenSubFolderId.value = "";
            subFolderDropdownText.innerText = "-- Jadikan Folder Utama (Root) --";
            subFolderDropdownText.classList.remove('text-muted', 'text-dark', 'fw-bold');
            subFolderDropdownText.classList.add('text-primary', 'fw-bold');

            defaultRootOpt.style.display = 'flex';
            subFolderSearchInput.value = '';
            noSubFolderFound.classList.add('d-none');

            filterFoldersByDept(this.value);
        });

        // 3. Saat opsi sub-folder diklik
        document.querySelectorAll('.sub-folder-opt').forEach(opt => {
            opt.addEventListener('click', function(e) {
                e.preventDefault();
                const folderId = this.getAttribute('data-id');
                let rawText = this.innerText.trim(); 
                
                hiddenSubFolderId.value = folderId;
                subFolderDropdownText.innerText = rawText;
                subFolderDropdownText.classList.remove('text-primary');
                subFolderDropdownText.classList.add('text-dark', 'fw-bold');
                
                subFolderSearchInput.value = '';
                subFolderSearchInput.dispatchEvent(new Event('input'));
            });
        });

        // 4. Mesin Pencari Dropdown
        subFolderSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let visibleCount = 0;

            subFolderOptions.forEach(opt => {
                if(!opt.classList.contains('d-none-dept')) {
                    const text = opt.innerText.toLowerCase();
                    if(text.includes(searchTerm)) {
                        opt.style.setProperty('display', 'flex', 'important');
                        visibleCount++;
                    } else {
                        opt.style.setProperty('display', 'none', 'important');
                    }
                }
            });

            if(searchTerm === '') {
                defaultRootOpt.style.display = 'flex';
                visibleCount++;
            } else {
                defaultRootOpt.style.display = 'none';
            }

            if(visibleCount === 0) {
                noSubFolderFound.classList.remove('d-none');
            } else {
                noSubFolderFound.classList.add('d-none');
            }
        });
    });
</script>

<style>
    .input-group-custom { position: relative; }
    .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); z-index: 10; color: #6c757d; }
    .md-input { padding-left: 42px !important; border-radius: 10px; border: 1px solid #dee2e6; height: 46px; font-size: 0.95rem; transition: all 0.2s; }
    .md-input:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); }
    select.md-input { appearance: none; padding-right: 36px; }
    .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-elevate:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>
@endsection