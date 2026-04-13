@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <h4 class="text-center mb-4">Create Account</h4>

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-bold">Full Name</label>
            <input type="text" name="full_name" class="form-control" placeholder="Firstname Lastname" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="user123" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@gov.tk" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Department</label>
            <select name="department_id" class="form-select" required>
                @foreach (\App\Models\Department::all() as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label small fw-bold">Confirm</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Interface Language</label>
            <select name="preferred_lang" class="form-select">
                <option value="tk">Türkmençe</option>
                <option value="ru">Русский</option>
                <option value="en">English</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100 btn-auth mb-3">Register</button>

        <p class="text-center small mb-0">
            Already registered? <a href="{{ route('login') }}" class="text-primary fw-bold">Login here</a>
        </p>
    </form>
@endsection
