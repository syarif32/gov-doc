@extends('layouts.admin')

@section('content')
    <div class="dashboard-container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 stagger-1">
            <div>
                <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.5px;">
                    {{ __('System Administration') }}
                </h2>
                <p class="text-secondary small mb-0">{{ __('Global oversight and security monitoring') }}</p>
            </div>
            
            <div class="mt-3 mt-md-0 d-flex align-items-center bg-white px-3 py-2 rounded-pill shadow-sm border border-light">
                <div class="pulse-dot bg-success me-2"></div>
                <span class="small fw-semibold text-dark">{{ __('Live System Monitor') }}</span>
                <span class="badge bg-success bg-opacity-10 text-success ms-2 rounded-pill border border-success-subtle">Online</span>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-sm-6 stagger-2">
                <div class="card md-card h-100 border-0 position-relative">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-secondary fw-semibold small mb-1 text-uppercase tracking-wide">{{ __('Total Users') }}</p>
                                <h2 class="fw-bold text-dark mb-0 display-6">{{ $stats['total_users'] }}</h2>
                            </div>
                            <div class="icon-shape bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between mt-4">
                            <a href="{{ route('admin.users.index') }}" class="stretched-link small text-primary text-decoration-none fw-semibold d-flex align-items-center link-hover">
                                {{ __('View All Users') }} <i class="bi bi-arrow-right ms-1 transition-icon"></i>
                            </a>
                            @if ($stats['pending_users'] > 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill position-relative z-1">
                                    {{ $stats['pending_users'] }} {{ __('Pending') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 stagger-3">
                <div class="card md-card h-100 border-0 position-relative">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-secondary fw-semibold small mb-1 text-uppercase tracking-wide">{{ __('Total Documents') }}</p>
                                <h2 class="fw-bold text-dark mb-0 display-6">{{ $stats['total_docs'] }}</h2>
                            </div>
                            <div class="icon-shape bg-info bg-opacity-10 text-info">
                                <i class="bi bi-folder-fill"></i>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('docs.index') }}" class="stretched-link small text-info text-decoration-none fw-semibold d-flex align-items-center link-hover">
                                {{ __('Global Filesystem') }} <i class="bi bi-arrow-right ms-1 transition-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 stagger-4">
                <div class="card md-card h-100 border-0 position-relative">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-secondary fw-semibold small mb-1 text-uppercase tracking-wide">{{ __('Departments') }}</p>
                                <h2 class="fw-bold text-dark mb-0 display-6">{{ $stats['total_depts'] }}</h2>
                            </div>
                            <div class="icon-shape bg-success bg-opacity-10 text-success">
                                <i class="bi bi-building-fill"></i>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.departments.index') }}" class="stretched-link small text-success text-decoration-none fw-semibold d-flex align-items-center link-hover">
                                {{ __('Active Units') }} <i class="bi bi-arrow-right ms-1 transition-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 stagger-5">
                <div class="card md-card h-100 border-0 position-relative">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-secondary fw-semibold small mb-1 text-uppercase tracking-wide">{{ __('System Health') }}</p>
                                <h2 class="fw-bold text-dark mb-0 display-6">100<span class="fs-4 text-muted">%</span></h2>
                            </div>
                            <div class="icon-shape bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.logs') }}" class="stretched-link small text-warning text-decoration-none fw-semibold d-flex align-items-center link-hover" style="color: #d97706 !important;">
                                {{ __('Encrypted & Secure') }} <i class="bi bi-arrow-right ms-1 transition-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 stagger-6">
            <div class="col-xl-8">
                <div class="card md-card border-0 h-100 overflow-hidden">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">{{ __('System-Wide Security Audit') }}</h5>
                        <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-light fw-semibold text-primary rounded-pill px-3 py-1">
                            {{ __('View All') }}
                        </a>
                    </div>
                    <div class="card-body p-0 mt-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle mb-0">
                                <thead class="border-bottom border-light">
                                    <tr>
                                        <th class="ps-4 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Admin/User') }}</th>
                                        <th class="text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Action') }}</th>
                                        <th class="text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('IP Address') }}</th>
                                        <th class="pe-4 text-secondary small fw-semibold text-uppercase tracking-wide text-end">{{ __('Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats['recent_activities'] as $log)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold small">
                                                        {{ strtoupper(substr($log->user->username ?? 'S', 0, 1)) }}
                                                    </div>
                                                    <div class="fw-semibold text-dark">{{ $log->user->username ?? 'System' }}</div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                @php
                                                    $badgeClass = 'bg-secondary text-secondary border-secondary-subtle';
                                                    if (str_contains(strtolower($log->action), 'delete') || str_contains(strtolower($log->action), 'fail')) {
                                                        $badgeClass = 'bg-danger text-danger border-danger-subtle';
                                                    } elseif (str_contains(strtolower($log->action), 'upload') || str_contains(strtolower($log->action), 'create')) {
                                                        $badgeClass = 'bg-success text-success border-success-subtle';
                                                    } elseif (str_contains(strtolower($log->action), 'update') || str_contains(strtolower($log->action), 'edit')) {
                                                        $badgeClass = 'bg-primary text-primary border-primary-subtle';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }} bg-opacity-10 border rounded-pill px-3 py-2 fw-medium">
                                                    {{ $log->action }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-secondary font-monospace small">{{ $log->ip_address }}</td>
                                            <td class="pe-4 py-3 text-secondary small text-end">{{ $log->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card md-card border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold text-dark mb-0">{{ __('User Distribution') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        @foreach ($stats['dept_distribution'] as $dept)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <span class="fw-semibold text-dark">{{ $dept->name }}</span>
                                    <span class="text-secondary small fw-medium">{{ $dept->users_count }} {{ __('users') }}</span>
                                </div>
                                <div class="progress bg-light" style="height: 8px; border-radius: 8px;">
                                    @php
                                        $percent = ($dept->users_count / max($stats['total_users'], 1)) * 100;
                                        // Google brand colors equivalent
                                        $colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info'];
                                        $currentColor = $colors[$loop->index % count($colors)];
                                    @endphp
                                    <div class="progress-bar {{ $currentColor }} rounded-pill" style="width: {{ $percent }}%" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Typography & Layout Variables */
        :root {
            --md-radius: 20px;
            --md-shadow-rest: 0 4px 20px rgba(0, 0, 0, 0.03);
            --md-shadow-hover: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .tracking-wide {
            letter-spacing: 0.5px;
        }

        /* Card Styling */
        .md-card {
            border-radius: var(--md-radius);
            box-shadow: var(--md-shadow-rest);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .md-card:hover {
            box-shadow: var(--md-shadow-hover);
            transform: translateY(-4px);
        }

        /* Icon Shapes */
        .icon-shape {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        /* Table Enhancements */
        .table > :not(caption) > * > * {
            border-bottom-color: #f1f3f4;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Link Interactions */
        .link-hover:hover .transition-icon {
            transform: translateX(4px);
        }
        .transition-icon {
            transition: transform 0.2s ease;
        }

        /* Status Pulse (Elegant Breathing Effect, not flashing) */
        .pulse-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.5);
            animation: pulse-soft 2s infinite cubic-bezier(0.66, 0, 0, 1);
        }

        @keyframes pulse-soft {
            to {
                box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
            }
        }

        /* Lightweight Staggered Entrance Animations */
        [class*="stagger-"] {
            opacity: 0;
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .stagger-1 { animation-delay: 0.0s; }
        .stagger-2 { animation-delay: 0.1s; }
        .stagger-3 { animation-delay: 0.2s; }
        .stagger-4 { animation-delay: 0.3s; }
        .stagger-5 { animation-delay: 0.4s; }
        .stagger-6 { animation-delay: 0.5s; }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection