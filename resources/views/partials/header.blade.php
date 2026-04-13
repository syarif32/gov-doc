<header class="navbar navbar-expand-md navbar-light bg-white shadow-sm py-2 px-4 sticky-top">
    <div class="container-fluid">
        <!-- Left Side: Date & LIVE TIME -->
        <div class="d-none d-md-block">
            <div class="d-flex align-items-center">
                <!-- Date -->
                <span class="text-muted small fw-bold">
                    <i class="bi bi-calendar3 me-1 text-primary"></i> {{ date('d.m.Y') }}
                </span>

                <!-- Vertical Separator -->
                <div class="vr mx-3 text-muted opacity-25" style="height: 20px;"></div>

                <!-- Live Clock -->
                <span class="text-muted small fw-bold">
                    <i class="bi bi-clock me-1 text-primary"></i>
                    <span id="live-clock">00:00:00</span>
                </span>
            </div>
        </div>

        <!-- Right Side: Lang & Profile -->
        <div class="ms-auto d-flex align-items-center">

            <!-- Language Selection -->
            <div class="dropdown me-3">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle border-0 fw-bold text-uppercase"
                    type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-translate me-1"></i> {{ app()->getLocale() }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 animate__animated animate__fadeIn">
                    <li><a class="dropdown-item @if (app()->getLocale() == 'tk') active @endif"
                            href="{{ url('lang/tk') }}">Türkmençe</a></li>
                    <li><a class="dropdown-item @if (app()->getLocale() == 'ru') active @endif"
                            href="{{ url('lang/ru') }}">Русский</a></li>
                    <li><a class="dropdown-item @if (app()->getLocale() == 'en') active @endif"
                            href="{{ url('lang/en') }}">English</a></li>
                </ul>
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none text-dark dropdown-toggle"
                    data-bs-toggle="dropdown">
                    <div class="text-end me-2 d-none d-sm-block">
                        <div class="fw-bold small lh-1">{{ auth()->user()->full_name }}</div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle"
                            style="font-size: 0.6rem;">{{ strtoupper(auth()->user()->role_level) }}</span>
                    </div>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                        style="width: 35px; height: 35px;">
                        {{ substr(auth()->user()->full_name, 0, 1) }}
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 animate__animated animate__fadeIn">
                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>
                            {{ __('Profile') }}</a></li>

                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>
                            {{ __('Settings') }}</a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger fw-bold">
                                <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- JavaScript for Live Clock -->
<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        const timeString = `${hours}:${minutes}:${seconds}`;
        document.getElementById('live-clock').textContent = timeString;
    }

    // Update every 1 second
    setInterval(updateClock, 1000);
    // Call once immediately so it doesn't wait 1 second to start
    updateClock();
</script>
