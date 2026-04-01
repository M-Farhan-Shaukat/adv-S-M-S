@extends('user.layouts.app')

@section('title', 'Sign Up')

@section('content')
    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <!-- Registration Card with Modern Design -->
                <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 28px;">
                    <!-- Decorative Header -->
                    <div class="card-header bg-transparent border-0 pt-5 pb-0 px-4 px-md-5 text-center position-relative">
                        <div class="position-absolute top-0 start-0 w-100 h-25" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); opacity: 0.1;"></div>
                        <div class="icon-circle-wrapper bg-white shadow-lg mx-auto mb-4" style="width: 90px; height: 90px; border-radius: 30px; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 70px; height: 70px; border-radius: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-plus fs-2 text-white"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1">Become a Member</h3>
                        <p class="text-muted small">Join our community today</p>
                    </div>

                    <div class="card-body p-4 p-md-5 pt-0">
                        <!-- Success Message -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-4" role="alert" style="border-radius: 16px;">
                                <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                                <span class="small flex-grow-1">{{ session('success') }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Registration Form -->
                        <form method="POST" action="{{ route('register.store') }}" id="registerForm">
                            @csrf

                            <div class="row g-3 g-md-4">
                                <!-- Name -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium text-secondary mb-2">
                                        <i class="bi bi-person me-1"></i>Full Name
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 ps-3">
                                            <i class="bi bi-person fs-5 text-primary"></i>
                                        </span>
                                        <input type="text" name="name"
                                               class="form-control bg-light border-0 @error('name') is-invalid @enderror"
                                               placeholder="Full Name"
                                               value="{{ old('name') }}"
                                               required
                                               style="padding: 0.9rem 1rem;">
                                        @error('name')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium text-secondary mb-2">
                                        <i class="bi bi-envelope me-1"></i>Email Address
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 ps-3">
                                            <i class="bi bi-envelope fs-5 text-primary"></i>
                                        </span>
                                        <input type="email" name="email"
                                               class="form-control bg-light border-0 @error('email') is-invalid @enderror"
                                               placeholder="john@example.com"
                                               value="{{ old('email') }}"
                                               required
                                               style="padding: 0.9rem 1rem;">
                                        @error('email')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- cnic -->
                                {{--<div class="col-12 col-md-6">
                                    <label class="form-label fw-medium text-secondary mb-2">
                                        <i class="bi bi-card-text me-1"></i>CNIC
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 ps-3">
                                            <i class="bi bi-card-text fs-5 text-primary"></i>
                                        </span>
                                        <input type="text" name="cnic"
                                               class="form-control bg-light border-0 @error('cnic') is-invalid @enderror"
                                               placeholder="12345-6789012-3"
                                               value="{{ old('cnic') }}"
                                               required
                                               style="padding: 0.9rem 1rem;">
                                        @error('cnic')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>--}}

                                <!-- Password -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium text-secondary mb-2">
                                        <i class="bi bi-lock me-1"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 ps-3">
                                            <i class="bi bi-lock fs-5 text-primary"></i>
                                        </span>
                                        <input type="password" name="password"
                                               class="form-control bg-light border-0 @error('password') is-invalid @enderror"
                                               placeholder="Create password"
                                               id="password"
                                               required
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
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium text-secondary mb-2">
                                        <i class="bi bi-check-circle me-1"></i>Confirm Password
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 ps-3">
                                            <i class="bi bi-check-circle fs-5 text-primary"></i>
                                        </span>
                                        <input type="password" name="password_confirmation"
                                               class="form-control bg-light border-0"
                                               placeholder="Confirm password"
                                               id="password_confirmation"
                                               required
                                               style="padding: 0.9rem 1rem;">
                                        <button class="btn btn-light border-0" type="button" onclick="togglePassword('password_confirmation', this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Strength Meter -->

                            <div class="mt-4" id="passwordStrengthSection" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted fw-medium">Password Strength</small>
                                    <small class="text-muted fw-medium" id="passwordStrengthText">Weak</small>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 10px;">
                                    <div class="progress-bar bg-danger" id="passwordStrengthBar" style="width: 0%; border-radius: 10px;"></div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Use 8+ characters with uppercase, number & special character
                                </small>
                            </div>

                            <!-- Password Match Indicator -->
                            <div class="mt-3">
                                <div id="passwordMatch" class="small"></div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-medium rounded-3 mb-4 position-relative overflow-hidden">
                                <span class="position-relative z-index-1">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Create Account
                                </span>
                            </button>


                            <!-- Divider -->
                            <div class="position-relative my-4">
                                <hr class="opacity-25">
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">already registered?</span>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary px-5 py-2 rounded-3 fw-medium">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Sign In Instead
                                </a>
                            </div>
                        </form>
                    </div>
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
            const strengthSection = document.getElementById('passwordStrengthSection');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');

            if (password) {
                password.addEventListener('input', function() {
                    const value = this.value;

                    // Show strength section if password has value
                    if (value.length > 0) {
                        strengthSection.style.display = 'block';

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
                    } else {
                        strengthSection.style.display = 'none';
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
        $(document).ready(function() {
            $('select[name="city_id"]').select2({
                placeholder: "Select City",
                width: '100%'
            });
        });

    </script>

    <style>
        /* Modern Register Styles */
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

        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
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

            .row.g-3 {
                --bs-gutter-y: 1rem;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .card-body {
                padding: 2rem !important;
            }
        }

        /* Touch-friendly improvements */
        .btn, .form-check-label, a {
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }

        /* Progress Bar */
        .progress {
            background-color: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.3s ease;
        }
    </style>

@endsection
