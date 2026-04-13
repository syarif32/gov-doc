@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">{{ __('Add Department') }}</div>
                <div class="card-body">
                    <form action="{{ route('admin.departments.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold">{{ __('Name (TK)') }}</label>
                            <input type="text" name="name_tk" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">{{ __('Name (RU)') }}</label>
                            <input type="text" name="name_ru" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name_en" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">{{ __('Departments') }}</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="bg-light small">
                            <tr>
                                <th class="ps-4">{{ __('Department Name') }}</th>
                                <th>{{ __('Total Users') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $dept)
                                <tr>
                                    <td class="ps-4">{{ $dept->name }}</td>
                                    <td><span class="badge bg-info text-dark">{{ $dept->users_count }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
