@extends('layouts.admin')

@section('content')
    <div class="animate__animated animate__fadeIn">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-0">
                    <i class="bi bi-shield-lock-fill me-2"></i> {{ __('System Administration') }}
                </h3>
                <p class="text-muted small mb-0">{{ __('Global oversight and security monitoring') }}</p>
            </div>
            <span class="badge bg-danger p-2 animate__animated animate__pulse animate__infinite">
                <i class="bi bi-broadcast me-1"></i> {{ __('Live System Monitor') }}
            </span>
        </div>

        <!-- Admin Widgets -->
        <div class="row g-3">
            <!-- Total Users Card -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden admin-card">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">{{ __('Total Users') }}</div>
                        <h2 class="fw-bold my-1">{{ $stats['total_users'] }}</h2>
                        <a href="{{ route('admin.users.index') }}"
                            class="stretched-link small text-primary text-decoration-none fw-bold">
                            {{ __('View All Users') }} <i class="bi bi-arrow-right"></i>
                        </a>
                        @if ($stats['pending_users'] > 0)
                            <div class="mt-2 animate__animated animate__flash animate__infinite">
                                <span class="badge bg-danger">{{ $stats['pending_users'] }}
                                    {{ __('Waiting Approval') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Total Documents Card -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden admin-card">
                    <div class="card-body text-center p-4">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-file-earmark-lock-fill fs-3"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">{{ __('Total Documents') }}</div>
                        <h2 class="fw-bold text-primary my-1">{{ $stats['total_docs'] }}</h2>
                        <a href="{{ route('docs.index') }}"
                            class="stretched-link small text-info text-decoration-none fw-bold">
                            {{ __('Global Filesystem') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Departments Card -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden admin-card">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-building-fill fs-3"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">{{ __('Departments') }}</div>
                        <h2 class="fw-bold text-success my-1">{{ $stats['total_depts'] }}</h2>
                        <a href="{{ route('admin.departments.index') }}"
                            class="stretched-link small text-success text-decoration-none fw-bold">
                            {{ __('Active Units') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status Card -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden admin-card">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-cpu-fill fs-3"></i>
                        </div>
                        <div class="text-muted small fw-bold text-uppercase">{{ __('System Status') }}</div>
                        <h2 class="fw-bold text-warning my-1">100%</h2>
                        <a href="{{ route('admin.logs') }}"
                            class="stretched-link small text-warning text-decoration-none fw-bold">
                            {{ __('Online / Encrypted') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Global Audit Log -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-journal-text me-2"></i> {{ __('System-Wide Security Audit') }}</span>
                        <a href="{{ route('admin.logs') }}"
                            class="btn btn-sm btn-outline-light border-0">{{ __('View All') }}</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 small align-middle">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="ps-4">{{ __('Admin/User') }}</th>
                                        <th>{{ __('Action Performed') }}</th>
                                        <th>{{ __('IP') }}</th>
                                        <th class="pe-4">{{ __('Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stats['recent_activities'] as $log)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold">{{ $log->user->username ?? 'System' }}</div>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = 'bg-secondary';
                                                    if (str_contains($log->action, 'Delete')) {
                                                        $badgeClass = 'bg-danger';
                                                    }
                                                    if (
                                                        str_contains($log->action, 'Uploaded') ||
                                                        str_contains($log->action, 'Created')
                                                    ) {
                                                        $badgeClass = 'bg-success';
                                                    }
                                                @endphp
                                                <span
                                                    class="badge {{ $badgeClass }} bg-opacity-10 text-dark border border-{{ str_replace('bg-', '', $badgeClass) }}-subtle">
                                                    {{ $log->action }}
                                                </span>
                                            </td>
                                            <td class="text-muted font-monospace">{{ $log->ip_address }}</td>
                                            <td class="pe-4 text-muted">{{ $log->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dept Breakdown -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold py-3">
                        <i class="bi bi-pie-chart-fill me-2 text-primary"></i> {{ __('User Distribution') }}
                    </div>
                    <div class="card-body">
                        @foreach ($stats['dept_distribution'] as $dept)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small fw-bold">{{ $dept->name }}</span>
                                    <span class="badge bg-light text-dark border small">{{ $dept->users_count }}
                                        {{ __('users') }}</span>
                                </div>
                                <div class="progress shadow-sm" style="height: 10px; border-radius: 10px;">
                                    @php
                                        $percent = ($dept->users_count / max($stats['total_users'], 1)) * 100;
                                        $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                                        $currentColor = $colors[$loop->index % count($colors)];
                                    @endphp
                                    <div class="progress-bar {{ $currentColor }} progress-bar-striped progress-bar-animated"
                                        style="width: {{ $percent }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Hover Effects -->
    <style>
        .admin-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .progress-bar {
            border-radius: 10px;
        }
    </style>
@endsection
