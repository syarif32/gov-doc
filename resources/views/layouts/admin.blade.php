<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GovDoc - Management System</title>

    <!-- Local Assets Links -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> <!-- Your custom styles -->

    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #f8f9fa;
        }

        #sidebar {
            width: var(--sidebar-width);
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        #main-content {
            margin-left: var(--sidebar-width);
            width: 100%;
            flex-grow: 1;
            min-height: 100vh;
        }

        .nav-link {
            padding: 12px 20px;
            border-radius: 0;
            margin-bottom: 2px;
        }

        .nav-link i {
            margin-right: 12px;
        }

        .nav-link.active {
            background: rgba(13, 110, 253, 0.15) !important;
            color: #0d6efd !important;
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>

<body>

    <!-- 1. Sidebar -->
    @include('partials.sidebar')

    <!-- 2. Main Area -->
    <div id="main-content">

        <!-- 3. Header -->
        @include('partials.header')

        <!-- 4. Dynamic Page Content -->
        <main class="p-4">
            @yield('content')
        </main>

    </div>

    <!-- Local JS Assets -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Real-time Echo (Local Reverb) -->
    <script src="{{ asset('assets/js/pusher.min.js') }}"></script>
    <script src="{{ asset('assets/js/echo.iife.js') }}"></script>

    <script>
        // Pre-setup for real-time later
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: '{{ env('REVERB_HOST') }}',
            wsPort: {{ env('REVERB_PORT') }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });
    </script>

    @stack('scripts')
</body>

</html>
