@extends('layouts.app')

@section('content')
    <h1>@lang('congregants.import')</h1>

    @include('layouts.error')

    @if(isset($result))
        <div class="alert {{ $result['imported'] > 0 ? 'alert-success' : 'alert-warning' }}">
            <strong>@lang('congregants.import_result')</strong><br>
            @lang('congregants.import_success_count', ['count' => $result['imported']])
            &nbsp;|&nbsp;
            @lang('congregants.import_failed_count', ['count' => $result['failed']])
        </div>

        @if(!empty($result['errors']))
            <div class="card mb-3">
                <div class="card-header text-danger fw-bold">@lang('congregants.import_errors')</div>
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
            <h5 class="card-title">@lang('congregants.import_instructions_title')</h5>
            <p class="card-text">@lang('congregants.import_instructions')</p>
            <ul class="mb-3">
                <li><code>honorific_title</code> — bpk / ibu / sdr / sdri (@lang('optional'))</li>
                <li><code>full_name</code> — @lang('congregants.import_col_full_name')</li>
                <li><code>gender</code> — male / female</li>
                <li><code>date_of_birth</code> — @lang('congregants.import_col_date') (@lang('optional'))</li>
                <li><code>phone_number</code> — @lang('congregants.import_col_phone') (@lang('optional'))</li>
                <li><code>email</code> — @lang('optional')</li>
                <li><code>date_of_baptism</code> — @lang('congregants.import_col_date') (@lang('optional'))</li>
                <li><code>status</code> — member / sympathizer</li>
            </ul>
            <a href="{{ route('congregants.template') }}" class="btn btn-outline-secondary btn-sm">@lang('congregants.download_template')</a>
        </div>
    </div>

    <form action="{{ route('congregants.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">@lang('congregants.import_file')</label>
            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".csv">
            @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">@lang('congregants.import_submit')</button>
        <a class="btn btn-secondary" href="{{ route('congregants.index') }}">@lang('back')</a>
    </form>
@endsection
