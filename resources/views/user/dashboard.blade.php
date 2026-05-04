@extends('layouts.admin')

@section('content')
    <div class="animate__animated animate__fadeIn">
        <div class="row mb-4">
            <div class="col-md-8">
                <!-- Tambah fs-4 fs-md-3 agar ukuran teks responsif -->
                <h3 class="fw-bold fs-4 fs-md-3">Welcome back, {{ auth()->user()->full_name }}</h3>
                <p class="text-muted">Workspace: <span
                        class="badge bg-info text-dark">{{ auth()->user()->department->name }}</span></p>
            </div>
            <!-- Tambah mt-3 mt-md-0 dan d-grid d-md-block agar tombol full di HP tapi normal di Desktop -->
            <div class="col-md-4 text-md-end mt-3 mt-md-0 d-grid d-md-block">
                <a href="{{ route('docs.index') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Upload</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-folder2-open fs-2 text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="fw-bold mb-0">{{ $stats['my_docs_count'] }}</h4>
                            <div class="text-muted small">My Documents</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-share fs-2 text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="fw-bold mb-0">{{ $stats['shared_with_me'] }}</h4>
                            <div class="text-muted small">Shared With Me</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-envelope fs-2 text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="fw-bold mb-0">{{ $stats['unread_messages'] }}</h4>
                            <div class="text-muted small">Messages</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tambah g-4 agar saat di HP ada jarak (margin) antar Card -->
        <div class="row mt-4 g-4">
            <!-- Dept Shared Docs -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold border-0 pt-3">
                        Recent Department Files
                    </div>
                    <div class="card-body">
                        @forelse($stats['dept_docs'] as $doc)
                            <div class="d-flex align-items-center mb-3 p-2 border-bottom">
                                <i class="bi bi-file-earmark-{{ in_array($doc->extension, ['pdf', 'jpg']) ? 'pdf' : 'text' }} text-primary fs-4 flex-shrink-0"></i>
                                
                                <!-- Tambah flex-grow-1, overflow-hidden agar judul super panjang tidak merusak layar HP -->
                                <div class="ms-3 flex-grow-1 overflow-hidden" style="min-width: 0;">
                                    <div class="fw-bold small text-truncate" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.7rem;">Uploaded by:
                                        {{ $doc->owner->full_name }}</div>
                                </div>
                                
                                <!-- Tambah flex-shrink-0 agar tombol View tidak gepeng di HP kecil -->
                                <div class="ms-2 flex-shrink-0">
                                    <a href="{{ route('docs.download', $doc->id) }}" class="btn btn-sm btn-light">View</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">No recent files in your department.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Personal Activity -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold border-0 pt-3">My Recent Activity</div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach ($stats['my_recent_activities'] as $log)
                                <div class="list-group-item px-0 border-0">
                                    <!-- Tambah gap-2 dan ubah align agar teks panjang turun ke bawah dengan cantik -->
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <span class="small fw-bold text-break">{{ $log->action }}</span>
                                        <span class="text-muted small text-nowrap flex-shrink-0"
                                            style="font-size: 0.65rem;">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection