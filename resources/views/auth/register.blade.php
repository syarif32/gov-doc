@extends('layouts.auth')

@section('title', __('register_title'))

@section('content')

    <span class="auth-badge">{{ __('new_account') }}</span>
    <h3>{{ __('create_account') }}</h3>
    <p class="auth-subtitle">{{ __('register_subtitle') }}</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3 small">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="field-label">{{ __('full_name') }}</label>
            <div class="input-icon-wrap">
                <i class="bi bi-person field-icon"></i>
                <input type="text" name="full_name" class="form-control"
                    placeholder="{{ __('full_name_placeholder') }}" value="{{ old('full_name') }}" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="field-label">{{ __('username') }}</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-at field-icon"></i>
                    <input type="text" name="username" class="form-control"
                        placeholder="{{ __('username_reg_placeholder') }}" value="{{ old('username') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="field-label">{{ __('email') }}</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-envelope field-icon"></i>
                    <input type="email" name="email" class="form-control"
                        placeholder="{{ __('email_placeholder') }}" value="{{ old('email') }}" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="field-label">{{ __('department') }}</label>
            <div class="input-icon-wrap">
                <i class="bi bi-building field-icon"></i>
                <select name="department_id" class="form-select" required>
                    <option value="" disabled selected>{{ __('select_department') }}</option>
                    @foreach (\App\Models\Department::all() as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="field-label">{{ __('password') }}</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-lock field-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="field-label">{{ __('confirm_password') }}</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-lock-fill field-icon"></i>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••"
                        required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="field-label">{{ __('interface_language') }}</label>
            <div class="input-icon-wrap">
                <i class="bi bi-globe field-icon"></i>
                <select name="preferred_lang" class="form-select">
                    <!-- <option value="tk" {{ old('preferred_lang') == 'tk' ? 'selected' : '' }}>Türkmençe</option> -->
                    <option value="ru" {{ old('preferred_lang') == 'ru' ? 'selected' : '' }}>Indonesia</option>
                    <option value="en" {{ old('preferred_lang') == 'en' ? 'selected' : '' }}>English</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-auth-primary">{{ __('create_account_btn') }}</button>

        <p class="auth-divider">{{ __('already_registered') }}</p>
        <p class="auth-switch">
            <a href="{{ route('login') }}">{{ __('sign_in_here') }} →</a>
        </p>
    </form>

@endsection
