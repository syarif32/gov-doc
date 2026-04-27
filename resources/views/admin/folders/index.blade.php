@extends('layouts.admin')

@section('content')
    <div class="dashboard-container">
        
        @if(session('success'))
            <div class="alert alert-success mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-3"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 rounded-4 shadow-sm border-0 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i> {{ session('error') }}
            </div>
        @endif

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 stagger-1">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-folder-symlink-fill fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Manajemen Direktori</h2>
                    <p class="text-secondary small mb-0">Kelola struktur folder dan hierarki penyimpanan per bidang</p>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary md-btn d-flex align-items-center px-4 py-2 shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#addFolderModal">
                    <i class="bi bi-folder-plus me-2 fs-5"></i> <span class="fw-semibold">Folder Baru</span>
                </button>
            </div>
        </div>

        <div class="card md-card border-0 stagger-2 overflow-hidden h-100" style="min-height: 600px;">
            <div class="row g-0 h-100">

                <div class="col-md-4 col-lg-3 border-end bg-light d-flex flex-column">
                    <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-uppercase small tracking-wide text-secondary">Daftar Bidang</span>
                        <span class="badge bg-primary rounded-pill">{{ $departments->count() }}</span>
                    </div>

                    <div class="p-2 overflow-auto flex-grow-1 custom-scrollbar" style="max-height: calc(100vh - 250px);">
                        <div class="nav flex-column nav-pills custom-v-pills" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            @forelse($departments as $dept)
                                <button class="nav-link text-start mb-1 {{ $loop->first ? 'active' : '' }}"
                                    id="v-pills-dept-{{ $dept->id }}-tab" data-bs-toggle="pill"
                                    data-bs-target="#v-pills-dept-{{ $dept->id }}" type="button" role="tab">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium text-truncate pe-2"><i
                                                class="bi bi-building me-2 opacity-50"></i>{{ $dept->name }}</span>
                                        <span
                                            class="badge bg-white text-dark shadow-sm rounded-pill">{{ $dept->folders->count() }}</span>
                                    </div>
                                </button>
                            @empty
                                <div class="text-center p-3 text-muted small">
                                    Belum ada data bidang.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-md-8 col-lg-9 bg-white d-flex flex-column">
                    <div class="tab-content flex-grow-1" id="v-pills-tabContent">
                        @foreach($departments as $dept)
                            <div class="tab-pane fade h-100 {{ $loop->first ? 'show active' : '' }}"
                                id="v-pills-dept-{{ $dept->id }}" role="tabpanel" tabindex="0">

                                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">{{ $dept->name }}</h5>
                                        <div class="text-muted small">ID Bidang: {{ str_pad($dept->id, 3, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="p-0 table-responsive custom-scrollbar" style="max-height: calc(100vh - 320px);">
                                    <table class="table table-hover table-borderless align-middle mb-0">
                                        <thead class="bg-light bg-opacity-50 border-bottom border-light sticky-top">
                                            <tr>
                                                <th
                                                    class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">
                                                    Nama Folder</th>
                                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">
                                                    Hierarki</th>
                                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">
                                                    Dibuat Pada</th>
                                                <th
                                                    class="text-end pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($dept->folders as $folder)
                                                <tr class="table-row-hover border-bottom border-light">
                                                    <td class="ps-4 py-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-folder-fill fs-4 text-warning me-3"></i>
                                                            <span class="fw-semibold text-dark">{{ $folder->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="py-3">
                                                        @if($folder->parent)
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-arrow-return-right text-muted me-2"></i>
                                                                <span
                                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 rounded">
                                                                    {{ $folder->parent->name }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="badge bg-light text-secondary border px-2 py-1 rounded">
                                                                <i class="bi bi-hdd-network me-1"></i> Folder Root
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 text-secondary small font-monospace">
                                                        {{ $folder->created_at->format('d M Y') }}
                                                    </td>
                                                    
                                                    <td class="text-end pe-4 py-3">
                                                        <a href="{{ route('admin.folders.edit', $folder->id) }}"
                                                            class="btn btn-sm btn-icon btn-light text-primary hover-elevate me-2"
                                                            data-bs-toggle="tooltip" title="Edit Folder">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        <form action="{{ route('admin.folders.destroy', $folder->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('PERINGATAN: Hapus folder ini? Sistem akan menolak jika masih ada dokumen di dalamnya.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-icon btn-light text-danger hover-elevate"
                                                                data-bs-toggle="tooltip" title="Hapus Folder">
                                                                <i class="bi bi-trash3-fill"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5">
                                                        <div class="text-muted d-flex flex-column align-items-center">
                                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                                                                style="width: 70px; height: 70px;">
                                                                <i class="bi bi-folder-x fs-2 text-secondary opacity-50"></i>
                                                            </div>
                                                            <h6 class="fw-bold text-dark mb-1">Direktori Kosong</h6>
                                                            <p class="small mb-0">Belum ada folder yang dibuat untuk bidang ini.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="addFolderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.folders.store') }}" method="POST"
                class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-folder-plus"></i>
                        </div>
                        Buat Folder Baru
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">Nama
                            Folder</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-folder"></i></span>
                            <input type="text" name="name" class="form-control md-input"
                                placeholder="Contoh: Laporan Triwulan I" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">Pilih
                            Bidang (Pemilik)</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-building"></i></span>
                            <select name="department_id" id="deptSelect" class="form-select md-input" required>
                                <option value="" disabled selected>-- Tentukan Bidang --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-2 bg-light p-3 border rounded-3">
                        <label
                            class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2 d-flex align-items-center">
                            <i class="bi bi-diagram-3 text-primary me-2"></i> Sub-Folder (Opsional)
                        </label>
                        <select name="parent_id" id="subFolderSelect" class="form-select md-input bg-white">
                            <option value="" class="default-opt">-- Pilih Bidang Terlebih Dahulu --</option>
                            @foreach($folders as $f)
                                <option value="{{ $f->id }}" data-dept="{{ $f->department_id }}" style="display:none;">
                                    {{ $f->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small mt-2">Pilih jika Anda ingin meletakkan folder ini di dalam folder yang
                            sudah ada (Sesuai Bidang).</div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary md-btn px-5"><i class="bi bi-save2 me-2"></i> Simpan
                        Folder</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tooltip Init
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Logika Dinamis Sub-Folder
            const deptSelect = document.getElementById('deptSelect');
            const subFolderSelect = document.getElementById('subFolderSelect');
            const allFolderOptions = subFolderSelect.querySelectorAll('option:not(.default-opt)');

            deptSelect.addEventListener('change', function() {
                const selectedDeptId = this.value;

                // 1. Kembalikan ke pilihan default
                subFolderSelect.value = "";
                subFolderSelect.querySelector('.default-opt').innerText = "-- Letakkan sebagai Folder Utama (Root) --";

                // 2. Sembunyikan semua opsi folder dulu
                allFolderOptions.forEach(opt => {
                    opt.style.display = 'none';
                    
                    // 3. Tampilkan HANYA folder yang 'data-dept'-nya sama dengan bidang yang dipilih
                    if(opt.getAttribute('data-dept') === selectedDeptId) {
                        opt.style.display = 'block';
                    }
                });
            });
        });
    </script>

    <style>
        .custom-v-pills .nav-link { border-radius: 8px; color: #5f6368; padding: 12px 16px; transition: all 0.2s ease; background: transparent; border: 1px solid transparent; }
        .custom-v-pills .nav-link:hover:not(.active) { background-color: #f1f3f4; color: #202124; }
        .custom-v-pills .nav-link.active { background-color: #e8f0fe !important; color: #1a73e8 !important; border: 1px solid #d2e3fc; }
        .custom-v-pills .nav-link.active .badge { background-color: #1a73e8 !important; color: #fff !important; }
        select.md-input { appearance: none; padding-right: 36px; }
        .input-group-custom { position: relative; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); z-index: 10; color: #5f6368; }
        .md-input { padding-left: 40px !important; border-radius: 10px; border: 1px solid #dadce0; }
    </style>
@endsection