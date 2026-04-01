@extends('user.layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <!-- Forgot Password Card -->
                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 28px;">
                    <!-- Decorative Header -->
                    <div class="card-header bg-transparent border-0 pt-5 pb-0 px-4 px-md-5 text-center position-relative">
                        <div class="position-absolute top-0 start-0 w-100 h-25" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); opacity: 0.1;"></div>
                        <div class="icon-circle-wrapper bg-white shadow-lg mx-auto mb-4" style="width: 90px; height: 90px; border-radius: 30px; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 70px; height: 70px; border-radius: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-key fs-2 text-white"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1">Reset Password</h3>
                        <p class="text-muted small">We'll send you a reset link</p>
                    </div>

                    <div class="card-body p-4 p-md-5 pt-0">
                        <!-- Success Message -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-4" role="alert" style="border-radius: 16px;">
                                <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                                <span class="small flex-grow-1">{{ session('status') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-4" role="alert" style="border-radius: 16px;">
                                <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                                <span class="small flex-grow-1">{{ $errors->first() }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Info Alert -->
                        <div class="alert alert-info bg-opacity-10 d-flex align-items-center py-2 px-3 mb-4" style="border-radius: 16px; border: none;">
                            <i class="bi bi-info-circle-fill text-info me-2 flex-shrink-0"></i>
                            <small class="text-info">Enter your email and we'll send you instructions to reset your password.</small>
                        </div>

                        <!-- Password Reset Form -->
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Input -->
                            <div class="form-group mb-4">
                                <label class="form-label fw-medium text-secondary mb-2">
                                    <i class="bi bi-envelope me-1"></i>Email Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 ps-3">
                                        <i class="bi bi-envelope fs-5 text-primary"></i>
                                    </span>
                                    <input type="email" name="email"
                                           class="form-control bg-light border-0 @error('email') is-invalid @enderror"
                                           placeholder="your@email.com"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus
                                           style="padding: 0.9rem 1rem;">
                                    @error('email')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-medium rounded-3 mb-4 position-relative overflow-hidden">
                                <span class="position-relative z-index-1">
                                    <i class="bi bi-send me-2"></i>
                                    Send Reset Link
                                </span>
                            </button>

                            <!-- Back to Login -->
                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}" class="text-decoration-none d-inline-flex align-items-center text-muted">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    <span class="small">Back to Login</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Support Info -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-headset me-1"></i>
                        Need help? <a href="#" class="text-decoration-none fw-medium">Contact Support</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern Forgot Password Styles */
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2) !important;
        }

        .icon-circle-wrapper {
            animation: float 3s ease-in-out infinite;
        }

        .form-control, .input-group-text {
            border-radius: 16px !important;
        }

        .form-control:focus {
            box-shadow: none;
            background-color: #eef2f6 !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .alert-info {
            background-color: rgba(13, 202, 240, 0.1);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Mobile Optimizations */
        @media (max-width: 575.98px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .icon-circle-wrapper {
                width: 70px !important;
                height: 70px !important;
            }

            .icon-circle-wrapper > div {
                width: 55px !important;
                height: 55px !important;
            }

            .icon-circle-wrapper i {
                font-size: 1.5rem !important;
            }

            h3 {
                font-size: 1.4rem !important;
            }

            .form-control, .btn {
                padding: 0.7rem !important;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .card-body {
                padding: 2rem !important;
            }
        }

        /* Touch-friendly improvements */
        .btn, a {
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
@endsection
