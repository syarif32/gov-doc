@extends('layouts.admin')

@section('content')
<div class="dashboard-container h-100 d-flex flex-column animate__animated animate__fadeIn">
    
    <div class="d-flex align-items-center mb-4">
        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 48px; height: 48px;">
            <i class="bi bi-hdd-network fs-4"></i>
        </div>
        <div>
            <h3 class="mb-0 fw-bold text-dark">{{ __('Folders Explorer') }}</h3>
            <div class="small text-secondary">Jelajahi struktur folder dan dokumen di server</div>
        </div>
    </div>

    <!-- SPLIT PANE LAYOUT -->
    <div class="row flex-grow-1 g-3">
        
        <!-- SIDEBAR POHON FOLDER -->
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom border-light p-3">
                    <span class="fw-bold text-uppercase small tracking-wide text-secondary"><i class="bi bi-diagram-3 me-2"></i> Direktori Server</span>
                </div>
                <div class="card-body p-0 overflow-auto custom-scrollbar" style="height: calc(100vh - 250px);">
                    
                    {{-- FUNGSI PHP UNTUK RENDER POHON BESERTA RBAC (HAK AKSES) --}}
                    @php
                        $currentUser = auth()->user();
                        $isAdmin = $currentUser->role_level === 'admin';

                        if (!function_exists('renderTreeAJAX')) {
                            function renderTreeAJAX($folders, $parentId = null, $user, $isAdmin) {
                                $children = $folders->where('parent_id', $parentId)->sortBy('name');
                                if ($children->isEmpty()) return '';

                                $html = '<ul class="folder-tree">';
                                $hasVisibleItems = false; // Tracker untuk mengecek apakah ada isi di cabang ini

                                foreach ($children as $child) {
                                    $subfoldersCount = $folders->where('parent_id', $child->id)->count();
                                    
                                    // SAKTI: Hitung jumlah file fisik BERDASARKAN HAK AKSES USER YANG LOGIN
                                    $query = \App\Models\Document::where('folder_id', $child->id);
                                    if (!$isAdmin) {
                                        $query->where(function ($q) use ($user) {
                                            $q->where('owner_id', $user->id)
                                              ->orWhereHas('permissions', function ($pq) use ($user) {
                                                  $pq->where('user_id', $user->id)
                                                     ->orWhere('department_id', $user->department_id);
                                              })
                                              ->orWhere('is_public', true);
                                        });
                                    }
                                    $filesCount = $query->count();
                                    
                                    // AUTO-HIDE: Jika bukan admin, dan folder ini tidak punya anak, SERTA tidak ada file yang bisa diakses -> SEMBUNYIKAN!
                                    if (!$isAdmin && $subfoldersCount == 0 && $filesCount == 0) {
                                        continue; 
                                    }

                                    $hasVisibleItems = true;
                                    $hasChildren = $subfoldersCount > 0;
                                    $toggleIcon = $hasChildren ? '<i class="bi bi-chevron-right folder-toggle text-secondary" data-bs-toggle="collapse" data-bs-target="#collapseFolder'.$child->id.'"></i>' : '<i class="bi bi-dot text-light"></i>';
                                    
                                    $html .= '<li>';
                                    $html .= '<div class="folder-item d-flex align-items-center" data-id="'.$child->id.'">';
                                    $html .= $toggleIcon;
                                    $html .= '<i class="bi bi-folder-fill folder-icon text-warning mx-2"></i>';
                                    $html .= '<span class="folder-name text-truncate">'.$child->name.'</span>';
                                    
                                    // ---- UI BADGE INDIKATOR DI KANAN ----
                                    $html .= '<div class="ms-auto d-flex align-items-center gap-1 opacity-75">';
                                    
                                    if ($subfoldersCount > 0) {
                                        $html .= '<span class="badge bg-warning bg-opacity-25 text-dark border border-warning-subtle rounded-pill fw-medium d-flex align-items-center" style="font-size: 0.65rem;" title="'.$subfoldersCount.' Sub-folder"><i class="bi bi-folder-fill text-warning me-1"></i>'.$subfoldersCount.'</span>';
                                    }
                                    
                                    if ($filesCount > 0) {
                                        $html .= '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill fw-medium d-flex align-items-center" style="font-size: 0.65rem;" title="'.$filesCount.' Dokumen Tersedia"><i class="bi bi-file-earmark-text-fill me-1"></i>'.$filesCount.'</span>';
                                    }
                                    
                                    if ($subfoldersCount == 0 && $filesCount == 0) {
                                        $html .= '<span class="badge bg-light text-muted border border-light rounded-pill fw-normal" style="font-size: 0.6rem;">Kosong</span>';
                                    }
                                    
                                    $html .= '</div></div>';

                                    // Render anak-anak folder secara rekursif
                                    if ($hasChildren) {
                                        $childHtml = renderTreeAJAX($folders, $child->id, $user, $isAdmin);
                                        // Jangan cetak div collapse jika anak-anaknya semua disembunyikan
                                        if ($childHtml !== '') {
                                            $html .= '<div id="collapseFolder'.$child->id.'" class="collapse">'.$childHtml.'</div>';
                                        }
                                    }
                                    $html .= '</li>';
                                }
                                $html .= '</ul>';
                                
                                return $hasVisibleItems ? $html : '';
                            }
                        }
                    @endphp

                    <ul class="root-tree mt-3">
                        @foreach($departments as $dept)
                            @php
                                $renderedTree = renderTreeAJAX($dept->folders, null, $currentUser, $isAdmin);
                                
                                // AUTO-HIDE DEPARTEMEN: Jika departemen ini tidak ada file/folder yang bisa diakses user biasa, sembunyikan utuh!
                                if (!$isAdmin && $renderedTree === '') continue;
                            @endphp
                            
                            <li class="mb-2">
                                <div class="dept-label d-flex align-items-center fw-bold text-dark px-3 py-2" data-bs-toggle="collapse" data-bs-target="#deptCollapse{{ $dept->id }}" style="cursor: pointer; background: #f8f9fa;">
                                    <i class="bi bi-building text-primary me-2"></i> 
                                    <span class="text-truncate">{{ $dept->name }}</span>
                                    <i class="bi bi-chevron-down ms-auto text-secondary" style="font-size: 0.8rem;"></i>
                                </div>
                                <div class="collapse show" id="deptCollapse{{ $dept->id }}">
                                    @if($renderedTree !== '')
                                        {!! $renderedTree !!}
                                    @else
                                        <div class="small text-muted ps-5 py-2 fst-italic">Kosong</div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>
        </div>

        <!-- AREA KONTEN FILE (AJAX TARGET) -->
        <div class="col-md-8 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-md-5 overflow-auto custom-scrollbar" id="ajaxFileContainer" style="height: calc(100vh - 250px);">
                    
                    <!-- TAMPILAN DEFAULT SEBELUM KLIK -->
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center opacity-50" id="emptyState">
                        <i class="bi bi-mouse3 fs-1 text-secondary mb-3"></i>
                        <h4 class="fw-bold text-secondary">Pilih Folder</h4>
                        <p class="small">Klik salah satu folder di panel sebelah kiri<br>untuk melihat isi dokumen di dalamnya.</p>
                    </div>

                    <!-- SPINNER LOADING -->
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center d-none" id="loadingState">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                        <div class="mt-3 fw-medium text-secondary">Mengambil Data...</div>
                    </div>

                    <!-- WADAH HTML HASIL AJAX -->
                    <div id="fileResult" class="d-none"></div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS UNTUK HIERARKI POHON */
    .root-tree { list-style: none; padding-left: 0; margin: 0; }
    .folder-tree { list-style: none; padding-left: 15px; margin-top: 2px; }
    
    .folder-tree .folder-tree { border-left: 1px dashed #dee2e6; margin-left: 12px; }

    .folder-item {
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 2px;
        font-size: 0.9rem;
        color: #495057;
    }
    
    .folder-item:hover { background-color: #f1f3f4; color: #202124; }
    
    .folder-item.active {
        background-color: #e8f0fe;
        color: #1a73e8;
        font-weight: 600;
    }

    .folder-toggle {
        width: 16px;
        display: inline-block;
        transition: transform 0.2s;
    }
    
    .folder-toggle[aria-expanded="true"] { transform: rotate(90deg); color: #1a73e8 !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const folderItems = document.querySelectorAll('.folder-item');
        const emptyState = document.getElementById('emptyState');
        const loadingState = document.getElementById('loadingState');
        const fileResult = document.getElementById('fileResult');

        folderItems.forEach(item => {
            item.addEventListener('click', function(e) {
                if(e.target.classList.contains('folder-toggle')) return;

                folderItems.forEach(f => {
                    f.classList.remove('active');
                    let icon = f.querySelector('.folder-icon');
                    icon.classList.remove('bi-folder2-open', 'text-primary');
                    icon.classList.add('bi-folder-fill', 'text-warning');
                });

                this.classList.add('active');
                let activeIcon = this.querySelector('.folder-icon');
                activeIcon.classList.remove('bi-folder-fill', 'text-warning');
                activeIcon.classList.add('bi-folder2-open', 'text-primary');

                const folderId = this.getAttribute('data-id');
                emptyState.classList.add('d-none');
                fileResult.classList.add('d-none');
                loadingState.classList.remove('d-none');

                let baseUrl = "{{ route('docs.explorer.files', 'DUMMY_ID') }}";
                const fetchUrl = baseUrl.replace('DUMMY_ID', folderId);
                
                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Terjadi kesalahan jaringan atau route tidak ditemukan');
                        }
                        return response.json(); 
                    })
                    .then(data => {
                        loadingState.classList.add('d-none');
                        
                        if(data.success) {
                            fileResult.innerHTML = data.html;
                            fileResult.classList.remove('d-none');
                        } else {
                            alert('Gagal memuat data!');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        loadingState.classList.add('d-none');
                        alert('Terjadi kesalahan jaringan. Cek console untuk info lebih lanjut.');
                    });
            });
        });
    });
</script>
@endsection