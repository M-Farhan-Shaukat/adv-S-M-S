@extends('user.layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <!-- Login Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <!-- Header -->
                        <div class="text-center mb-3 mb-md-4">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-2 mb-md-3"
                                 style="width: 55px; height: 55px;">
                                <i class="bi bi-shield-lock fs-3"></i>
                            </div>
                            <h3 class="fw-bold mb-1 fs-4 fs-md-3">Welcome Back</h3>
                            <p class="text-muted small mb-0">Sign in to your account</p>
                        </div>

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 small" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle me-2 flex-shrink-0"></i>
                                    <span class="flex-grow-1">{{ $errors->first() }}</span>
                                </div>
                                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Session Messages -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 small" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle me-2 flex-shrink-0"></i>
                                    <span class="flex-grow-1">{{ session('status') }}</span>
                                </div>
                                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login.store') }}">
                            @csrf

                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label class="form-label fw-medium small text-secondary mb-1">Email Address</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 ps-3">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" name="email"
                                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                                           placeholder="your@email.com"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus>
                                    @error('email')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="form-group mb-3">
                                <label class="form-label fw-medium small text-secondary mb-1">Password</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 ps-3">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" name="password"
                                           class="form-control border-start-0 @error('password') is-invalid @enderror"
                                           placeholder="••••••••"
                                           required>
                                    <button class="btn btn-outline-secondary bg-light border"
                                            type="button"
                                            onclick="togglePassword(this)"
                                            style="border-top-right-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remember & Forgot -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-2">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label small text-muted" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-decoration-none small">
                                    <i class="bi bi-question-circle me-1"></i>Forgot password?
                                </a>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 py-2 py-md-2 fw-medium rounded-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="position-relative my-4">
                            <hr class="opacity-25">
                            <div class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                                or
                            </div>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-muted small mb-1">Don't have an account?</p>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm py-2 px-4 rounded-3">
                                <i class="bi bi-person-plus me-1"></i>
                                Create Account
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock me-1"></i>
                        Secure, encrypted connection
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(element) {
            const passwordInput = element.closest('.input-group').querySelector('input[name="password"]');
            const icon = element.querySelector('i');

            if (passwordInput) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        }
    </script>

    <style>
        .card {
            border-radius: 20px !important;
            transition: all 0.3s ease;
        }

        .icon-circle {
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-outline-primary {
            border: 1.5px solid #667eea;
            color: #667eea;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
        }

        .input-group-text {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .input-group .btn {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border: 1px solid #dee2e6;
            border-left: none;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        @media (max-width: 575.98px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .icon-circle {
                width: 50px !important;
                height: 50px !important;
            }

            .icon-circle i {
                font-size: 1.5rem !important;
            }

            h3 {
                font-size: 1.35rem !important;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .card-body {
                padding: 2rem !important;
            }
        }

        /* Touch-friendly improvements */
        .btn, .form-check, a {
            cursor: pointer;
        }

        .input-group .btn {
            padding: 0.375rem 0.75rem;
        }

        @media (max-width: 576px) {
            .input-group .btn {
                padding: 0.375rem 0.6rem;
            }
        }
    </style>
@endsection
