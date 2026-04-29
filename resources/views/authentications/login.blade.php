@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection

@section('content')
    <div class="row my-3">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="col-lg-4 offset-lg-4">
                @include('layouts.error')

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('email') }}</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required maxlength='255' autofocus>

                    @error('email')
                    <div class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('password') }}</label>
                    <div class="input-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="bi bi-eye-slash" id="toggle-icon"></i>
                        </button>
                    </div>

                    @error('password')
                    <div class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        <small>{{ __('auth.remember') }}</small>
                    </label>

                    @if(Route::has('password.request'))
                        <div class="float-end">
                            <a href="{{ route('password.request') }}" class="float-right">
                                <small>{{ __('auth.forgot_password') }}?</small>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary fw-bold" type=" submit">{{ __('auth.login') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('javascript')
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        }
    </script>
@endsection