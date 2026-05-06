<div class="d-flex align-items-center mb-4 border-bottom pb-3">
    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
        <i class="bi bi-folder2-open fs-5"></i>
    </div>
    <div>
        <h4 class="fw-bold mb-0 text-dark">{{ $folder->name }}</h4>
        <div class="text-secondary small">Bidang: {{ $folder->department->name ?? 'Umum' }} &bull; {{ $documents->count() }} Item ditemukan</div>
    </div>
</div>

@if($documents->count() > 0)
<div class="table-responsive">
    <table class="table table-hover table-borderless align-middle mb-0">
        <thead class="border-bottom border-light bg-light bg-opacity-50">
            <tr>
                <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide" style="width: 40%;">Nama File</th>
                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">Pemilik</th>
                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">Visibilitas</th>
                <th class="text-end pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $doc)
                @php
                    $ext = strtolower($doc->extension);
                    $icon = 'bi-file-earmark-text-fill'; $colorClass = 'bg-primary text-primary';
                    if ($ext == 'pdf') { $icon = 'bi-file-earmark-pdf-fill'; $colorClass = 'bg-danger text-danger'; }
                    elseif (in_array($ext, ['jpg', 'png', 'jpeg'])) { $icon = 'bi-file-earmark-image-fill'; $colorClass = 'bg-success text-success'; }
                    elseif (in_array($ext, ['zip', 'rar'])) { $icon = 'bi-file-earmark-zip-fill'; $colorClass = 'bg-warning text-warning'; }
                    elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) { $icon = 'bi-file-earmark-spreadsheet-fill'; $colorClass = 'bg-success text-success'; }
                    elseif (in_array($ext, ['ppt', 'pptx'])) { $icon = 'bi-file-earmark-slides-fill'; $colorClass = 'bg-warning text-warning'; }
                    
                    $editableExts = ['doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx'];
                    $isEditable = in_array(strtolower($doc->extension), $editableExts);
                    $isOwner = $doc->owner_id == auth()->id();
                    $hasWriteAccess = $doc->permissions->where('user_id', auth()->id())->where('access_level', 'write')->isNotEmpty();
                @endphp
                <tr class="table-row-hover border-bottom border-light">
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center">
                            <div class="file-icon-box {{ $colorClass }} bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:40px; height:40px;">
                                <i class="{{ $icon }} fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <div class="fw-semibold text-dark text-truncate">{{ $doc->title }}</div>
                                <div class="small text-secondary mt-1">{{ $doc->created_at->format('d M Y') }} &bull; {{ $doc->file_size == 0 ? '-' : number_format($doc->file_size / 1024, 1) . ' KB' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle-sm bg-secondary bg-opacity-10 text-secondary fw-bold rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                {{ strtoupper(substr($doc->owner->full_name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-dark small fw-medium">{{ $doc->owner->full_name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="py-3">
                        @if($doc->is_public)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle" style="font-size: 0.65rem;"><i class="bi bi-globe me-1"></i> Publik</span>
                        @elseif($doc->permissions->count() == 0)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle" style="font-size: 0.65rem;"><i class="bi bi-lock-fill me-1"></i> Privat</span>
                        @else
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle" style="font-size: 0.65rem;"><i class="bi bi-people-fill me-1"></i> Dibagikan ({{ $doc->permissions->count() }})</span>
                        @endif
                    </td>
                    <td class="text-end pe-4 py-3">
                        <div class="d-flex justify-content-end gap-1">
                            @if($doc->google_file_id && $isEditable)
                                <a href="{{ route('docs.editor', $doc->id) }}" class="btn btn-sm btn-primary text-white shadow-sm" title="Live Edit"><i class="bi bi-pencil-square"></i></a>
                            @elseif($doc->google_file_id && !$isEditable)
                                <a href="https://drive.google.com/file/d/{{ $doc->google_file_id }}/view" target="_blank" class="btn btn-sm btn-info text-white shadow-sm" title="Lihat"><i class="bi bi-eye-fill"></i></a>
                            @endif
                            <a href="{{ route('docs.download', $doc->id) }}" class="btn btn-sm btn-light text-secondary shadow-sm" title="Download"><i class="bi bi-download"></i></a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="text-center py-5">
    <div class="mb-3 opacity-50 d-inline-block p-4 bg-light rounded-circle">
        <i class="bi bi-folder-x fs-1 text-secondary"></i>
    </div>
    <h5 class="fw-bold text-dark mb-1">Folder Kosong</h5>
    <p class="text-secondary small">Belum ada dokumen yang disimpan di folder ini.</p>
</div>
@endif