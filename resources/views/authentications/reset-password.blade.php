@extends('layouts.app')

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="login-logo">{{ config('app.name') }}</div>
            <p class="login-subtitle">@lang('auth.reset_password_subtitle')</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('email') }}</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-color:#d1d5db; background:#f8fafc; border-right:0;">
                        <i class="bi bi-envelope" style="color:#94a3b8;"></i>
                    </span>
                    <input type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email"
                        value="{{ old('email', $email) }}"
                        required maxlength="255" autofocus
                        placeholder="nama@email.com"
                        style="border-left:0;">
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $errors->first('email') }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('auth.new_password') }}</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-color:#d1d5db; background:#f8fafc; border-right:0;">
                        <i class="bi bi-lock" style="color:#94a3b8;"></i>
                    </span>
                    <input type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password"
                        required autocomplete="new-password"
                        placeholder="••••••••"
                        style="border-left:0; border-right:0;">
                    <button class="input-group-text" type="button" onclick="togglePassword('password', 'toggle-icon-pw')"
                        style="border-color:#d1d5db; background:#f8fafc; cursor:pointer; border-left:0;">
                        <i class="bi bi-eye-slash" id="toggle-icon-pw" style="color:#94a3b8;"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $errors->first('password') }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">{{ __('auth.confirm_password') }}</label>
                <div class="input-group">
                    <span class="input-group-text" style="border-color:#d1d5db; background:#f8fafc; border-right:0;">
                        <i class="bi bi-lock-fill" style="color:#94a3b8;"></i>
                    </span>
                    <input type="password"
                        class="form-control"
                        id="password_confirmation" name="password_confirmation"
                        required autocomplete="new-password"
                        placeholder="••••••••"
                        style="border-left:0; border-right:0;">
                    <button class="input-group-text" type="button" onclick="togglePassword('password_confirmation', 'toggle-icon-confirm')"
                        style="border-color:#d1d5db; background:#f8fafc; cursor:pointer; border-left:0;">
                        <i class="bi bi-eye-slash" id="toggle-icon-confirm" style="color:#94a3b8;"></i>
                    </button>
                </div>
            </div>

            <button class="btn btn-primary w-100 fw-semibold" type="submit" style="padding:.5625rem;">
                @lang('auth.reset_password')
            </button>
        </form>
    </div>
</div>
@endsection

@section('javascript')
<script>
    function togglePassword(fieldId, iconId) {
        const pw   = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        icon.classList.toggle('bi-eye-slash', !show);
        icon.classList.toggle('bi-eye', show);
    }
</script>
@endsection
