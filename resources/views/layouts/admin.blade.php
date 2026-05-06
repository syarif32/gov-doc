<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Lambang_Kota_Semarang.png/960px-Lambang_Kota_Semarang.png" type="image/x-icon">
    <title>Diskominfo - Management System</title>

    <!-- Local Assets Links -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
    
    <!-- SAYA COMMENT AGAR TIDAK ERROR 404 DI CONSOLE -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> -->

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
            z-index: 1040; /* Pastikan di atas segalanya */
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease-in-out; /* Animasi mulus saat ditarik */
        }

        #main-content {
            margin-left: var(--sidebar-width);
            width: 100%;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
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

        /* Background gelap saat sidebar muncul di HP */
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1030;
            display: none;
            backdrop-filter: blur(2px);
        }

        /* LOGIKA RESPONSIVE HP */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%); /* Sembunyikan sidebar di kiri */
            }
            
            /* Class ini akan ditambahkan oleh JS saat tombol diklik */
            #sidebar.sidebar-open {
                transform: translateX(0); 
            }

            #main-content {
                margin-left: 0; /* Tarik konten agar full layar */
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body>

    <!-- Overlay Latar Gelap (Khusus HP) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

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
        <footer class="bg-white border-top py-3 px-4 mt-auto">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div class="text-muted small text-center text-md-start">
                    &copy; {{ date('Y') }} <strong>GovDoc</strong>. {{ __('All rights reserved.') }}
                </div>
                <div class="dev-signature text-muted text-center text-md-end">
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
        // Hanya jalankan WebSockets jika REVERB_APP_KEY benar-benar ada di .env
        @if(env('REVERB_APP_KEY'))
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ env('REVERB_APP_KEY') }}',
                wsHost: '{{ env('REVERB_HOST', 'localhost') }}',
                wsPort: {{ env('REVERB_PORT', 8080) }},
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
            });
        @endif
   

        // =========================================================
        // LOGIKA PEMBUKA SIDEBAR DI HP (FIXED)
        // =========================================================
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Tangkap semua event klik di halaman
            document.addEventListener('click', function(e) {
                // 1. Jika tombol ber-icon list/hamburger di header diklik
                if (e.target.closest('.bi-list') || e.target.closest('.navbar-toggler') || e.target.closest('[data-bs-toggle="sidebar"]')) {
                    e.preventDefault();
                    sidebar.classList.toggle('sidebar-open');
                    overlay.classList.toggle('show');
                }
                
                // 2. Jika area gelap (overlay) diklik, tutup sidebar
                if (e.target === overlay) {
                    sidebar.classList.remove('sidebar-open');
                    overlay.classList.remove('show');
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>