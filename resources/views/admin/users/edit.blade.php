@extends('layouts.admin')

@section('content')
    <div class="container-fluid px-0">
        <div class="mb-4 stagger-1">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-secondary hover-primary d-inline-flex align-items-center fw-medium">
                <i class="bi bi-arrow-left me-2"></i> {{ __('Back to User Management') }}
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 stagger-2">
                <div class="card md-card border-0 overflow-hidden">
                    <div class="card-header bg-white border-bottom-0 pt-5 pb-3 px-4 px-md-5 position-relative">
                        <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 4px;"></div>
                        
                        <div class="d-flex align-items-sm-center flex-column flex-sm-row">
                            <div class="avatar-circle-lg bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center mb-3 mb-sm-0 me-sm-4 shadow-sm">
                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1 text-dark">{{ __('Edit Profile') }}</h4>
                                <p class="text-secondary mb-0 font-monospace"><i class="bi bi-at opacity-50"></i>{{ $user->username }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-4 px-md-5 pb-5 pt-4">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf @method('PUT')

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Full Name') }}</label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-person"></i></span>
                                        <input type="text" name="full_name" class="form-control md-input" value="{{ $user->full_name }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Username') }}</label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-at"></i></span>
                                        <input type="text" name="username" class="form-control md-input" value="{{ $user->username }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Email Address') }}</label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" class="form-control md-input" value="{{ $user->email }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Department') }}</label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-building"></i></span>
                                        <select name="department_id" class="form-select md-input">
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Role') }}</label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                        <select name="role_level" class="form-select md-input">
                                            <option value="employee" {{ $user->role_level == 'employee' ? 'selected' : '' }}>Employee</option>
                                            <option value="manager" {{ $user->role_level == 'manager' ? 'selected' : '' }}>Manager</option>
                                            <option value="admin" {{ $user->role_level == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="p-4 bg-light rounded-4 border border-light">
                                        <label class="form-label small fw-bold text-dark text-uppercase tracking-wide mb-2 d-flex align-items-center">
                                            <i class="bi bi-shield-key text-primary me-2 fs-5"></i> {{ __('Security Update') }}
                                        </label>
                                        <p class="text-secondary small mb-3">{{ __('Enter a new password below only if you wish to change it. Leave blank to retain the current password.') }}</p>
                                        
                                        <div class="input-group-custom">
                                            <span class="input-icon"><i class="bi bi-key"></i></span>
                                            <input type="password" name="password" class="form-control md-input" placeholder="•••••••• (Optional)">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-2">
                                    <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">{{ __('Account Status') }}</h6>
                                            <p class="small text-muted mb-0">{{ __('If disabled, the user will lose access to the system immediately.') }}</p>
                                        </div>
                                        <div class="form-check form-switch ms-3 mb-0">
                                            <input class="form-check-input md-switch" type="checkbox" name="is_active" id="editActiveSwitch" style="width: 46px; height: 24px;" {{ $user->is_active ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 text-light">
                            
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light fw-medium px-4">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn btn-primary md-btn px-5 d-flex align-items-center">
                                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> {{ __('Save Changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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