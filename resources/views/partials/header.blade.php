<header class="app-header sticky-top">
    <div class="d-flex align-items-center justify-content-between w-100 px-3 px-md-4 py-2">
        
        <div class="d-flex align-items-center flex-grow-1 me-3">
            <button class="btn btn-light btn-icon d-lg-none me-3 rounded-circle" id="mobileMenuToggle" type="button">
                <i class="bi bi-list fs-4 text-dark"></i>
            </button>

            <div class="header-user-info d-none d-md-flex align-items-center">
    
    
        
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-2"
            style="width: 32px; height: 32px; font-size: 0.9rem;">
            {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
        </div>

        <div class="d-flex flex-column lh-sm">
            <span class="fw-semibold text-dark small">
                {{ auth()->user()->full_name }}
            </span>
            <span class="text-muted small">
                {{ auth()->user()->department->name ?? 'Department' }}
            </span>
        </div>

    

</div>
        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3">

            <div class="d-none d-xl-flex align-items-center bg-light px-3 py-1 rounded-pill border border-light subtle-shadow">
                <div class="d-flex align-items-center">
                    <span class="text-secondary small fw-medium tracking-wide">
                        {{ date('d M Y') }}
                    </span>
                </div>
                <div class="vr bg-secondary opacity-25 mx-3" style="height: 14px;"></div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-clock text-primary me-2" style="font-size: 0.9rem;"></i>
                    <span id="live-clock" class="text-dark small fw-bold font-monospace tracking-wide" style="min-width: 65px;">
                        00:00:00
                    </span>
                </div>
            </div>

            <button class="btn btn-light btn-icon rounded-circle d-none d-sm-flex position-relative">
                <i class="bi bi-bell text-secondary"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </button>

            <div class="dropdown">
                <button class="btn btn-light btn-icon rounded-circle lang-btn" type="button" data-bs-toggle="dropdown" title="{{ __('Language') }}">
                    <i class="bi bi-globe2 text-secondary"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 py-2">
                    <li><h6 class="dropdown-header small text-uppercase fw-bold text-muted tracking-wide">{{ __('Region & Language') }}</h6></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-2 px-3 @if (app()->getLocale() == 'ru') active-lang @endif" href="{{ url('lang/ru') }}">
                            <span class="me-3 fs-5">🇮🇩</span> <span class="fw-medium">Indonesia (IDN)</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center py-2 px-3 @if (app()->getLocale() == 'en') active-lang @endif" href="{{ url('lang/en') }}">
                            <span class="me-3 fs-5">🇺🇸</span> <span class="fw-medium">English (EN)</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="dropdown ms-1">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle hide-caret user-dropdown p-1" data-bs-toggle="dropdown">
                    <div class="position-relative hover-elevate-avatar">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                            style="width: 42px; height: 42px; border: 2px solid #fff; font-size: 1.1rem;">
                            {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                        </div>
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle"></span>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2 profile-menu" style="width: 280px;">
                    <li>
                        <div class="d-flex flex-column align-items-center text-center p-3 mb-2 bg-light rounded-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold mb-2"
                                style="width: 56px; height: 56px; font-size: 1.5rem;">
                                {{ strtoupper(substr(auth()->user()->full_name, 0, 1)) }}
                            </div>
                            <div class="fw-bold text-dark fs-6">{{ auth()->user()->full_name }}</div>
                            <div class="text-secondary small font-monospace mb-2"><i class="bi bi-at opacity-50"></i>{{ auth()->user()->username }}</div>
                            <span class="badge bg-white text-dark border px-3 py-1 rounded-pill fw-medium shadow-sm">
                                {{ auth()->user()->department->name ?? __('Government Office') }}
                            </span>
                        </div>
                    </li>
                    
                    <li><a class="dropdown-item rounded-3 py-2 px-3 mb-1 d-flex align-items-center" href="{{ route('profile') }}">
                        <i class="bi bi-person me-3 text-secondary fs-5"></i> <span class="fw-medium">{{ __('Manage Profile') }}</span>
                    </a></li>
                    
                    <li><a class="dropdown-item rounded-3 py-2 px-3 mb-1 d-flex align-items-center" href="#">
                        <i class="bi bi-shield-check me-3 text-secondary fs-5"></i> <span class="fw-medium">{{ __('Security Settings') }}</span>
                    </a></li>
                    
                    <li><hr class="dropdown-divider my-2 opacity-25"></li>
                    
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item rounded-3 py-2 px-3 text-danger d-flex align-items-center hover-danger">
                                <i class="bi bi-box-arrow-right me-3 fs-5"></i> <span class="fw-semibold">{{ __('Sign Out') }}</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</header>

<style>
    /* Header Container - Glassmorphism */
    .app-header {
        background-color: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    /* Google Style Search Bar */
    .header-search .search-input {
        background-color: #f1f3f4;
        border: 1px solid transparent;
        border-radius: 24px;
        padding: 10px 16px 10px 44px;
        font-size: 0.95rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        color: #202124;
    }
    .header-search .search-input:focus {
        background-color: #fff;
        border-color: transparent;
        box-shadow: 0 1px 6px rgba(32,33,36,0.15);
        outline: none;
    }
    .header-search .search-input::placeholder {
        color: #5f6368;
        font-weight: 400;
    }

    /* Icon Buttons */
    .btn-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
        border: none;
    }
    .btn-icon:hover {
        background-color: #e8eaed;
    }

    /* Typography & Utilities */
    .tracking-wide { letter-spacing: 0.5px; }
    .subtle-shadow { box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .hide-caret::after { display: none !important; }

    /* Dropdown Enhancements */
    .dropdown-item {
        transition: background-color 0.15s ease, color 0.15s ease;
        color: #3c4043;
    }
    .dropdown-item:active {
        background-color: #e8f0fe;
        color: #1a73e8;
    }
    .dropdown-item:hover:not(.text-danger) {
        background-color: #f8f9fa;
        color: #202124;
    }
    .active-lang {
        background-color: #e8f0fe !important;
        color: #1a73e8 !important;
    }
    .hover-danger:hover {
        background-color: #fce8e6 !important;
        color: #d93025 !important;
    }

    /* Avatar Hover Effect */
    .hover-elevate-avatar {
        transition: transform 0.2s ease;
    }
    .hover-elevate-avatar:hover {
        transform: scale(1.05);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clockElement = document.getElementById('live-clock');
        if (clockElement) {
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
            setInterval(updateClock, 1000);
            updateClock();
        }
    });
</script>