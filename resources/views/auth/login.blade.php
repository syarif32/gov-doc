@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <h4 class="text-center mb-4">Sign In</h4>

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-bold">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <div class="mb-3 d-flex justify-content-between">
            <div class="form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small" for="remember">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-auth mb-3">Login</button>

        <p class="text-center small mb-0">
            Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-bold">Register here</a>
        </p>
    </form>
@endsection
