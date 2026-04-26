@php
    // Logika penentuan icon dan warna berdasarkan ekstensi file
    $ext = strtolower($document->extension);
    $icon = 'bi-file-earmark-text-fill'; 
    $colorClass = 'bg-primary text-primary';

    if ($ext == 'pdf') { 
        $icon = 'bi-file-earmark-pdf-fill'; 
        $colorClass = 'bg-danger text-danger'; 
    } elseif (in_array($ext, ['jpg', 'png', 'jpeg'])) { 
        $icon = 'bi-file-earmark-image-fill'; 
        $colorClass = 'bg-success text-success'; 
    } elseif (in_array($ext, ['zip', 'rar'])) { 
        $icon = 'bi-file-earmark-zip-fill'; 
        $colorClass = 'bg-warning text-warning'; 
    } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) { 
        $icon = 'bi-file-earmark-spreadsheet-fill'; 
        $colorClass = 'bg-success text-success'; 
    } elseif (in_array($ext, ['ppt', 'pptx'])) { 
        $icon = 'bi-file-earmark-slides-fill'; 
        $colorClass = 'bg-warning text-warning'; 
    }
@endphp

<div class="card h-100 border-0 shadow-sm rounded-4 transition-all" style="transition: transform 0.2s, box-shadow 0.2s;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="file-icon-box {{ $colorClass }} bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                <i class="{{ $icon }} fs-2"></i>
            </div>
            <div class="text-end">
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1 text-uppercase" style="font-size: 0.7rem;">
                    {{ $ext }}
                </span>
                @if($document->file_size == 0)
                    <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-2 py-1 ms-1" style="font-size: 0.7rem;">
                        Baru
                    </span>
                @endif
            </div>
        </div>

        <h5 class="card-title fw-bold text-dark text-truncate mb-1" title="{{ $document->title }}">
            {{ $document->title }}
        </h5>
        <p class="card-text text-secondary small mb-4">
            <i class="bi bi-folder2 me-1"></i> {{ $document->folder->name ?? 'Unsorted' }}
        </p>

        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
            <div class="small text-secondary font-monospace">
                {{ $document->file_size == 0 ? '-' : number_format($document->file_size / 1024, 1) . ' KB' }}
            </div>
            <div class="small text-secondary fw-medium">
                {{ $document->created_at->format('d M Y') }}
            </div>
        </div>
    </div>

    <div class="card-footer bg-transparent border-top-0 pt-0 pb-4 px-4">
        <div class="d-flex justify-content-between gap-2">
            
            @if($document->google_file_id)
                <a href="{{ route('docs.editor', $document->id) }}" class="btn btn-sm btn-primary w-100 fw-medium shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Live Edit
                </a>
            @else
                <a href="{{ route('docs.download', $document->id) }}" class="btn btn-sm btn-light text-primary w-100 fw-medium border">
                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Download
                </a>
            @endif

            <div class="dropdown">
                <button class="btn btn-sm btn-light border text-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 text-sm">
                    @if ($document->owner_id == auth()->id())
                        <li>
                            <button class="dropdown-item d-flex align-items-center text-info py-2" data-bs-toggle="modal" data-bs-target="#shareModal{{ $document->id }}">
                                <i class="bi bi-share-fill me-2"></i> {{ __('Share Access') }}
                            </button>
                        </li>
                    @endif
                    <li>
                        <a class="dropdown-item d-flex align-items-center text-secondary py-2" href="{{ route('docs.edit', $document->id) }}">
                            <i class="bi bi-gear me-2"></i> {{ __('Edit Details') }}
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('docs.destroy', $document->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this document?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="dropdown-item d-flex align-items-center text-danger py-2">
                                <i class="bi bi-trash3-fill me-2"></i> {{ __('Delete') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
</style>