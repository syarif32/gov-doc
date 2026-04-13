@extends('layouts.admin')

@section('content')
    <div class="container" style="max-width: 700px;">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-primary">{{ __('Edit User') }}: {{ $user->username }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">{{ __('Full Name') }}</label>
                        <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">{{ __('Username') }}</label>
                            <input type="text" name="username" class="form-control" value="{{ $user->username }}"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-danger">{{ __('New Password') }}
                            ({{ __('Leave blank to keep current') }})</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">{{ __('Department') }}</label>
                        <select name="department_id" class="form-select">
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ $user->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">{{ __('Role') }}</label>
                        <select name="role_level" class="form-select">
                            <option value="employee" {{ $user->role_level == 'employee' ? 'selected' : '' }}>Employee
                            </option>
                            <option value="manager" {{ $user->role_level == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="admin" {{ $user->role_level == 'admin' ? 'selected' : '' }}>Administrator
                            </option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active"
                            {{ $user->is_active ? 'checked' : '' }}>
                        <label class="form-check-label">{{ __('Account Active') }}</label>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">{{ __('Back') }}</a>
                        <button type="submit" class="btn btn-primary px-5">{{ __('Update User') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
