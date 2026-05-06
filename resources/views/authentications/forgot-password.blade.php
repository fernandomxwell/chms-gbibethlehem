@extends('layouts.app')

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="login-logo">{{ config('app.name') }}</div>
            <p class="login-subtitle">@lang('auth.forgot_password_subtitle')</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">{{ __('email') }}</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-color:#d1d5db; background:#f8fafc; border-right:0;">
                        <i class="bi bi-envelope" style="color:#94a3b8;"></i>
                    </span>
                    <input type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email"
                        value="{{ old('email') }}"
                        required maxlength="255" autofocus
                        placeholder="nama@email.com"
                        style="border-left:0;">
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
                @enderror
            </div>

            <button class="btn btn-primary w-100 fw-semibold mb-3" type="submit" style="padding:.5625rem;">
                @lang('auth.send_reset_link')
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" style="font-size:.8125rem;">
                    &larr; @lang('auth.back_to_login')
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
