@extends('layouts.app')

@section('content')
    <h1>@lang('users.create')</h1>

    @include('layouts.error')

    <p class="text-muted">@lang('users.create_hint')</p>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">@lang('name'):</label>
            <input type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                class="form-control @error('name') is-invalid @enderror"
                maxlength="255"
                required
                autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">@lang('email'):</label>
            <input type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                maxlength="255"
                required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>@lang('users.password_auto_generated')
        </div>

        <button type="submit" class="btn btn-primary">@lang('submit')</button>
        <a class="btn btn-secondary" href="{{ url()->previous() }}">@lang('back')</a>
    </form>
@endsection
