@extends('layouts.admin')

@section('content')
    <div class="dashboard-container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 stagger-1">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ __('User Management') }}</h2>
                    <p class="text-secondary small mb-0">{{ __('Manage accounts, roles, and system access') }}</p>
                </div>
            </div>
            <div>
                <button class="btn btn-primary md-btn d-flex align-items-center px-4 py-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill me-2 fs-5"></i> <span class="fw-semibold">{{ __('Add New User') }}</span>
                </button>
            </div>
        </div>

        <div class="card md-card border-0 stagger-2 h-100">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="border-bottom border-light bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Full Name') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Department') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Role') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Status') }}</th>
                                <th class="text-end pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="table-row-hover">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3">
                                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $user->full_name }}</div>
                                                <div class="text-secondary small font-monospace"><i class="bi bi-at opacity-50"></i>{{ $user->username }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border border-secondary-subtle px-2 py-1 rounded">
                                            <i class="bi bi-building me-1 opacity-50"></i> {{ $user->department->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @php
                                            $roleClass = $user->role_level === 'admin' ? 'bg-purple-subtle text-purple border-purple-subtle' : 
                                                        ($user->role_level === 'manager' ? 'bg-warning bg-opacity-10 text-warning border-warning-subtle' : 
                                                        'bg-secondary bg-opacity-10 text-secondary border-secondary-subtle');
                                        @endphp
                                        <span class="badge {{ $roleClass }} border rounded-pill px-3 py-1 fw-medium tracking-wide">
                                            {{ strtoupper($user->role_level) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @if ($user->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 fw-medium d-inline-flex align-items-center">
                                                <span class="pulse-dot-small bg-success me-2"></span> {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-medium d-inline-flex align-items-center">
                                                <i class="bi bi-x-circle me-1"></i> {{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-icon btn-light text-primary hover-elevate" data-bs-toggle="tooltip" title="{{ __('Edit User') }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-light text-danger hover-elevate" data-bs-toggle="tooltip" title="{{ __('Delete User') }}">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted d-flex flex-column align-items-center">
                                            <i class="bi bi-people fs-1 mb-2 opacity-50"></i>
                                            <p class="mb-0 fw-medium">{{ __('No users found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
                <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
                    <div class="pagination-custom d-flex justify-content-end">
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                @csrf
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        {{ __('Add New User') }}
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body px-4 py-4">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Full Name') }}</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-person"></i></span>
                                <input type="text" name="full_name" class="form-control md-input" required placeholder="e.g. John Doe">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Username') }}</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-at"></i></span>
                                <input type="text" name="username" class="form-control md-input" required placeholder="johndoe">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Email Address') }}</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control md-input" required placeholder="john@example.com">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Password') }}</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control md-input" required placeholder="••••••••">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Department') }}</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-building"></i></span>
                                <select name="department_id" class="form-select md-input" required>
                                    <option value="" disabled selected>{{ __('Select Dept...') }}</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('System Role') }}</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                <select name="role_level" class="form-select md-input" required>
                                    <option value="employee">Employee</option>
                                    <option value="manager">Manager</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 border">
                        <div class="form-check form-switch d-flex align-items-center mb-0">
                            <input class="form-check-input md-switch me-3" type="checkbox" name="is_active" id="activeSwitch" checked style="width: 40px; height: 20px;">
                            <label class="form-check-label fw-semibold text-dark" for="activeSwitch" style="cursor: pointer;">
                                {{ __('Activate Account Immediately') }}
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary md-btn px-5"><i class="bi bi-check2 me-1"></i> {{ __('Save User') }}</button>
                </div>
            </form>
        </div>
    </div>
    <style>
    /* Material Design Variables & Core */
    :root {
        --md-radius: 16px;
        --md-shadow-rest: 0 2px 12px rgba(0, 0, 0, 0.04);
        --md-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.08);
        --google-blue: #1a73e8;
        --google-blue-focus: rgba(26, 115, 232, 0.15);
        --purple-subtle: #f3e8ff;
        --purple: #7e22ce;
    }

    .tracking-wide { letter-spacing: 0.5px; }

    /* Component: Cards */
    .md-card {
        border-radius: var(--md-radius);
        box-shadow: var(--md-shadow-rest);
        transition: box-shadow 0.3s ease;
    }
    .md-card:hover { box-shadow: var(--md-shadow-hover); }

    /* Component: Form Inputs (Google standard) */
    .input-group-custom {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-icon {
        position: absolute;
        left: 14px;
        color: #5f6368;
        z-index: 10;
        font-size: 1.1rem;
    }
    .md-input {
        padding-left: 42px;
        height: 48px;
        border-radius: 10px;
        border: 1px solid #dadce0;
        font-size: 14px;
        color: #202124;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #fff;
    }
    .md-input:focus {
        border-color: var(--google-blue);
        box-shadow: 0 0 0 4px var(--google-blue-focus);
        outline: none;
    }
    /* Select fix for the icon */
    select.md-input { appearance: none; padding-right: 36px; }

    /* Component: Buttons */
    .md-btn {
        border-radius: 10px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
    }
    .md-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
    }
    .md-btn:active { transform: translateY(0); box-shadow: none; }
    
    .btn-icon {
        width: 36px; height: 36px;
        display: inline-flex; justify-content: center; align-items: center;
        border-radius: 8px; border: none;
    }
    .hover-elevate { transition: all 0.2s; }
    .hover-elevate:hover { transform: translateY(-2px); background: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .hover-primary:hover { color: var(--google-blue) !important; }

    /* Component: Switches */
    .md-switch:checked {
        background-color: var(--google-blue);
        border-color: var(--google-blue);
    }
    .md-switch:focus { box-shadow: 0 0 0 3px var(--google-blue-focus); }

    /* Avatars */
    .avatar-circle { width: 38px; height: 38px; font-size: 15px; }
    .avatar-circle-lg { width: 72px; height: 72px; font-size: 28px; }

    /* Table specifics */
    .table-row-hover { transition: background-color 0.2s; }
    .table-row-hover:hover { background-color: #f8f9fa; }
    .bg-purple-subtle { background-color: var(--purple-subtle); }
    .text-purple { color: var(--purple); }
    .border-purple-subtle { border-color: #d8b4fe !important; }

    /* Active Pulse */
    .pulse-dot-small {
        display: inline-block; width: 6px; height: 6px; border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.5);
        animation: pulse-soft 2s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes pulse-soft { to { box-shadow: 0 0 0 6px rgba(25, 135, 84, 0); } }

    /* Animations */
    [class*="stagger-"] { opacity: 0; animation: slideUpFade 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
    .stagger-1 { animation-delay: 0.0s; }
    .stagger-2 { animation-delay: 0.15s; }
    @keyframes slideUpFade { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Pagination override */
    .pagination-custom .pagination { margin-bottom: 0; gap: 4px; }
    .pagination-custom .page-link { border-radius: 8px; border: none; color: #5f6368; font-weight: 500; }
    .pagination-custom .page-item.active .page-link { background-color: #e8f0fe; color: #1a73e8; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection