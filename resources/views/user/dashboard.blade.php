@extends('layouts.admin')

@section('content')
    <div class="animate__animated animate__fadeIn">
        <div class="row mb-4">
            <div class="col-md-8">
                <h3 class="fw-bold">Welcome back, {{ auth()->user()->full_name }}</h3>
                <p class="text-muted">Workspace: <span
                        class="badge bg-info text-dark">{{ auth()->user()->department->name }}</span></p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('docs.index') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Upload</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4 bg-white">
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
                <div class="card border-0 shadow-sm p-4 bg-white">
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
                <div class="card border-0 shadow-sm p-4 bg-white">
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

        <div class="row mt-4">
            <!-- Dept Shared Docs -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold border-0 pt-3">
                        Recent Department Files
                    </div>
                    <div class="card-body">
                        @forelse($stats['dept_docs'] as $doc)
                            <div class="d-flex align-items-center mb-3 p-2 border-bottom">
                                <i
                                    class="bi bi-file-earmark-{{ in_array($doc->extension, ['pdf', 'jpg']) ? 'pdf' : 'text' }} text-primary fs-4"></i>
                                <div class="ms-3">
                                    <div class="fw-bold small">{{ $doc->title }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Uploaded by:
                                        {{ $doc->owner->full_name }}</div>
                                </div>
                                <div class="ms-auto">
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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold border-0 pt-3">My Recent Activity</div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach ($stats['my_recent_activities'] as $log)
                                <div class="list-group-item px-0 border-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="small fw-bold">{{ $log->action }}</span>
                                        <span class="text-muted small"
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
