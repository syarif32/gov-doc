@extends('layouts.admin')

@section('content')
    <div class="dashboard-container">
        <div class="d-flex align-items-center mb-4 pb-2 stagger-1">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                <i class="bi bi-person-badge fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ __('My Profile') }}</h2>
                <p class="text-secondary small mb-0">{{ __('Manage your personal information and security settings') }}</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 stagger-2">
                <div class="card md-card border-0 h-100 overflow-hidden position-relative">
                    <div class="bg-light w-100 position-absolute top-0 start-0" style="height: 120px; z-index: 0; border-bottom: 1px solid #f1f3f4;"></div>
                    
                    <div class="card-body text-center p-4 position-relative" style="z-index: 1;">
                        <div class="avatar-circle-xl bg-primary text-white d-flex align-items-center justify-content-center fw-bold mx-auto shadow"
                            style="width: 100px; height: 100px; font-size: 2.5rem; margin-top: 40px; border: 4px solid #fff;">
                            {{ strtoupper(substr($user->full_name, 0, 1)) }}
                        </div>
                        
                        <h4 class="fw-bold text-dark mt-3 mb-1">{{ $user->full_name }}</h4>
                        <p class="text-secondary font-monospace small mb-3"><i class="bi bi-at opacity-50"></i>{{ $user->username }}</p>
                        
                        @php
                            $roleClass = $user->role_level === 'admin' ? 'bg-purple-subtle text-purple border-purple-subtle' : 
                                        ($user->role_level === 'manager' ? 'bg-warning bg-opacity-10 text-warning border-warning-subtle' : 
                                        'bg-primary bg-opacity-10 text-primary border-primary-subtle');
                        @endphp
                        <span class="badge {{ $roleClass }} border rounded-pill px-4 py-2 fw-medium tracking-wide text-uppercase mb-4">
                            {{ $user->role_level }}
                        </span>
                        
                        <hr class="text-light my-4">
                        
                        <div class="text-start px-3">
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-light text-secondary rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <div class="small text-muted text-uppercase tracking-wide" style="font-size: 0.65rem; font-weight: 600;">{{ __('Department') }}</div>
                                    <div class="fw-medium text-dark">{{ $user->department->name ?? __('Not Assigned') }}</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <div class="bg-light text-secondary rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div>
                                    <div class="small text-muted text-uppercase tracking-wide" style="font-size: 0.65rem; font-weight: 600;">{{ __('Member Since') }}</div>
                                    <div class="fw-medium text-dark">{{ $user->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 stagger-3">
                
                <ul class="nav nav-tabs md-tabs border-0 mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold d-flex align-items-center px-4" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personalInfo" type="button" role="tab">
                            <i class="bi bi-person-lines-fill me-2 fs-5"></i> {{ __('Personal Info') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold d-flex align-items-center px-4" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                            <i class="bi bi-shield-lock-fill me-2 fs-5"></i> {{ __('Security') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                    
                    <div class="tab-pane fade show active" id="personalInfo" role="tabpanel">
                        <div class="card md-card border-0">
                            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold text-dark mb-0">{{ __('Basic Information') }}</h5>
                                <p class="text-secondary small mt-1">{{ __('Update your account details and preferences.') }}</p>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('profile.update') }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-4">
                                        <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Full Name') }}</label>
                                        <div class="input-group-custom">
                                            <span class="input-icon"><i class="bi bi-person"></i></span>
                                            <input type="text" name="full_name" class="form-control md-input" value="{{ $user->full_name }}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4 mb-4">
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
                                    </div>
                                    
                                    <div class="mb-5">
                                        <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Interface Language') }}</label>
                                        <div class="input-group-custom">
                                            <span class="input-icon"><i class="bi bi-globe2"></i></span>
                                            <select name="preferred_lang" class="form-select md-input">
                                                <option value="ru" {{ $user->preferred_lang == 'ru' ? 'selected' : '' }}>Indonesia (IDN)</option>
                                                <option value="en" {{ $user->preferred_lang == 'en' ? 'selected' : '' }}>English (EN)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end border-top pt-4">
                                        <button type="submit" class="btn btn-primary md-btn px-5 d-inline-flex align-items-center">
                                            <i class="bi bi-check2-circle me-2"></i> {{ __('Save Preferences') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="card md-card border-0">
                            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                                <h5 class="fw-bold text-dark mb-0">{{ __('Update Password') }}</h5>
                                <p class="text-secondary small mt-1">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('profile.password') }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-4">
                                        <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Current Password') }}</label>
                                        <div class="input-group-custom">
                                            <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                                            <input type="password" name="current_password" class="form-control md-input" required placeholder="••••••••">
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 bg-light rounded-3 mb-4 border">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('New Password') }}</label>
                                                <div class="input-group-custom">
                                                    <span class="input-icon"><i class="bi bi-key"></i></span>
                                                    <input type="password" name="password" class="form-control md-input" required placeholder="••••••••">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Confirm Password') }}</label>
                                                <div class="input-group-custom">
                                                    <span class="input-icon"><i class="bi bi-check2-all"></i></span>
                                                    <input type="password" name="password_confirmation" class="form-control md-input" required placeholder="••••••••">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center alert alert-warning border-0 bg-warning bg-opacity-10 py-3 px-4 rounded-3 mb-4">
                                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-3"></i>
                                        <div class="small text-dark">
                                            {{ __('Changing your password will automatically log you out of all other active sessions across devices.') }}
                                        </div>
                                    </div>
                                    
                                    <div class="text-end border-top pt-4">
                                        <button type="submit" class="btn btn-danger md-btn px-5 d-inline-flex align-items-center">
                                            <i class="bi bi-shield-check me-2"></i> {{ __('Change Password') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Google Style Tabs (Underline) */
        .md-tabs {
            border-bottom: 1px solid #dadce0 !important;
            gap: 8px;
        }
        
        .md-tabs .nav-item {
            margin-bottom: -1px;
        }
        
        .md-tabs .nav-link {
            border: none !important;
            border-bottom: 3px solid transparent !important;
            color: #5f6368;
            padding: 12px 16px;
            border-radius: 8px 8px 0 0;
            transition: all 0.2s ease;
            background: transparent;
        }
        
        .md-tabs .nav-link:hover:not(.active) {
            background-color: #f1f3f4;
            color: #202124;
            border-bottom-color: #dadce0 !important;
        }
        
        .md-tabs .nav-link.active {
            color: #1a73e8 !important;
            border-bottom-color: #1a73e8 !important;
            background-color: transparent !important;
        }

        /* Profile Specific Colors */
        .bg-purple-subtle { background-color: #f3e8ff; }
        .text-purple { color: #7e22ce; }
        .border-purple-subtle { border-color: #d8b4fe !important; }

        /* Animation specific to tabs content */
        .tab-content > .tab-pane {
            display: none;
        }
        .tab-content > .active {
            display: block;
            animation: slideUpFade 0.4s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
    </style>
@endsection