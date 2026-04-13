@extends('layouts.admin')

@section('content')
    <div class="container" style="max-width: 600px;">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">{{ __('Edit Document Details') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('docs.update', $document->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Title') }}</label>
                        <input type="text" name="title" class="form-control" value="{{ $document->title }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="4">{{ $document->description }}</textarea>
                    </div>

                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('You are only editing the name and description. To change the file, please delete and re-upload.') }}
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('docs.index') }}" class="btn btn-light">{{ __('Back') }}</a>
                        <button type="submit" class="btn btn-primary px-4">{{ __('Save Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
