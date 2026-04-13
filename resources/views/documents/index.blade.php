@extends('layouts.admin')

@section('content')
    <div class="container-fluid animate__animated animate__fadeIn">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">{{ __('Documents') }}</h3>
                <p class="text-muted small mb-0">{{ __('Manage and share company files securely') }}</p>
            </div>
            <button class="btn btn-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i> {{ __('Upload File') }}
            </button>
        </div>

        <!-- Stats summary (Optional) -->
        @if (auth()->user()->role_level === 'admin')
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-shield-lock-fill fs-4 me-3"></i>
                <div>
                    <strong>{{ __('Administrator Mode') }}:</strong>
                    {{ __('You can see and manage all documents across all departments.') }}
                </div>
            </div>
        @endif

        <!-- Documents Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small fw-bold">
                            <tr>
                                <th class="ps-4" style="width: 40%;">{{ __('Title') }}</th>
                                <th>{{ __('Owner') }}</th>
                                <th>{{ __('Size') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th class="text-end pe-4">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $icon = 'file-earmark-text';
                                                $color = 'text-primary';
                                                if ($doc->extension == 'pdf') {
                                                    $icon = 'file-earmark-pdf';
                                                    $color = 'text-danger';
                                                } elseif (in_array($doc->extension, ['jpg', 'png', 'jpeg'])) {
                                                    $icon = 'file-earmark-image';
                                                    $color = 'text-success';
                                                } elseif ($doc->extension == 'zip') {
                                                    $icon = 'file-earmark-zip';
                                                    $color = 'text-warning';
                                                }
                                            @endphp
                                            <i class="bi {{ $icon }} {{ $color }} fs-3 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $doc->title }}</div>
                                                <div class="text-muted small text-uppercase">{{ $doc->extension }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-normal">
                                            <i class="bi bi-person me-1"></i> {{ $doc->owner->full_name }}
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        {{ number_format($doc->file_size / 1024, 1) }} KB
                                    </td>
                                    <td class="small text-muted">
                                        {{ $doc->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <!-- Download: Everyone who can see the row can download -->
                                            <a href="{{ route('docs.download', $doc->id) }}"
                                                class="btn btn-sm btn-outline-secondary" title="{{ __('Download') }}">
                                                <i class="bi bi-download"></i>
                                            </a>

                                            <!-- Share: Only Owner can share -->
                                            @if ($doc->owner_id == auth()->id())
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                    data-bs-target="#shareModal{{ $doc->id }}"
                                                    title="{{ __('Share') }}">
                                                    <i class="bi bi-share"></i>
                                                </button>
                                            @endif

                                            <!-- Edit/Delete: Only Owner or Admin -->
                                            @if ($doc->owner_id == auth()->id() || auth()->user()->role_level == 'admin')
                                                <a href="{{ route('docs.edit', $doc->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('docs.destroy', $doc->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-folder2-open display-4 text-muted opacity-25"></i>
                                        <p class="text-muted mt-3">{{ __('No documents found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($documents->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade animate__animated animate__fadeIn" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('docs.store') }}" method="POST" enctype="multipart/form-data"
                class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-up me-2"></i>
                        {{ __('Upload New Document') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label
                            class="form-label fw-bold small text-uppercase text-muted">{{ __('Document Title') }}</label>
                        <input type="text" name="title" class="form-control form-control-lg"
                            placeholder="e.g. Project Plan 2026" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Select File') }}</label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text small">{{ __('Max size: 50MB. PDF, Word, Excel, Images.') }}</div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">{{ __('Upload Now') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Share Modals (Generated only for Owned Documents) -->
    @foreach ($documents as $doc)
        @if ($doc->owner_id == auth()->id())
            <div class="modal fade" id="shareModal{{ $doc->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('docs.share', $doc->id) }}" method="POST"
                        class="modal-content border-0 shadow">
                        @csrf
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title fw-bold"><i class="bi bi-share me-2"></i> {{ __('Share') }}:
                                {{ $doc->title }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-bold small text-uppercase text-muted">{{ __('Select Colleague') }}</label>
                                <select name="user_id" class="form-select select2" required>
                                    <option value="">{{ __('Choose a user...') }}</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->full_name }} (@ {{ $u->username }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label
                                    class="form-label fw-bold small text-uppercase text-muted">{{ __('Access Level') }}</label>
                                <select name="access_level" class="form-select">
                                    <option value="read">{{ __('Read Only (Download)') }}</option>
                                    <option value="write">{{ __('Editor (Can Edit Metadata)') }}</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit"
                                    class="btn btn-info text-white fw-bold">{{ __('Grant Access') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
@endsection
