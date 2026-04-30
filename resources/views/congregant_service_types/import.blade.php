@extends('layouts.app')

@section('content')
    <h1>@lang('congregant_services.import')</h1>

    @include('layouts.error')

    @if(isset($result))
        <div class="alert {{ $result['imported'] > 0 ? 'alert-success' : 'alert-warning' }}">
            <strong>@lang('congregant_services.import_result')</strong><br>
            @lang('congregant_services.import_success_count', ['count' => $result['imported']])
            &nbsp;|&nbsp;
            @lang('congregant_services.import_failed_count', ['count' => $result['failed']])
        </div>

        @if(!empty($result['errors']))
            <div class="card mb-3">
                <div class="card-header text-danger fw-bold">@lang('congregant_services.import_errors')</div>
                <ul class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @foreach($result['errors'] as $error)
                        <li class="list-group-item list-group-item-danger small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">@lang('congregant_services.import_instructions_title')</h5>
            <p class="card-text">@lang('congregant_services.import_instructions')</p>
            <ul class="mb-3">
                <li><code>full_name</code> — @lang('congregant_services.import_col_full_name')</li>
                <li><code>can_serve_consecutively</code> — @lang('congregant_services.import_col_can_serve')</li>
                <li><code>activity</code> — @lang('congregant_services.import_col_activity')</li>
                <li><code>service_type</code> — @lang('congregant_services.import_col_service_type')</li>
            </ul>
            <p class="text-muted small">@lang('congregant_services.import_note_multirow')</p>
            <a href="{{ route('congregant_services.template') }}" class="btn btn-outline-secondary btn-sm">@lang('congregant_services.download_template')</a>
        </div>
    </div>

    <form action="{{ route('congregant_services.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">@lang('congregant_services.import_file')</label>
            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".csv">
            @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">@lang('congregant_services.import_submit')</button>
        <a class="btn btn-secondary" href="{{ route('congregant_services.index') }}">@lang('back')</a>
    </form>
@endsection
