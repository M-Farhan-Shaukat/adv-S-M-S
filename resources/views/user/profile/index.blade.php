@extends('user.layouts.app')

@section('content')
    <div class="container-fluid px-0">
        <!-- Header with gradient background -->
        <div class="profile-header mb-4">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle bg-white shadow-lg me-3" style="width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person fs-1 text-primary"></i>
                        </div>
                        <div class="text-white">
                            <h4 class="fw-bold mb-1">My Profile</h4>
                            <p class="mb-0 opacity-75 small d-none d-sm-block">Manage your personal information and account settings</p>
                            <p class="mb-0 opacity-75 small d-sm-none">Profile Settings</p>
                        </div>

                        <!-- Desktop back button -->
                        <a href="{{ route('user.dashboard') }}" class="btn btn-light btn-sm rounded-pill ms-auto px-4 py-2 d-none d-md-flex align-items-center">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>

                        <!-- Mobile back button - hidden because app layout already has navbar -->
                        <!-- The mobile navbar with hamburger menu is already in the layout -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div id="successMsg" class="alert alert-success alert-dismissible fade show d-none mx-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span id="successMsgText"></span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Profile Form Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card border-0 shadow-lg mx-3 mx-md-0">
                    <div class="card-body p-4 p-md-5">
                        <form id="profileForm">
                            @csrf
                            @method('PUT')

                            <!-- Personal Information Section -->
                            <h5 class="fw-bold mb-4 pb-2 border-bottom">
                                <i class="bi bi-person-circle text-primary me-2"></i>
                                Personal Information
                            </h5>

                            <div class="row g-4">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary mb-2">
                                        <i class="bi bi-person me-1"></i>Full Name
                                    </label>
                                    <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person text-primary"></i>
                                    </span>
                                        <input type="text" name="name" id="name"
                                               class="form-control border-start-0 @error('name') is-invalid @enderror"
                                               value="{{ old('name', auth()->user()->name) }}"
                                               placeholder="Enter your full name">
                                        <div class="invalid-feedback" id="error_name"></div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary mb-2">
                                        <i class="bi bi-envelope me-1"></i>Email Address
                                    </label>
                                    <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-primary"></i>
                                    </span>
                                        <input type="email" name="email" id="email"
                                               class="form-control border-start-0 @error('email') is-invalid @enderror"
                                               value="{{ old('email', auth()->user()->email) }}"
                                               placeholder="your@email.com">
                                        <div class="invalid-feedback" id="error_email"></div>
                                    </div>
                                </div>


                                <!-- CNIC -->
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary mb-2">
                                        <i class="bi bi-card-text me-1"></i>CNIC
                                    </label>
                                    <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-card-text text-primary"></i>
                                    </span>
                                        <input type="text" name="cnic" id="cnic"
                                               class="form-control border-start-0 @error('cnic') is-invalid @enderror"
                                               value="{{ old('cnic', auth()->user()->cnic) }}"
                                               placeholder="12345-6789012-3">
                                        <div class="invalid-feedback" id="error_cnic"></div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="bi bi-info-circle me-1"></i>Format: 12345-6789012-3
                                    </small>
                                </div>
                            </div>

                            <!-- Password Change Section -->
                            <div class="mt-5">
                                <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                                    <i class="bi bi-shield-lock text-warning me-2 fs-4"></i>
                                    <h5 class="fw-bold mb-0">Security Settings</h5>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" id="changePasswordSwitch" name="change_password" value="1">
                                    <label class="form-check-label fw-medium" for="changePasswordSwitch">
                                        <i class="bi bi-key me-1"></i> Change Password
                                    </label>
                                    <small class="text-muted d-block mt-1">Enable to update your password</small>
                                </div>

                                <div id="passwordFields" style="display:none;">
                                    <div class="row g-4">
                                        <!-- Current Password -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-secondary mb-2">
                                                <i class="bi bi-lock me-1"></i>Current Password
                                            </label>
                                            <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-lock text-primary"></i>
                                            </span>
                                                <input type="password" name="current_password" id="current_password"
                                                       class="form-control border-start-0 @error('current_password') is-invalid @enderror"
                                                       placeholder="Current password">
                                                <button class="btn btn-outline-secondary bg-light border" type="button"
                                                        onclick="togglePassword('current_password', this)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <div class="invalid-feedback" id="error_current_password"></div>
                                            </div>
                                        </div>

                                        <!-- New Password -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-secondary mb-2">
                                                <i class="bi bi-key me-1"></i>New Password
                                            </label>
                                            <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-key text-primary"></i>
                                            </span>
                                                <input type="password" name="password" id="password"
                                                       class="form-control border-start-0 @error('password') is-invalid @enderror"
                                                       placeholder="New password">
                                                <button class="btn btn-outline-secondary bg-light border" type="button"
                                                        onclick="togglePassword('password', this)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <div class="invalid-feedback" id="error_password"></div>
                                            </div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-medium text-secondary mb-2">
                                                <i class="bi bi-check-circle me-1"></i>Confirm Password
                                            </label>
                                            <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-check-circle text-primary"></i>
                                            </span>
                                                <input type="password" name="password_confirmation" id="password_confirmation"
                                                       class="form-control border-start-0"
                                                       placeholder="Confirm password">
                                                <button class="btn btn-outline-secondary bg-light border" type="button"
                                                        onclick="togglePassword('password_confirmation', this)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Strength Indicator -->
                                    <div class="mt-3" id="passwordStrength" style="display:none;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">Password strength</small>
                                            <small class="text-muted" id="passwordStrengthText">Weak</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-danger" id="passwordStrengthBar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Minimum 8 characters with at least one uppercase, number, and special character
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex flex-column flex-sm-row gap-3 mt-5 pt-3">
                                <button type="submit" class="btn btn-primary flex-fill py-2 rounded-3">
                                    <i class="bi bi-check-circle me-2"></i>Update Profile
                                </button>
                                <button type="reset" class="btn btn-outline-secondary flex-fill py-2 rounded-3">
                                    <i class="bi bi-x-circle me-2"></i>Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .profile-header .card {
            border-radius: 20px;
            overflow: hidden;
        }

        .avatar-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
        }

        .form-control, .input-group-text {
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .input-group-text {
            background-color: #f8fafc;
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

        .btn-outline-secondary {
            border-color: #e2e8f0;
        }

        .btn-outline-secondary:hover {
            background-color: #f8fafc;
            border-color: #cbd5e0;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .border-bottom {
            border-bottom: 2px solid #e2e8f0 !important;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .profile-header .card-body {
                padding: 1.5rem !important;
            }

            .avatar-circle {
                width: 45px !important;
                height: 45px !important;
            }

            .avatar-circle i {
                font-size: 1.3rem !important;
            }

            h4 {
                font-size: 1.2rem !important;
                margin-bottom: 0.25rem !important;
            }

            .btn {
                padding: 0.5rem 0.8rem !important;
                font-size: 0.9rem;
            }

            .container-fluid {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* Adjust header for mobile */
            .profile-header .d-flex {
                flex-wrap: wrap;
            }

            .text-white {
                flex: 1;
                min-width: 150px;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1.25rem !important;
            }

            .row.g-4 {
                --bs-gutter-y: 1rem;
            }

            .col-md-6, .col-md-4 {
                margin-bottom: 0.5rem;
            }

            .d-flex.gap-3 {
                gap: 0.75rem !important;
            }

            /* Adjust avatar and text for very small screens */
            .avatar-circle {
                width: 40px !important;
                height: 40px !important;
                margin-right: 0.75rem !important;
            }

            .avatar-circle i {
                font-size: 1.2rem !important;
            }

            h4 {
                font-size: 1.1rem !important;
            }

            .text-white p {
                font-size: 0.7rem !important;
            }
        }

        /* Fix for cards on mobile */
        @media (max-width: 480px) {
            .mx-3 {
                margin-left: 0.75rem !important;
                margin-right: 0.75rem !important;
            }

            .card-body {
                padding: 1rem !important;
            }

            .form-label {
                font-size: 0.8rem;
                margin-bottom: 0.25rem;
            }

            .input-group .form-control,
            .input-group .input-group-text,
            .input-group .btn {
                padding: 0.5rem !important;
                font-size: 0.85rem;
            }

            .input-group .btn i {
                font-size: 0.9rem;
            }
        }

        /* Success Message Animation */
        .alert-success {
            border-left: 4px solid #198754;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Password Strength Progress Bar */
        .progress-bar {
            transition: width 0.3s ease;
        }

        /* Flex-fill buttons on mobile */
        @media (max-width: 576px) {
            .flex-fill {
                width: 100%;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function(){
            // Toggle password fields
            $('#changePasswordSwitch').on('change', function(){
                if($(this).is(':checked')){
                    $('#passwordFields').slideDown(300);
                } else {
                    $('#passwordFields').slideUp(300);
                    $('#passwordStrength').hide();
                    $('#current_password, #password, #password_confirmation').val('');
                    $('.invalid-feedback').text('');
                    $('.form-control').removeClass('is-invalid');
                }
            });

            // Password strength checker
            $('#password').on('input', function() {
                if ($('#changePasswordSwitch').is(':checked')) {
                    $('#passwordStrength').show();
                    const password = $(this).val();
                    let strength = 0;

                    if (password.length >= 8) strength += 25;
                    if (/[A-Z]/.test(password)) strength += 25;
                    if (/[0-9]/.test(password)) strength += 25;
                    if (/[!@#$%^&*]/.test(password)) strength += 25;

                    $('#passwordStrengthBar').css('width', strength + '%');

                    if (strength <= 25) {
                        $('#passwordStrengthBar').removeClass().addClass('progress-bar bg-danger');
                        $('#passwordStrengthText').text('Weak').removeClass().addClass('text-danger fw-medium');
                    } else if (strength <= 50) {
                        $('#passwordStrengthBar').removeClass().addClass('progress-bar bg-warning');
                        $('#passwordStrengthText').text('Fair').removeClass().addClass('text-warning fw-medium');
                    } else if (strength <= 75) {
                        $('#passwordStrengthBar').removeClass().addClass('progress-bar bg-info');
                        $('#passwordStrengthText').text('Good').removeClass().addClass('text-info fw-medium');
                    } else {
                        $('#passwordStrengthBar').removeClass().addClass('progress-bar bg-success');
                        $('#passwordStrengthText').text('Strong').removeClass().addClass('text-success fw-medium');
                    }
                }
            });

            // CNIC auto-formatting
            $('#cnic').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length > 5) value = value.slice(0,5) + '-' + value.slice(5);
                if (value.length > 13) value = value.slice(0,13) + '-' + value.slice(13,14);
                $(this).val(value);
            });

            // Form submission with SweetAlert2
            // Form submission with SweetAlert2 toast
            $('#profileForm').submit(function(e){
                e.preventDefault();

                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('user.profile.update') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res){
                        Swal.fire({
                            toast: true,
                            position: 'top-end', // top-right corner
                            icon: 'success',
                            title: res.message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            background: '#f0f9ff',
                            color: '#0f5132',
                            iconColor: '#198754'
                        });

                        // Clear password fields
                        $('#current_password, #password, #password_confirmation').val('');
                        $('#changePasswordSwitch').prop('checked', false);
                        $('#passwordFields').slideUp(300);
                        $('#passwordStrength').hide();
                    },
                    error: function(xhr){
                        if(xhr.status === 422){
                            let errors = xhr.responseJSON.errors;

                            if(errors.current_password || errors.password){
                                $('#changePasswordSwitch').prop('checked', true);
                                $('#passwordFields').slideDown(300);
                            }

                            $.each(errors, function(key, value){
                                $('#' + key).addClass('is-invalid');
                                $('#error_' + key).text(value[0]);
                            });
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Something went wrong. Please try again.',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                background: '#fff5f5',
                                color: '#842029',
                                iconColor: '#dc3545'
                            });
                        }
                    }
                });
            });

        });

        // Toggle password visibility
        function togglePassword(fieldId, btn){
            let field = document.getElementById(fieldId);
            let icon = btn.querySelector('i');

            if(field.type === 'password'){
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>

@endsection
