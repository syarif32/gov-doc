@extends('layouts.admin')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="dashboard-container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 stagger-1">
        <div class="d-flex align-items-center mb-3 mb-md-0">
            <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                <i class="bi bi-tools fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ __('System Maintenance') }}</h2>
                <p class="text-secondary small mb-0">{{ __('Pusat kendali Administrator untuk menjaga performa dan kebersihan server.') }}</p>
            </div>
        </div>
    </div>

    <div class="row g-4 stagger-2">
        
        <!-- KOLOM KIRI: CACHE & OPTIMIZATION -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-cpu text-primary me-2"></i> Aplikasi & Cache</h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-secondary small mb-4">Gunakan alat ini jika aplikasi terasa lambat, atau ketika Anda baru saja melakukan perubahan pada file konfigurasi (.env) namun tidak terbaca oleh sistem.</p>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border border-light hover-elevate">
                            <div>
                                <div class="fw-semibold text-dark small">Bersihkan Cache Total</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Menghapus semua cache views, konfigurasi, dan rute.</div>
                            </div>
                            <form action="{{ route('admin.maintenance.cache', 'optimize') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger px-3 rounded-pill" onclick="return confirm('Bersihkan seluruh cache aplikasi?')"><i class="bi bi-trash3 me-1"></i> Clear</button>
                            </form>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border border-light hover-elevate">
                            <div>
                                <div class="fw-semibold text-dark small">Rebuild & Optimize</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Membangun ulang struktur cache agar aplikasi berjalan lebih cepat (Mode Production).</div>
                            </div>
                            <form action="{{ route('admin.maintenance.cache', 'rebuild') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success px-3 rounded-pill"><i class="bi bi-lightning-charge me-1"></i> Optimize</button>
                            </form>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 border border-light hover-elevate">
                            <div>
                                <div class="fw-semibold text-dark small">Bersihkan Route Cache</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Lakukan ini jika terjadi error "Route not defined" setelah update.</div>
                            </div>
                            <form action="{{ route('admin.maintenance.cache', 'route') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary px-3 rounded-pill">Clear Route</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: STORAGE & LOGS -->
        <div class="col-lg-5">
            <div class="row g-4">
                
                <!-- KARTU STORAGE SAMPAH -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom border-light p-4">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-hdd-network text-warning me-2"></i> Local Storage Cleanup</h6>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-folder-x fs-3"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ number_format($tempSize / 1048576, 2) }} <span class="fs-6 text-muted">MB</span></h3>
                            <p class="text-secondary small mb-3">Kapasitas digunakan oleh potongan file upload yang gagal/nyangkut di server.</p>
                            
                            <form action="{{ route('admin.maintenance.temp') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning text-dark w-100 md-btn fw-semibold" onclick="return confirm('Hapus semua file sisa upload sementara di server?')">
                                    <i class="bi bi-stars me-2"></i> Bersihkan Storage
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- KARTU SYSTEM LOGS -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom border-light p-4">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-journal-code text-info me-2"></i> System Logs</h6>
                        </div>
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="fw-bold mb-0">{{ number_format($logSize / 1048576, 2) }} <span class="fs-6 text-muted">MB</span></h4>
                                <div class="text-secondary small mt-1">Ukuran File Log</div>
                            </div>
                            <form action="{{ route('admin.maintenance.logs') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-info rounded-pill px-4" onclick="return confirm('Hapus semua riwayat error log?')">
                                    <i class="bi bi-trash me-1"></i> Bersihkan Log
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
    .hover-elevate { transition: all 0.2s ease; }
    .hover-elevate:hover { transform: translateY(-3px); box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); background-color: #fff !important; }
</style>

@endsection