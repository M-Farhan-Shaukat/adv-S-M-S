@extends('admin.layouts.app')

@section('title', 'Admin Login')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100 p-3">
        <div class="w-100" style="max-width: 450px;">
            <!-- Brand/Logo Section -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center mb-3">
                    <div class="brand-circle bg-gradient-primary d-flex align-items-center justify-content-center"
                         style="width: 70px; height: 70px; border-radius: 18px;">
                        <i class="bi bi-shield-check fs-2 text-white"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-1">Admin Panel</h2>
                <p class="text-muted small">Secure access to your administrative dashboard</p>
            </div>

            <!-- Login Card -->
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h4 class="fw-semibold mb-1">Welcome Back</h4>
                        <p class="text-muted small">Please enter your credentials to continue</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger d-flex align-items-center py-2 px-3 mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <span class="small">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success d-flex align-items-center py-2 px-3 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span class="small">{{ session('status') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login.store') }}">
                        @csrf

                        <!-- Email Field -->
                        <div class="form-floating mb-3">
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   placeholder="name@example.com"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus>
                            <label for="email">
                                <i class="bi bi-envelope me-2 text-muted"></i>Email Address
                            </label>
                            @error('email')
                            <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-floating mb-2">
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   placeholder="Password"
                                   required>
                            <label for="password">
                                <i class="bi bi-key me-2 text-muted"></i>Password
                            </label>
                            <button type="button"
                                    class="btn btn-link position-absolute end-0 top-50 translate-middle-y me-2 p-0"
                                    style="z-index: 10;"
                                    onclick="togglePassword()">
                                <i id="passwordIcon" class="bi bi-eye fs-6 text-muted"></i>
                            </button>
                            @error('password')
                            <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a href="#" class="small text-decoration-none">
                                <i class="bi bi-question-circle me-1"></i>Forgot Password?
                            </a>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="btn btn-gradient-primary w-100 py-3 mb-3 rounded-3 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Sign In to Dashboard
                        </button>

                        <!-- Demo Credentials (Remove in production) -->
{{--                        <div class="alert alert-light bg-light border-0 p-3 rounded-3 mt-4">--}}
{{--                            <div class="d-flex align-items-center mb-2">--}}
{{--                                <i class="bi bi-info-circle text-primary me-2"></i>--}}
{{--                                <span class="small fw-semibold">Demo Credentials</span>--}}
{{--                            </div>--}}
{{--                            <div class="row g-2 small text-muted">--}}
{{--                                <div class="col-6">--}}
{{--                                    <div><strong>Admin:</strong> admin@example.com</div>--}}
{{--                                    <div class="font-monospace">••••••••</div>--}}
{{--                                </div>--}}
{{--                                <div class="col-6">--}}
{{--                                    <div><strong>Manager:</strong> manager@example.com</div>--}}
{{--                                    <div class="font-monospace">••••••••</div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4">
               
                <p class="small text-muted mt-1">
                    &copy; {{ date('Y') }} Admin Panel. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        /* Gradient Background */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46a1 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-gradient-primary:active {
            transform: translateY(0);
        }

        /* Form Styling */
        .form-floating > .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem 0.75rem;
            height: calc(3.5rem + 2px);
        }

        .form-floating > .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.1);
        }

        .form-floating > label {
            padding: 1rem 0.75rem;
            color: #6c757d;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        /* Brand Circle */
        .brand-circle {
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .brand-circle:hover {
            transform: scale(1.05);
        }

        /* Card Styling */
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        /* Password Toggle Button */
        .btn-link {
            color: #6c757d;
            text-decoration: none;
        }

        .btn-link:hover {
            color: #667eea;
        }

        /* Alert Styling */
        .alert {
            border-radius: 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 576px) {
            .card-body {
                padding: 1.5rem !important;
            }

            .btn-gradient-primary {
                padding: 0.75rem !important;
            }

            .brand-circle {
                width: 60px !important;
                height: 60px !important;
            }

            .brand-circle i {
                font-size: 1.5rem !important;
            }

            h2 {
                font-size: 1.5rem !important;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeIn 0.5s ease-out;
        }

        /* Demo Credentials Styling */
        .alert-light {
            background-color: #f8f9fa;
        }

        .font-monospace {
            font-family: 'SF Mono', Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
            letter-spacing: 2px;
        }

        /* Floating Labels Adjustment */
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            opacity: 0.85;
            transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
            color: #667eea;
        }

        /* Input Group Text Fix */
        .form-floating .btn-link {
            padding: 0.375rem 0.75rem;
        }

        /* Hover Effects */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 20px 35px rgba(0,0,0,0.1) !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46a1 100%);
        }
    </style>

    <!-- JavaScript -->
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');

            if (password) {
                if (password.type === 'password') {
                    password.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    password.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        }

        // Add floating label effect
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-floating input');
            inputs.forEach(input => {
                if (input.value) {
                    input.classList.add('has-value');
                }

                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
        });
    </script>
@endsection
