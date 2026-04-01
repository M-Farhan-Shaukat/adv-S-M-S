@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-2 px-md-3">
        <!-- Header -->
        <div class="d-flex flex-wrap flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
            <div>
                <h4 class="fw-bold mb-0 fs-6 fs-md-5">Create New User</h4>
                <p class="text-muted mb-0 small d-none d-sm-block">Add a new user to the system</p>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill small">
                <i class="bi bi-person-plus-fill me-1"></i> New
            </span>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-md-4">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            <!-- Single column on mobile, two on desktop -->
                            <div class="row g-3">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-person"></i> Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-sm @error('name') is-invalid @enderror"
                                               name="name"
                                               placeholder="Enter full name"
                                               value="{{ old('name') }}"
                                               required>
                                        @error('name')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-envelope"></i> Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email"
                                               class="form-control form-control-sm @error('email') is-invalid @enderror"
                                               name="email"
                                               placeholder="user@example.com"
                                               value="{{ old('email') }}"
                                               required>
                                        @error('email')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>



                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-building"></i> City
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-sm @error('city') is-invalid @enderror"
                                               name="city"
                                               placeholder="New York"
                                               value="{{ old('city') }}">
                                        @error('city')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">


                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-card-text"></i> CNIC
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-sm @error('cnic') is-invalid @enderror"
                                               name="cnic"
                                               placeholder="12345-6789012-3"
                                               id="cnic"
                                               value="{{ old('cnic') }}">
                                        @error('cnic')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                   +

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-key"></i> Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <input type="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   name="password"
                                                   placeholder="Password"
                                                   id="password"
                                                   required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                                <i class="bi bi-eye" id="passwordIcon"></i>
                                            </button>
                                            @error('password')
                                            <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted small">Min 8 characters</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-check-circle"></i> Confirm Password <span class="text-danger">*</span>
                                        </label>
                                        <input type="password"
                                               class="form-control form-control-sm"
                                               name="password_confirmation"
                                               placeholder="Confirm password"
                                               id="password_confirmation">
                                    </div>
                                </div>

                                <!-- Account Status -->
                                <div class="col-12">
                                    <div class="d-flex flex-wrap align-items-center bg-light p-2 rounded-3 mt-2 gap-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                                            <label class="form-check-label small fw-semibold" for="isActive">Active</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="email_verified" id="emailVerified">
                                            <label class="form-check-label small fw-semibold" for="emailVerified">Verify Email</label>
                                        </div>
                                        <small class="text-muted ms-auto small">
                                            <i class="bi bi-info-circle"></i> Welcome email will be sent
                                        </small>
                                    </div>
                                </div>

                                <!-- Password Match Indicator -->
                                <div class="col-12">
                                    <div id="passwordMatch" class="small"></div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center mt-4 pt-3 border-top gap-2">
                                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary order-2 order-sm-1">
                                    <i class="bi bi-arrow-left"></i> Back
                                </a>
                                <div class="d-flex gap-2 order-1 order-sm-2">
                                    <button type="reset" class="btn btn-sm btn-outline-secondary flex-fill flex-sm-grow-0">
                                        <i class="bi bi-eraser"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-success flex-fill flex-sm-grow-0">
                                        <i class="bi bi-check-circle"></i> Save User
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="alert alert-info bg-opacity-10 border-0 mt-3 p-2 small">
                    <i class="bi bi-info-circle me-1"></i>
                    <span class="fw-semibold">Tips:</span>
                    <span class="text-muted">Required fields marked * | Password: 8+ chars | CNIC auto-formats</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media (max-width: 575.98px) {
            .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
            .form-label {
                font-size: 0.7rem;
            }
            .btn-sm {
                padding: 0.3rem 0.7rem;
            }
        }
        .form-control-sm, .form-select-sm {
            font-size: 0.875rem;
            border-radius: 0.5rem;
        }
        .input-group-sm .form-control {
            border-radius: 0.5rem 0 0 0.5rem;
        }
        .input-group-sm .btn {
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password match checker
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            const matchDiv = document.getElementById('passwordMatch');

            if (confirm) {
                confirm.addEventListener('input', function() {
                    if (this.value.length > 0) {
                        if (password.value === this.value) {
                            matchDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Passwords match</span>';
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                        } else {
                            matchDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle-fill"></i> Passwords do not match</span>';
                            this.classList.remove('is-valid');
                            this.classList.add('is-invalid');
                        }
                    } else {
                        matchDiv.innerHTML = '';
                        this.classList.remove('is-valid', 'is-invalid');
                    }
                });
            }

            // Auto-format CNIC
            document.getElementById('cnic')?.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 5) {
                    value = value.slice(0, 5) + '-' + value.slice(5);
                }
                if (value.length > 13) {
                    value = value.slice(0, 13) + '-' + value.slice(13, 14);
                }
                e.target.value = value;
            });
        });
    </script>
@endpush
