<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diskominfo - Management System</title>

    <!-- Local Assets Links -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

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
            display: flex;
            flex-direction: column;
            /* Added to allow mt-auto in sidebar */
        }

        #main-content {
            margin-left: var(--sidebar-width);
            width: 100%;
            display: flex;
            flex-direction: column;
            /* Added to keep footer at the bottom */
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

        /* Signature Styling */
        .dev-signature {
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .dev-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 700;
            transition: 0.3s;
        }

        .dev-link:hover {
            text-decoration: underline;
            opacity: 0.8;
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
        <main class="p-4 flex-grow-1">
            @yield('content')
        </main>

        <!-- 5. Professional Signature Footer -->
        <footer class="bg-white border-top py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    &copy; {{ date('Y') }} <strong>GovDoc</strong>. {{ __('All rights reserved.') }}
                </div>
                <div class="dev-signature text-muted">
                    <i class="bi bi-code-slash text-primary"></i> {{ __('Developed by') }}
                    <a href="https://github.com/syarif32" target="_blank" class="dev-link">syarif</a>
                </div>
            </div>
        </footer>

    </div>

    <!-- Local JS Assets -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Real-time Echo (Local Reverb) -->
    <script src="{{ asset('assets/js/pusher.min.js') }}"></script>
    <script src="{{ asset('assets/js/echo.iife.js') }}"></script>

    <script>
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
