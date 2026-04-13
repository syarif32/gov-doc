@extends('layouts.admin')

@section('content')
    <div class="container animate__animated animate__fadeIn" style="max-width: 900px;">
        <h4 class="fw-bold mb-4 text-primary"><i class="bi bi-person-badge me-2"></i> {{ __('My Profile') }}</h4>

        <div class="row">
            <!-- Sidebar Info -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm text-center p-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($user->full_name, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->full_name }}</h5>
                    <p class="text-muted small mb-3">@ {{ $user->username }}</p>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-3">
                        {{ strtoupper($user->role_level) }}
                </div>
                <hr>
                <div class="text-start">
                    <div class="small text-muted text-uppercase fw-bold">{{ __('Department') }}</div>
                    <div class="mb-3 fw-bold">{{ $user->department->name ?? 'N/A' }}</div>

                    <div class="small text-muted text-uppercase fw-bold">{{ __('Member Since') }}</div>
                    <div class="fw-bold">{{ $user->created_at->format('d.m.Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Forms -->
        <div class="col-md-8">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs border-0 mb-3" id="profileTabs">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" data-bs-toggle="tab"
                        data-bs-target="#personalInfo">{{ __('Personal Info') }}</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" data-bs-toggle="tab"
                        data-bs-target="#security">{{ __('Security') }}</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab 1: Personal Info -->
                <div class="tab-pane fade show active animate__animated animate__fadeInUp" id="personalInfo">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">{{ __('Full Name') }}</label>
                                    <input type="text" name="full_name" class="form-control"
                                        value="{{ $user->full_name }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">{{ __('Username') }}</label>
                                        <input type="text" name="username" class="form-control"
                                            value="{{ $user->username }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">{{ __('Email') }}</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $user->email }}" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">{{ __('Interface Language') }}</label>
                                    <select name="preferred_lang" class="form-select">
                                        <option value="tk" {{ $user->preferred_lang == 'tk' ? 'selected' : '' }}>
                                            Türkmençe</option>
                                        <option value="ru" {{ $user->preferred_lang == 'ru' ? 'selected' : '' }}>
                                            Русский</option>
                                        <option value="en" {{ $user->preferred_lang == 'en' ? 'selected' : '' }}>
                                            English</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary px-4">{{ __('Update Profile') }}</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Security (Password) -->
                <div class="tab-pane fade animate__animated animate__fadeInUp" id="security">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">{{ __('Current Password') }}</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">{{ __('New Password') }}</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">{{ __('Confirm New Password') }}</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-danger px-4">{{ __('Change Password') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
