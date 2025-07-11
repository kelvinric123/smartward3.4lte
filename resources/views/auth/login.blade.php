@extends('adminlte::master')

@section('title', config('app.name', 'Medical Referral System') . ' - Login')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        .login-page {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            width: 450px;
            margin: 0;
        }
        .login-card-body {
            padding: 35px;
        }
        .input-group {
            margin-bottom: 1.8rem !important;
        }
        .input-group input, .input-group select {
            padding: 12px;
            font-size: 1.1rem;
            height: auto;
        }
        .btn-primary {
            padding: 12px;
            font-size: 1.1rem;
        }
        .login-logo {
            margin-bottom: 30px;
        }
        .login-logo img {
            margin-bottom: 15px;
        }
        .login-logo b {
            font-weight: 700;
            font-size: 2rem;
        }
        .card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .card-header h3 {
            font-size: 1.5rem;
            padding: 10px 0;
        }
    </style>
    @stack('css')
    @yield('css')
@stop

@section('classes_body', 'login-page')

@section('body')
    <div class="login-box">
        <!-- Logo -->
        <div class="login-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset(config('adminlte.logo_img')) }}" alt="{{ config('adminlte.logo_img_alt') }}" height="70">
                <div><b>{{ config('app.name', 'Qmed_Referral3.4') }}</b></div>
            </a>
        </div>

        <!-- Card -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title float-none text-center">Sign in to start your session</h3>
            </div>
            <div class="card-body login-card-body">
                <form action="{{ route('login') }}" method="post">
                    @csrf

                    <!-- Email field -->
                    <div class="input-group">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="Email" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password field -->
                    <div class="input-group">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Role selection field -->
                    <div class="input-group">
                        <select name="role" class="form-control @error('role') is-invalid @enderror">
                            <option value="" disabled selected>Select Role</option>
                            <option value="super-admin" {{ old('role') == 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="hospital-admin" {{ old('role') == 'hospital-admin' ? 'selected' : '' }}>Hospital Admin</option>
                            <option value="consultant" {{ old('role') == 'consultant' ? 'selected' : '' }}>Consultant</option>
                            <option value="gp-doctor" {{ old('role') == 'gp-doctor' ? 'selected' : '' }}>GP Doctor</option>
                            <option value="booking-agent" {{ old('role') == 'booking-agent' ? 'selected' : '' }}>Booking Agent</option>
                            <option value="nurse" {{ old('role') == 'nurse' ? 'selected' : '' }}>Nurse</option>
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user-tag"></span>
                            </div>
                        </div>
                        @error('role')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Login field -->
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-sign-in-alt mr-2"></i> Login
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-4 mb-1 text-center">
                    <p class="mb-0">
                        <a href="{{ route('password.request') }}">I forgot my password</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop