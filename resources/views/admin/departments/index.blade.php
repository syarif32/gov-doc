@extends('layouts.admin')

@section('content')
    <div class="dashboard-container">
        <div class="d-flex align-items-center mb-4 pb-2 stagger-1">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                <i class="bi bi-diagram-3-fill fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ __('Department Management') }}</h2>
                <p class="text-secondary small mb-0">{{ __('Organize organizational units and internal divisions') }}</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-4 col-lg-5 stagger-2">
                <div class="card md-card border-0 position-relative overflow-hidden h-100">
                    <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 4px;"></div>
                    
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <i class="bi bi-plus-circle-fill text-primary me-2"></i> {{ __('Add Department') }}
                        </h5>
                    </div>
                    
                    <div class="card-body px-4 pb-4 pt-2">
                        <form action="{{ route('admin.departments.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Name (IDN)') }}</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-translate"></i></span>
                                    <input type="text" name="name_ru" class="form-control md-input" placeholder="Misal: Bidang Jaringan" required autocomplete="off">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary text-uppercase tracking-wide mb-2">{{ __('Name (EN)') }}</label>
                                <div class="input-group-custom">
                                    <span class="input-icon"><i class="bi bi-globe2"></i></span>
                                    <input type="text" name="name_en" class="form-control md-input" placeholder="e.g: Networking Division" required autocomplete="off">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary md-btn w-100 mt-2 d-flex justify-content-center align-items-center py-2">
                                <i class="bi bi-check2-circle me-2 fs-5"></i> <span class="fw-semibold tracking-wide">{{ __('Save Department') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7 stagger-3">
                <div class="card md-card border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">{{ __('Active Departments') }}</h5>
                        <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fw-medium">
                            {{ $departments->count() }} {{ __('Total') }}
                        </span>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle mb-0">
                                <thead class="border-bottom border-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide">{{ __('Department Name') }}</th>
                                        <th class="pe-4 py-3 text-secondary small fw-semibold text-uppercase tracking-wide text-end">{{ __('Total Users') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departments as $dept)
                                        <tr class="table-row-hover">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="dept-icon-box bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-building"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark">{{ $dept->name }}</div>
                                                        <div class="small text-muted d-none d-md-block">ID: {{ str_pad($dept->id ?? $loop->iteration, 3, '0', STR_PAD_LEFT) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="pe-4 py-3 text-end">
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle rounded-pill px-3 py-2 fw-medium">
                                                    <i class="bi bi-people-fill me-1"></i> {{ $dept->users_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-5">
                                                <div class="text-muted d-flex flex-column align-items-center">
                                                    <i class="bi bi-inbox fs-1 mb-2 opacity-50"></i>
                                                    <p class="mb-0 fw-medium">{{ __('No departments found') }}</p>
                                                    <small>{{ __('Add a new department using the form.') }}</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Variables */
        :root {
            --md-radius: 16px;
            --md-shadow-rest: 0 2px 12px rgba(0, 0, 0, 0.04);
            --md-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.08);
            --google-blue: #1a73e8;
            --google-blue-focus: rgba(26, 115, 232, 0.15);
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

        /* Form Inputs (Google Style) */
        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #5f6368;
            z-index: 10;
        }

        .md-input {
            padding-left: 40px;
            height: 46px;
            border-radius: 8px;
            border: 1px solid #dadce0;
            font-size: 14px;
            color: #202124;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #fff;
        }

        .md-input:focus {
            border-color: var(--google-blue);
            box-shadow: 0 0 0 3px var(--google-blue-focus);
            outline: none;
        }

        .md-input::placeholder {
            color: #9aa0a6;
        }

        /* Button Enhancement */
        .md-btn {
            border-radius: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
        }

        .md-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
        }

        .md-btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
        }

        /* Table Styling */
        .table > :not(caption) > * > * {
            border-bottom-color: #f1f3f4;
        }
        
        .table-row-hover {
            transition: background-color 0.2s ease;
        }
        
        .table-row-hover:hover {
            background-color: #f8f9fa;
        }

        .table-row-hover:hover .dept-icon-box {
            background-color: #e8f0fe !important;
            color: var(--google-blue) !important;
            transition: all 0.2s ease;
        }

        .dept-icon-box {
            width: 40px;
            height: 40px;
            font-size: 18px;
            transition: all 0.2s ease;
        }

        /* Staggered Animations */
        [class*="stagger-"] {
            opacity: 0;
            animation: slideUpFade 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        .stagger-1 { animation-delay: 0.0s; }
        .stagger-2 { animation-delay: 0.1s; }
        .stagger-3 { animation-delay: 0.2s; }

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