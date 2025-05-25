@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card auth-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ __('Login') }}</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="login" class="form-label">{{ __('Email or Mobile') }}</label>
                        <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" 
                               name="login" value="{{ old('login') }}" required autocomplete="login" autofocus
                               placeholder="Enter email or mobile number">

                        @error('login')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password"
                               placeholder="Enter your password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2">
                            {{ __('Login') }}
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        Don't have an account? 
                        <a href="{{ route('register') }}">{{ __('Register') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection