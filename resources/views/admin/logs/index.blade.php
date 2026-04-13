@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="bi bi-journal-text"></i> {{ __('System Logs') }}</h4>
        <span class="badge bg-dark">{{ __('Security Audit Trail') }}</span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small fw-bold">
                        <tr>
                            <th class="ps-4">{{ __('User') }}</th>
                            <th>{{ __('Action') }}</th>
                            <th>{{ __('IP Address') }}</th>
                            <th>{{ __('Device / Browser') }}</th>
                            <th class="text-end pe-4">{{ __('Date & Time') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $log->user->username ?? 'Unknown' }}</div>
                                    <div class="small text-muted">{{ $log->user->full_name ?? '' }}</div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ str_contains($log->action, 'Delete') ? 'bg-danger' : 'bg-secondary' }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="small font-monospace">{{ $log->ip_address }}</td>
                                <td class="small text-muted" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 40) }}
                                </td>
                                <td class="text-end pe-4 small fw-bold">
                                    {{ $log->created_at->format('d.m.Y H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
