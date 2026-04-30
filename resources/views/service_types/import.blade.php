@extends('layouts.app')

@section('content')
    <h1>@lang('service_types.import')</h1>

    @include('layouts.error')

    @if(isset($result))
        <div class="alert {{ $result['imported'] > 0 ? 'alert-success' : 'alert-warning' }}">
            <strong>@lang('service_types.import_result')</strong><br>
            @lang('service_types.import_success_count', ['count' => $result['imported']])
            &nbsp;|&nbsp;
            @lang('service_types.import_failed_count', ['count' => $result['failed']])
        </div>

        @if(!empty($result['errors']))
            <div class="card mb-3">
                <div class="card-header text-danger fw-bold">@lang('service_types.import_errors')</div>
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
            <h5 class="card-title">@lang('service_types.import_instructions_title')</h5>
            <p class="card-text">@lang('service_types.import_instructions')</p>
            <ul class="mb-3">
                <li><code>name</code> — @lang('service_types.import_col_name')</li>
                <li><code>description</code> — @lang('optional')</li>
                <li><code>activities</code> — @lang('service_types.import_col_activities')</li>
            </ul>
            <a href="{{ route('service_types.template') }}" class="btn btn-outline-secondary btn-sm">@lang('service_types.download_template')</a>
        </div>
    </div>

    <form action="{{ route('service_types.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">@lang('service_types.import_file')</label>
            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".csv">
            @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">@lang('service_types.import_submit')</button>
        <a class="btn btn-secondary" href="{{ route('service_types.index') }}">@lang('back')</a>
    </form>
@endsection
