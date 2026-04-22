@extends('layouts.admin')

@section('content')
    <div class="dashboard-container">
        <div class="d-flex align-items-center mb-4 pb-2 stagger-1">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                <i class="bi bi-diagram-3-fill fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ __('Manajemen Bidang') }}</h2>
                <p class="text-secondary small mb-0">{{ __('Kelola unit organisasi dan bidang internal dinas') }}</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-4 col-lg-5 stagger-2">
                <div class="card md-card border-0 position-relative overflow-hidden h-100">
                    <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 4px;"></div>
                    
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <i class="bi bi-plus-circle-fill text-primary me-2"></i> Tambah Bidang
                        </h5>
                    </div>
                    
                    <div class="card-body px-4 pb-4 pt-2">
                        <form action="{{ route('admin.departments.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">Nama Bidang</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-building"></i></span>
                                    <input type="text" name="name" class="form-control md-input" placeholder="Misal: Bidang Jaringan / TIK" required autocomplete="off">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary md-btn w-100 mt-2 d-flex justify-content-center align-items-center py-2">
                                <i class="bi bi-check2-circle me-2 fs-5"></i> <span class="fw-semibold tracking-wide">Simpan Bidang</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7 stagger-3">
                <div class="card md-card border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">Daftar Bidang Aktif</h5>
                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fw-medium">
                            {{ $departments->count() }} Total
                        </span>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle mb-0">
                                <thead class="border-bottom border-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">Nama Bidang</th>
                                        <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide text-center">Total Staff</th>
                                        <th class="pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departments as $dept)
                                        <tr class="table-row-hover">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="dept-icon-box bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-building"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $dept->name }}</div>
                                                        <div class="small text-muted d-none d-md-block">ID: {{ str_pad($dept->id ?? $loop->iteration, 3, '0', STR_PAD_LEFT) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle rounded-pill px-3 py-2 fw-medium">
                                                    <i class="bi bi-people-fill me-1"></i> {{ $dept->users_count }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light text-primary hover-elevate" data-bs-toggle="modal" data-bs-target="#editDeptModal{{ $dept->id }}" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    
                                                    <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang ini? Ini akan berdampak pada akses dokumen milik staff di bidang ini.')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light text-danger hover-elevate" title="Hapus">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <div class="text-muted d-flex flex-column align-items-center">
                                                    <i class="bi bi-inbox fs-1 mb-2 opacity-50"></i>
                                                    <p class="mb-0 fw-medium">Belum ada bidang yang terdaftar</p>
                                                    <small>Tambahkan bidang baru menggunakan form di sebelah kiri.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($departments as $dept)
    <div class="modal fade" id="editDeptModal{{ $dept->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('admin.departments.update', $dept->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-pencil"></i>
                            </div>
                            Edit Bidang
                        </h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body px-4 py-4">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">Nama Bidang</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-building"></i></span>
                                <input type="text" name="name" class="form-control md-input" value="{{ $dept->name }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-dark md-btn px-5"><i class="bi bi-save2 me-2"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <style>
        :root {
            --md-radius: 16px;
            --md-shadow-rest: 0 2px 12px rgba(0, 0, 0, 0.04);
            --md-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.08);
            --google-blue: #1a73e8;
            --google-blue-focus: rgba(26, 115, 232, 0.15);
        }
        .tracking-wide { letter-spacing: 0.5px; }
        .md-card { border-radius: var(--md-radius); box-shadow: var(--md-shadow-rest); transition: box-shadow 0.3s ease; }
        .md-card:hover { box-shadow: var(--md-shadow-hover); }
        .input-group-custom { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 14px; color: #5f6368; z-index: 10; }
        .md-input { padding-left: 40px; height: 46px; border-radius: 8px; border: 1px solid #dadce0; font-size: 14px; color: #202124; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); background-color: #fff; }
        .md-input:focus { border-color: var(--google-blue); box-shadow: 0 0 0 3px var(--google-blue-focus); outline: none; }
        .md-input::placeholder { color: #9aa0a6; }
        .md-btn { border-radius: 8px; transition: all 0.2s ease; box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15); }
        .md-btn:hover { transform: translateY(-1px); box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15); }
        .md-btn:active { transform: translateY(0); box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15); }
        .table > :not(caption) > * > * { border-bottom-color: #f1f3f4; }
        .table-row-hover { transition: background-color 0.2s ease; }
        .table-row-hover:hover { background-color: #f8f9fa; }
        .table-row-hover:hover .dept-icon-box { background-color: #e8f0fe !important; color: var(--google-blue) !important; transition: all 0.2s ease; }
        .dept-icon-box { width: 40px; height: 40px; font-size: 18px; transition: all 0.2s ease; }
        [class*="stagger-"] { opacity: 0; animation: slideUpFade 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .stagger-1 { animation-delay: 0.0s; } .stagger-2 { animation-delay: 0.1s; } .stagger-3 { animation-delay: 0.2s; }
        @keyframes slideUpFade { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>
@endsection