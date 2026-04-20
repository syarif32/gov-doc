@extends('layouts.admin')

@section('content')
    <div class="dashboard-container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 stagger-1">
            <div class="d-flex align-items-center">
                <div class="bg-dark bg-opacity-10 text-dark rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-journal-code fs-4"></i>
                </div>
                <div>
                    <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ __('System Logs') }}</h2>
                    <p class="text-secondary small mb-0">{{ __('Comprehensive security and activity audit trail') }}</p>
                </div>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2 d-flex align-items-center fw-medium">
                    <i class="bi bi-shield-check text-success me-2 fs-6"></i> {{ __('Protected Area') }}
                </span>
            </div>
        </div>

        <div class="card md-card border-0 stagger-2 h-100 d-flex flex-column">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0 text-uppercase tracking-wide small">{{ __('Recent Activities') }}</h6>
                <div class="text-muted small">
                    <i class="bi bi-clock-history me-1"></i> {{ __('Real-time tracking') }}
                </div>
            </div>

            <div class="card-body p-0 flex-grow-1">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="border-bottom border-light">
                            <tr>
                                <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('User') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Action') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Network') }}</th>
                                <th class="py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Client Info') }}</th>
                                <th class="pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide text-end">{{ __('Timestamp') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr class="table-row-hover">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-secondary bg-opacity-10 text-secondary fw-bold rounded-circle d-flex align-items-center justify-content-center me-3">
                                                {{ strtoupper(substr($log->user->username ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $log->user->username ?? 'System / Guest' }}</div>
                                                <div class="small text-muted">{{ $log->user->full_name ?? 'Automated Process' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="py-3">
                                        @php
                                            $action = strtolower($log->action);
                                            $badgeClass = 'bg-secondary text-secondary border-secondary-subtle';
                                            $icon = 'bi-record-circle';
                                            
                                            if (str_contains($action, 'delete') || str_contains($action, 'remove') || str_contains($action, 'fail')) {
                                                $badgeClass = 'bg-danger text-danger border-danger-subtle';
                                                $icon = 'bi-trash';
                                            } elseif (str_contains($action, 'create') || str_contains($action, 'add') || str_contains($action, 'upload')) {
                                                $badgeClass = 'bg-success text-success border-success-subtle';
                                                $icon = 'bi-plus-circle';
                                            } elseif (str_contains($action, 'update') || str_contains($action, 'edit')) {
                                                $badgeClass = 'bg-primary text-primary border-primary-subtle';
                                                $icon = 'bi-pencil-square';
                                            } elseif (str_contains($action, 'login') || str_contains($action, 'auth')) {
                                                $badgeClass = 'bg-info text-info border-info-subtle';
                                                $icon = 'bi-box-arrow-in-right';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} bg-opacity-10 border rounded-pill px-3 py-2 fw-medium d-inline-flex align-items-center">
                                            <i class="bi {{ $icon }} me-2"></i> {{ $log->action }}
                                        </span>
                                    </td>

                                    <td class="py-3">
                                        <span class="bg-light text-secondary font-monospace small px-2 py-1 rounded border">
                                            {{ $log->ip_address }}
                                        </span>
                                    </td>

                                    <td class="py-3">
                                        <div class="small text-secondary text-truncate" style="max-width: 250px;" data-bs-toggle="tooltip" title="{{ $log->user_agent }}">
                                            <i class="bi bi-display me-1 opacity-50"></i> {{ Str::limit($log->user_agent, 35) }}
                                        </div>
                                    </td>

                                    <td class="pe-4 py-3 text-end">
                                        <div class="fw-semibold text-dark small">{{ $log->created_at->format('d M Y') }}</div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">{{ $log->created_at->format('H:i:s') }} WIB</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 stagger-3">
                                        <div class="text-muted d-flex flex-column align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                                <i class="bi bi-clipboard-x fs-2 text-secondary opacity-50"></i>
                                            </div>
                                            <p class="mb-0 fw-medium text-dark">{{ __('No logs recorded yet') }}</p>
                                            <small>{{ __('System activities will appear here in real-time.') }}</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($logs->hasPages())
                <div class="card-footer bg-white border-top border-light py-3 px-4 rounded-bottom-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="small text-muted mb-2 mb-md-0">
                            {{ __('Showing') }} <span class="fw-bold text-dark">{{ $logs->firstItem() }}</span> {{ __('to') }} <span class="fw-bold text-dark">{{ $logs->lastItem() }}</span> {{ __('of') }} <span class="fw-bold text-dark">{{ $logs->total() }}</span> {{ __('entries') }}
                        </div>
                        <div class="pagination-custom">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>

    <style>
        :root {
            --md-radius: 16px;
            --md-shadow-rest: 0 2px 12px rgba(0, 0, 0, 0.04);
            --md-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .tracking-wide {
            letter-spacing: 0.5px;
        }

        /* Card Styling */
        .md-card {
            border-radius: var(--md-radius);
            box-shadow: var(--md-shadow-rest);
            transition: box-shadow 0.3s ease;
        }
        
        .md-card:hover {
            box-shadow: var(--md-shadow-hover);
        }

        /* Table Aesthetics */
        .table > :not(caption) > * > * {
            border-bottom-color: #f1f3f4;
        }
        
        .table-row-hover {
            transition: background-color 0.2s ease;
        }
        
        .table-row-hover:hover {
            background-color: #f8f9fa;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            font-size: 14px;
        }

        /* Pagination Override to match styling */
        .pagination-custom .pagination {
            margin-bottom: 0;
            gap: 4px;
        }
        
        .pagination-custom .page-item .page-link {
            border-radius: 8px;
            border: 1px solid transparent;
            color: #5f6368;
            padding: 6px 12px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .pagination-custom .page-item.active .page-link {
            background-color: #e8f0fe;
            color: #1a73e8;
            border-color: transparent;
        }

        .pagination-custom .page-item:not(.active) .page-link:hover {
            background-color: #f1f3f4;
            border-color: #dadce0;
            color: #202124;
        }

        /* Staggered Animations */
        [class*="stagger-"] {
            opacity: 0;
            animation: slideUpFade 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        .stagger-1 { animation-delay: 0.0s; }
        .stagger-2 { animation-delay: 0.15s; }
        .stagger-3 { animation-delay: 0.3s; }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection