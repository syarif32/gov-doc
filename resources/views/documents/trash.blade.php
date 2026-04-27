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
            <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                <i class="bi bi-trash3 fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ __('Trash Center') }}</h2>
                <p class="text-secondary small mb-0">{{ __('Dokumen yang dihapus akan diamankan di sini sebelum dilenyapkan secara permanen.') }}</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 stagger-2">
        <div class="card-body p-3">
            <form action="{{ route('docs.trash') }}" method="GET" class="row g-2">
                <div class="col-md-6">
                    <div class="input-group-custom position-relative">
                        <span class="input-icon position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #5f6368;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 bg-light" style="padding-left: 40px !important; border-radius: 10px;" placeholder="{{ __('Cari dokumen di tong sampah...') }}" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="tanggal" class="form-control border-0 bg-light" style="border-radius: 10px;" value="{{ request('tanggal') }}" title="{{ __('Pilih Tanggal Dihapus') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100" style="border-radius: 10px;">
                        <i class="bi bi-filter me-1"></i> {{ __('Filter') }}
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('docs.trash') }}" class="btn btn-outline-secondary w-100" style="border-radius: 10px;">
                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card md-card border-0 shadow-sm rounded-4 mb-4 stagger-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0">
                    <thead class="border-bottom border-light bg-light bg-opacity-50">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide" style="width: 40%;">{{ __('File Name') }}</th>
                            <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Deleted At') }}</th>
                            <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Size') }}</th>
                            <th class="text-end pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            @php
                                $ext = strtolower($doc->extension);
                                // Warna icon dibuat sedikit redup untuk memberi kesan "sampah"
                                $icon = 'bi-file-earmark-text-fill'; $colorClass = 'bg-secondary text-secondary'; 
                                if ($ext == 'pdf') { $icon = 'bi-file-earmark-pdf-fill'; }
                                elseif (in_array($ext, ['jpg', 'png', 'jpeg'])) { $icon = 'bi-file-earmark-image-fill'; }
                                elseif (in_array($ext, ['zip', 'rar'])) { $icon = 'bi-file-earmark-zip-fill'; }
                                elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) { $icon = 'bi-file-earmark-spreadsheet-fill'; }
                                elseif (in_array($ext, ['ppt', 'pptx'])) { $icon = 'bi-file-earmark-slides-fill'; }
                            @endphp
                            <tr class="table-row-hover" style="opacity: 0.85;"> 
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon-box {{ $colorClass }} bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                                            <i class="{{ $icon }} fs-5"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-semibold text-dark text-truncate text-decoration-line-through" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-0 text-uppercase" style="font-size: 0.65rem;">{{ $ext }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-medium text-danger small mb-1"><i class="bi bi-clock-history me-1"></i> {{ $doc->deleted_at->diffForHumans() }}</div>
                                    <div class="text-secondary" style="font-size: 0.7rem;">{{ $doc->deleted_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td class="py-3 text-secondary font-monospace small">
                                    {{ $doc->file_size == 0 ? '-' : number_format($doc->file_size / 1024, 1) . ' KB' }}
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        
                                        <form action="{{ route('docs.restore', $doc->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light text-success border border-success-subtle hover-elevate shadow-sm px-3 fw-medium" data-bs-toggle="tooltip" title="Kembalikan file ini ke penyimpanan aktif">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                            </button>
                                        </form>

                                        <form action="{{ route('docs.forceDelete', $doc->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: File ini akan dimusnahkan secara permanen dari server dan Google Drive. Anda yakin?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger hover-elevate shadow-sm px-3 fw-medium" data-bs-toggle="tooltip" title="Hapus selama-lamanya">
                                                <i class="bi bi-trash3-fill me-1"></i> Permanen
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-secondary mb-3 opacity-50"><i class="bi bi-trash fs-1" style="font-size: 3rem !important;"></i></div>
                                    <h6 class="fw-bold text-dark">{{ __('Tong Sampah Kosong') }}</h6>
                                    <p class="text-secondary small mb-0">{{ __('Lingkungan kerja Anda bersih! Tidak ada dokumen yang dihapus atau ditemukan dari pencarian Anda.') }}</p>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

<style>
    .table-row-hover:hover { background-color: #f8f9fa; }
    .pagination { margin-bottom: 0; }
    .page-item.active .page-link { background-color: #212529; border-color: #212529; }
    .page-link { color: #212529; padding: 0.5rem 1rem; border-radius: 8px; margin: 0 3px; border: 1px solid #dee2e6; }
    .page-item:not(.active) .page-link:hover { background-color: #f8f9fa; color: #000; }
    .page-item.disabled .page-link { background-color: transparent; }
</style>
@endsection