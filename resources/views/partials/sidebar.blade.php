<div id="sidebar" class="sidebar-wrapper d-flex flex-column shadow-sm">

    <div class="sidebar-brand d-flex flex-column align-items-center justify-content-center py-4">
        <div class="brand-logo-wrap bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm">
           <img src="https://upload.wikimedia.org/wikipedia/commons/f/f2/Lambang_Kota_Semarang.png" 
     alt="Logo"
     class="img-fluid"
     style="max-width: 60%; height: auto;">
            
        </div>
        <h5 class="fw-bold mb-1 text-white text-uppercase tracking-wide text-center">
            Diskominfo<br><span class="text-primary" style="font-size: 0.9rem;">Semarang</span>
        </h5>
        
        <div class="mt-2 badge rounded-pill px-3 py-1 fw-medium" style="background: rgba(255,255,255,0.05); color: #8a99af; border: 1px solid rgba(255,255,255,0.1);">
            <i class="bi bi-circle-fill text-success me-2" style="font-size: 0.4rem; vertical-align: middle;"></i>
            {{ auth()->user()->department->name ?? 'CENTRAL UNIT' }}
        </div>
    </div>

    <div class="flex-grow-1 overflow-auto py-3 custom-scrollbar">
        <nav class="nav flex-column sidebar-nav">

            <div class="nav-category mt-2">
                {{ __('Main Menu') }}
            </div>

            <a href="{{ route('dashboard') }}" class="nav-link side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> <span>{{ __('Dashboard') }}</span>
            </a>

            <a href="{{ route('docs.index') }}" class="nav-link side-link {{ request()->routeIs('docs.*') ? 'active' : '' }}">
                <i class="bi bi-folder2-open"></i> <span>{{ __('Documents') }}</span>
            </a>

            <a href="{{ route('chat.index') }}" class="nav-link side-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-dots"></i> <span>{{ __('Messaging') }}</span>
                <span class="badge bg-primary ms-auto rounded-pill px-2 py-1" style="font-size: 0.65rem;">New</span>
            </a>

            @if (auth()->user()->role_level === 'admin')
                <div class="nav-category mt-4">
                    {{ __('Control Panel') }}
                </div>

                <a href="{{ route('admin.users.index') }}" class="nav-link side-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> <span>{{ __('User Management') }}</span>
                </a>

                <a href="{{ route('admin.departments.index') }}" class="nav-link side-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> <span>{{ __('Departments') }}</span>
                </a>
                <a href="{{ route('admin.folders.index') }}" class="nav-link side-link {{ request()->routeIs('admin.folders.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> <span>{{ __('Folders') }}</span>
                </a>

                <a href="{{ route('admin.logs') }}" class="nav-link side-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i> <span>{{ __('System Logs') }}</span>
                </a>
            @endif
        </nav>
    </div>

    <div class="sidebar-footer p-4">
        <a href="https://zaeeoon.vercel.app" target="_blank" class="dev-card d-block text-decoration-none rounded-3 p-3">
            <div class="d-flex align-items-center">
                <div class="dev-avatar bg-primary bg-opacity-25 text-primary rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0">
                    <i class="bi bi-terminal fs-5"></i>
                </div>
                <div class="overflow-hidden">
                    <div class="fw-bold text-white small text-truncate mb-1">M. Najwa Syarif</div>
                    <div class="text-uppercase tracking-wide" style="font-size: 0.6rem; color: #8a99af;">{{ __('System Architect') }}</div>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
    /* Premium Sidebar Color Palette */
    :root {
        --sidebar-bg: #1c2434;
        --sidebar-border: #293445;
        --sidebar-text-muted: #8a99af;
        --sidebar-text-hover: #ffffff;
        --sidebar-hover-bg: #333a48;
        --sidebar-active-bg: rgba(13, 110, 253, 0.15); /* Soft Blue */
    }

    .sidebar-wrapper {
        background-color: var(--sidebar-bg);
        border-right: 1px solid var(--sidebar-border);
        color: var(--sidebar-text-muted);
        height: 100vh;
    }

    /* Brand Section */
    .sidebar-brand {
        border-bottom: 1px solid var(--sidebar-border);
    }
    
    .brand-logo-wrap {
        width: 56px;
        height: 56px;
    }

    .tracking-wide {
        letter-spacing: 1px;
    }

    /* Navigation Links (Google Pill Style adjusted for Dark Mode) */
    .sidebar-nav {
        padding-right: 16px; /* Space for the pill shape */
    }

    .nav-category {
        padding-left: 28px;
        margin-bottom: 8px;
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #5c6a82;
        letter-spacing: 1.5px;
    }

    .side-link {
        color: var(--sidebar-text-muted) !important;
        border-radius: 0 24px 24px 0 !important; /* Rounded only on the right */
        padding: 12px 24px 12px 28px;
        margin-bottom: 4px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        font-weight: 500;
        border: none;
    }

    .side-link i {
        font-size: 1.15rem;
        width: 28px;
        margin-right: 8px;
        transition: color 0.2s ease;
    }

    /* Hover State (No jarring transform animations) */
    .side-link:hover {
        background-color: var(--sidebar-hover-bg);
        color: var(--sidebar-text-hover) !important;
    }

    /* Active State */
    .side-link.active {
        background-color: var(--sidebar-active-bg) !important;
        color: #3b82f6 !important; /* Bright blue text */
        font-weight: 600;
    }

    .side-link.active i {
        color: #3b82f6;
    }

    /* Footer Developer Card */
    .sidebar-footer {
        border-top: 1px solid var(--sidebar-border);
    }

    .dev-card {
        background: rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.2s ease;
    }

    .dev-card:hover {
        background: var(--sidebar-hover-bg);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .dev-avatar {
        width: 40px;
        height: 40px;
    }

    /* Refined Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #293445;
        border-radius: 10px;
    }

    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background: #3c4b61;
    }
</style>