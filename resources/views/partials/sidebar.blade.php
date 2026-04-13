<div id="sidebar" class="bg-dark text-white shadow">
    <div class="p-4 text-center border-bottom border-secondary">
        <h4 class="fw-bold mb-0 text-uppercase tracking-wider">
            <i class="bi bi-shield-check text-primary"></i> GovDoc
        </h4>
        <div class="small text-muted mt-1">{{ auth()->user()->department->name ?? 'Gov Unit' }}</div>
    </div>

    <nav class="nav flex-column p-2 mt-3">
        <!-- Main Links -->
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> {{ __('Dashboard') }}
        </a>

        <a href="{{ route('docs.index') }}" class="nav-link {{ request()->routeIs('docs.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-lock-fill"></i> {{ __('Documents') }}
        </a>

        <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
            <i class="bi bi-chat-square-text-fill"></i> {{ __('Messaging') }}
        </a>

        <!-- Admin Only Section -->
        @if (auth()->user()->role_level === 'admin')
            <div class="px-3 mt-4 mb-2 small text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">System Admin
            </div>

            <a href="{{ route('admin.users.index') }}"
                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> {{ __('User Management') }}
            </a>

            <a href="{{ route('admin.departments.index') }}"
                class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <i class="bi bi-building-fill"></i> {{ __('Departments') }}
            </a>
            
            <a href="{{ route('admin.logs') }}"
                class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> {{ __('System Logs') }}
            </a>
        @endif
    </nav>
</div>
