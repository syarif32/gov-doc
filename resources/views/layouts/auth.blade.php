<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') Diskominfo Semarang</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #eef2f7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .auth-wrapper {
            display: flex;
            width: 100%;
            max-width: 860px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
        }

        /* ── Left panel (Diubah ke Tema Merah Elegan) ── */
        .auth-side {
            width: 230px;
            flex-shrink: 0;
            /* Gradien merah elegan standar Eropa */
            background: linear-gradient(160deg, #991b1b 0%, #dc2626 100%);
            padding: 40px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .auth-brand {
            text-align: center;
            color: #fff;
        }

        /* Kelas baru untuk gambar logo */
        .brand-logo {
            width: 75px;
            height: auto;
            margin: 0 auto 16px;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2)); /* Memberikan efek kedalaman */
            transition: transform 0.3s ease;
        }
        
        .brand-logo:hover {
            transform: scale(1.05);
        }

        /* Dipertahankan untuk fallback jika masih menggunakan icon di tempat lain */
        .brand-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 28px;
        }

        .auth-brand h2 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .auth-brand p {
            font-size: 11px;
            opacity: 0.8;
            margin: 4px 0 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .auth-side-bottom {
            text-align: center;
        }

        .lang-switcher {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-bottom: 14px;
        }

        .lang-switcher a {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            padding: 3px 8px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all .15s;
        }

        .lang-switcher a:hover,
        .lang-switcher a.active {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.1);
        }

        .github-link {
            display: flex;
            align-items: center;
            gap: 5px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 10px;
            text-decoration: none;
            justify-content: center;
            transition: color .15s;
        }

        .github-link:hover {
            color: rgba(255, 255, 255, 1);
        }

        .github-link a {
            color: inherit;
            text-decoration: none;
        }

        .github-link i {
            font-size: 13px;
        }

        /* ── Right panel ── */
        .auth-body {
            flex: 1;
            background: #fff;
            padding: 40px 36px;
            overflow-y: auto;
        }

        .auth-badge {
            display: inline-block;
            background: #fef2f2; /* Disesuaikan ke rona merah */
            color: #b91c1c; /* Disesuaikan ke rona merah */
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 14px;
        }

        .auth-body h3 {
            font-size: 22px;
            font-weight: 700;
            color: #111;
            margin: 0 0 4px;
        }

        .auth-subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        /* ── Form elements ── */
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .field-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
            pointer-events: none;
        }

        .input-icon-wrap .form-control,
        .input-icon-wrap .form-select {
            padding-left: 34px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
            font-size: 14px;
            height: 42px;
            transition: border-color .15s, background .15s, box-shadow .15s;
        }

        .input-icon-wrap .form-control:focus,
        .input-icon-wrap .form-select:focus {
            border-color: #dc2626; /* Disesuaikan ke rona merah */
            background: #fff;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1); /* Disesuaikan ke rona merah */
            outline: none;
        }

        .btn-auth-primary {
            width: 100%;
            padding: 11px;
            background: #dc2626; /* Disesuaikan ke rona merah */
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, transform .1s;
            margin-top: 6px;
        }

        .btn-auth-primary:hover {
            background: #991b1b; /* Disesuaikan ke rona merah gelap */
        }
        
        .btn-auth-primary:active {
            transform: scale(0.98);
        }

        .auth-divider {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            margin: 16px 0 8px;
        }

        .auth-switch {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
        }

        .auth-switch a {
            color: #dc2626; /* Disesuaikan ke rona merah */
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s;
        }
        
        .auth-switch a:hover {
            color: #991b1b;
        }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            .auth-side {
                display: none;
            }

            .auth-wrapper {
                max-width: 420px;
            }

            .auth-body {
                padding: 32px 24px;
            }
        }

        /*  */
        /* ── Left panel (Diperbarui untuk kesan Elegan) ── */
.auth-side {
    width: 320px; /* Diperlebar sedikit dari 230px agar teks proporsional */
    flex-shrink: 0;
    background: linear-gradient(145deg, #8b0000 0%, #dc2626 100%); /* Merah korporat yang dalam */
    padding: 40px 32px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

/* Ornamen background abstrak menggunakan CSS murni */
.bg-shape {
    position: absolute;
    border-radius: 50%;
    z-index: -1;
}
.bg-shape-top {
    top: -50px;
    left: -50px;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
}
.bg-shape-bottom {
    bottom: -80px;
    right: -50px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0) 70%);
}

/* Brand Area */
.auth-brand {
    text-align: left; /* Rata kiri lebih modern */
    color: #fff;
}

.brand-logo {
    width: 65px;
    height: auto;
    margin-bottom: 20px;
    filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
}

.auth-brand h2 {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 1px;
    margin: 0 0 4px;
    line-height: 1.2;
}

.auth-brand p {
    font-size: 11px;
    opacity: 0.85;
    margin: 0;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

/* Mid Section: Info Sistem */
.auth-info {
    color: #fff;
    margin: 30px 0;
    padding-left: 14px;
    border-left: 2px solid rgba(255,255,255,0.25);
}

.badge-system {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(4px);
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 12px;
    border: 1px solid rgba(255,255,255,0.1);
}

.auth-info h3 {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 10px;
    line-height: 1.3;
}

.auth-info p {
    font-size: 12px;
    opacity: 0.75;
    line-height: 1.6;
    margin: 0 0 16px;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    font-size: 11px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    opacity: 0.9;
    letter-spacing: 0.3px;
}

.feature-list li i {
    color: #fca5a5; /* Warna merah muda redup untuk ikon centang */
    font-size: 14px;
}

/* Bottom Section */
.auth-side-bottom {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.lang-switcher {
    display: flex;
    gap: 8px;
    justify-content: flex-start;
}

.lang-switcher a {
    font-size: 10px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.5);
    text-decoration: none;
    padding: 4px 10px;
    border-radius: 4px;
    background: rgba(0,0,0,0.15);
    transition: all .2s ease;
}

.lang-switcher a:hover,
.lang-switcher a.active {
    color: #fff;
    background: rgba(255,255,255,0.2);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.creator-wrapper {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 16px;
}

.creator-wrapper span {
    display: block;
    font-size: 9px;
    color: rgba(255,255,255,0.5);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.github-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #fff;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    transition: opacity .15s;
}

.github-link:hover {
    opacity: 0.7;
}
    </style>
</head>

<body>

    <div class="auth-wrapper animate__animated animate__fadeInUp animate__faster">

        {{-- Left brand panel --}}
<div class="auth-side">
    <div class="bg-shape bg-shape-top"></div>
    <div class="bg-shape bg-shape-bottom"></div>

    <div class="auth-brand">
        <img src="https://diskominfo.semarangkota.go.id/img/logodiskominfo.png" alt="Logo Diskominfo" class="brand-logo">
        <h2>DISKOMINFO</h2>
        <p>Pemerintah Kota Semarang</p>
    </div>

    <div class="auth-info">
        <span class="badge-system">DMS Workspace</span>
        <h3>Sistem Manajemen<br>Dokumen Digital</h3>
        <p>Akses ruang kerja kolaboratif, kelola arsip, dan amankan data internal secara efisien.</p>
        <ul class="feature-list">
            <li><i class="bi bi-shield-check"></i> Infrastruktur Terenkripsi</li>
            <li><i class="bi bi-cloud-arrow-up"></i> Sinkronisasi Real-time</li>
        </ul>
    </div>

    <div class="auth-side-bottom">
        <div class="lang-switcher">
            <a href="{{ url('lang/ru') }}" class="{{ app()->getLocale() == 'ru' ? 'active' : '' }}">IDN</a>
            <a href="{{ url('lang/en') }}" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">ENG</a>
        </div>
        <div class="creator-wrapper">
            <span>Sistem Dikembangkan Oleh</span>
            <a href="https://zaeeoon.vercel.app" target="_blank" class="github-link">
                <i class="bi bi-github"></i> Muhammad Najwa Syarif
            </a>
        </div>
    </div>
</div>

        {{-- Right form panel --}}
        <div class="auth-body">
            @yield('content')
        </div>

    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>