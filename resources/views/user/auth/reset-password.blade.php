@extends('user.layouts.app')

@section('title', 'Reset Password')

@section('content')
    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <!-- Reset Password Card -->
                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 28px;">
                    <!-- Decorative Header -->
                    <div class="card-header bg-transparent border-0 pt-5 pb-0 px-4 px-md-5 text-center position-relative">
                        <div class="position-absolute top-0 start-0 w-100 h-25" style="background: linear-gradient(135deg, #20c997 0%, #198754 100%); opacity: 0.1;"></div>
                        <div class="icon-circle-wrapper bg-white shadow-lg mx-auto mb-4" style="width: 90px; height: 90px; border-radius: 30px; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 70px; height: 70px; border-radius: 25px; background: linear-gradient(135deg, #20c997 0%, #198754 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-shield-lock fs-2 text-white"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1">Set New Password</h3>
                        <p class="text-muted small">Create a strong, secure password</p>
                    </div>

                    <div class="card-body p-4 p-md-5 pt-0">
                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4" role="alert" style="border-radius: 16px;">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0 mt-1"></i>
                                    <div class="small flex-grow-1">
                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-4" role="alert" style="border-radius: 16px;">
                                <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                                <span class="small flex-grow-1">{{ session('status') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Password Reset Form -->
                        <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                            @csrf

                            <!-- Token -->
                            <input type="hidden" name="token" value="{{ $token ?? '' }}">

                            <!-- Email (Readonly) -->
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
                                           value="{{ $email ?? old('email') }}"
                                           readonly
                                           required
                                           style="padding: 0.9rem 1rem; background-color: #eef2f6 !important;">
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="form-group mb-4">
                                <label class="form-label fw-medium text-secondary mb-2">
                                    <i class="bi bi-lock me-1"></i>New Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 ps-3">
                                        <i class="bi bi-lock fs-5 text-primary"></i>
                                    </span>
                                    <input type="password" name="password"
                                           class="form-control bg-light border-0 @error('password') is-invalid @enderror"
                                           placeholder="Enter new password"
                                           id="password"
                                           required
                                           autofocus
                                           style="padding: 0.9rem 1rem;">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePassword('password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @error('password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group mb-4">
                                <label class="form-label fw-medium text-secondary mb-2">
                                    <i class="bi bi-check-circle me-1"></i>Confirm Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 ps-3">
                                        <i class="bi bi-check-circle fs-5 text-primary"></i>
                                    </span>
                                    <input type="password" name="password_confirmation"
                                           class="form-control bg-light border-0"
                                           placeholder="Confirm new password"
                                           id="password_confirmation"
                                           required
                                           style="padding: 0.9rem 1rem;">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePassword('password_confirmation', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Password Strength Meter -->
                            <div class="mb-4" id="passwordStrengthSection">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted fw-medium">Password Strength</small>
                                    <small class="text-muted fw-medium" id="passwordStrengthText">Weak</small>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 10px;">
                                    <div class="progress-bar bg-danger" id="passwordStrengthBar" style="width: 25%; border-radius: 10px;"></div>
                                </div>
                            </div>

                            <!-- Password Match Indicator -->
                            <div class="mb-4">
                                <div id="passwordMatch" class="small"></div>
                            </div>

                            <!-- Password Requirements -->
                            <div class="alert alert-light bg-light border-0 py-3 px-3 rounded-3 mb-4">
                                <small class="text-muted d-flex flex-column gap-1">
                                    <span class="fw-semibold text-success">
                                        <i class="bi bi-shield-check me-1"></i>Password Requirements:
                                    </span>
                                    <span class="ms-3">• Minimum 8 characters</span>
                                    <span class="ms-3">• At least one uppercase letter</span>
                                    <span class="ms-3">• At least one number</span>
                                    <span class="ms-3">• At least one special character (!@#$%^&*)</span>
                                </small>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100 py-3 fw-medium rounded-3 mb-4 position-relative overflow-hidden">
                                <span class="position-relative z-index-1">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Reset Password
                                </span>
                            </button>

                            <!-- Back to Login -->
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none d-inline-flex align-items-center text-muted">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    <span class="small">Return to Login</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Note -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock-check me-1"></i>
                        This is a secure, encrypted connection
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Password strength checker and match validator
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            const matchDiv = document.getElementById('passwordMatch');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');

            if (password) {
                password.addEventListener('input', function() {
                    const value = this.value;

                    // Calculate strength
                    let strength = 0;
                    if (value.length >= 8) strength += 25;
                    if (/[A-Z]/.test(value)) strength += 25;
                    if (/[0-9]/.test(value)) strength += 25;
                    if (/[!@#$%^&*]/.test(value)) strength += 25;

                    // Update bar
                    strengthBar.style.width = strength + '%';

                    // Update color and text
                    if (strength <= 25) {
                        strengthBar.className = 'progress-bar bg-danger';
                        strengthText.textContent = 'Weak';
                        strengthText.className = 'text-muted fw-medium text-danger';
                    } else if (strength <= 50) {
                        strengthBar.className = 'progress-bar bg-warning';
                        strengthText.textContent = 'Fair';
                        strengthText.className = 'text-muted fw-medium text-warning';
                    } else if (strength <= 75) {
                        strengthBar.className = 'progress-bar bg-info';
                        strengthText.textContent = 'Good';
                        strengthText.className = 'text-muted fw-medium text-info';
                    } else {
                        strengthBar.className = 'progress-bar bg-success';
                        strengthText.textContent = 'Strong';
                        strengthText.className = 'text-muted fw-medium text-success';
                    }

                    // Check match if confirm has value
                    if (confirm && confirm.value.length > 0) {
                        checkPasswordMatch();
                    }
                });
            }

            if (confirm) {
                confirm.addEventListener('input', checkPasswordMatch);
            }

            function checkPasswordMatch() {
                if (confirm.value.length > 0) {
                    if (password.value === confirm.value) {
                        matchDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Passwords match</span>';
                        confirm.classList.remove('is-invalid');
                        confirm.classList.add('is-valid');
                    } else {
                        matchDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle-fill me-1"></i> Passwords do not match</span>';
                        confirm.classList.remove('is-valid');
                        confirm.classList.add('is-invalid');
                    }
                } else {
                    matchDiv.innerHTML = '';
                    confirm.classList.remove('is-valid', 'is-invalid');
                }
            }
        });
    </script>

    <style>
        /* Modern Reset Password Styles */
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(32, 201, 151, 0.2) !important;
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

        input[readonly] {
            cursor: not-allowed;
            opacity: 0.9;
        }

        .btn-success {
            background: linear-gradient(135deg, #20c997 0%, #198754 100%);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-success::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(32, 201, 151, 0.3);
        }

        .btn-success:hover::before {
            left: 100%;
        }

        .progress {
            background-color: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.3s ease;
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
