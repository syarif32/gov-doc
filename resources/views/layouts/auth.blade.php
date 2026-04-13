<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - GovDoc System</title>

    <!-- Using your local CSS files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        body {
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: #fff;
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }

        .auth-header {
            background: #0d6efd;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .auth-body {
            padding: 40px;
        }

        .btn-auth {
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
        }

        .lang-switcher a {
            text-decoration: none;
            font-weight: bold;
            margin: 0 5px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <div class="auth-card animate__animated animate__fadeInDown">
        <div class="auth-header">
            <i class="bi bi-shield-lock-fill" style="font-size: 3rem;"></i>
            <h3 class="mt-2">GovConnect</h3>
            <p class="mb-0 opacity-75">Internal Document System</p>
        </div>

        <div class="auth-body">
            @yield('content')

            <hr>
            <div class="lang-switcher text-center mt-3">
                <a href="{{ url('lang/tk') }}"
                    class="{{ app()->getLocale() == 'tk' ? 'text-primary' : 'text-muted' }}">TM</a>
                <span class="text-muted">|</span>
                <a href="{{ url('lang/ru') }}"
                    class="{{ app()->getLocale() == 'ru' ? 'text-primary' : 'text-muted' }}">RU</a>
                <span class="text-muted">|</span>
                <a href="{{ url('lang/en') }}"
                    class="{{ app()->getLocale() == 'en' ? 'text-primary' : 'text-muted' }}">EN</a>
            </div>
        </div>
    </div>

    <!-- Local JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
