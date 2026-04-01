@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-2 px-md-3">
        <!-- Header -->
        <div class="d-flex flex-wrap flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
            <div>
                <h4 class="fw-bold mb-0 fs-6 fs-md-5">Edit User</h4>
                <p class="text-muted mb-0 small d-none d-sm-block">Update user information and settings</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill small">
                    <i class="bi bi-pencil-square me-1"></i> Editing
                </span>
                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-10 px-2 py-1 rounded-pill small"
                      style="color: {{ $user->is_active ? '#198754' : '#dc3545' }} !important;">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <!-- Form Card - Compact -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- Single column on mobile, two on desktop -->
                            <div class="row g-3">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-person"></i> Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-sm @error('name') is-invalid @enderror"
                                               name="name"
                                               placeholder="Full name"
                                               value="{{ old('name', $user->name) }}"
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
                                               value="{{ old('email', $user->email) }}"
                                               required>
                                        @error('email')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                        @if($user->email_verified_at)
                                            <small class="text-success d-block mt-1 small">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                Verified: {{ $user->email_verified_at->format('M d, Y') }}
                                            </small>
                                        @endif
                                    </div>



                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-building"></i> City
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-sm @error('city') is-invalid @enderror"
                                               name="city"
                                               placeholder="New York"
                                               value="{{ old('city', $user->city) }}">
                                        @error('city')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">


                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-card-text"></i> CNIC / ID
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-sm @error('cnic') is-invalid @enderror"
                                               name="cnic"
                                               placeholder="12345-6789012-3"
                                               id="cnic"
                                               value="{{ old('cnic', $user->cnic) }}">
                                        @error('cnic')
                                        <div class="invalid-feedback small">{{ $message }}</div>
                                        @enderror
                                    </div>



                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-key"></i> New Password
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <input type="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   name="password"
                                                   placeholder="Leave empty"
                                                   id="password">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                                <i class="bi bi-eye" id="passwordIcon"></i>
                                            </button>
                                            @error('password')
                                            <div class="invalid-feedback small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted small">Min 8 chars. Leave empty to keep current.</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-check-circle"></i> Confirm Password
                                        </label>
                                        <input type="password"
                                               class="form-control form-control-sm"
                                               name="password_confirmation"
                                               placeholder="Confirm new password"
                                               id="password_confirmation">
                                    </div>
                                </div>

                                <!-- Account Status Controls -->
                                <div class="col-12">
                                    <div class="d-flex flex-wrap align-items-center bg-light p-2 rounded-3 mt-2">
                                        <div class="form-check form-switch me-3">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="isActive">
                                                Active Account
                                            </label>
                                        </div>

                                        @if(!$user->email_verified_at)
                                            <div class="form-check form-switch me-3">
                                                <input class="form-check-input" type="checkbox" name="email_verified" id="emailVerified">
                                                <label class="form-check-label small fw-semibold" for="emailVerified">
                                                    Mark Email Verified
                                                </label>
                                            </div>
                                        @endif

                                        <div class="ms-auto mt-2 mt-sm-0">
                                            <small class="text-muted small">
                                                <i class="bi bi-clock-history me-1"></i>
                                                Updated: {{ $user->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
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
                                    <i class="bi bi-arrow-left"></i> Back to Users
                                </a>
                                <div class="d-flex gap-2 order-1 order-sm-2">
                                    <button type="reset" class="btn btn-sm btn-outline-secondary flex-fill flex-sm-grow-0">
                                        <i class="bi bi-eraser"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary flex-fill flex-sm-grow-0">
                                        <i class="bi bi-check-circle"></i> Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Danger Zone - For Admins Only -->
                @if(auth()->user()->hasPermission('delete_users') && auth()->user()->id !== $user->id)
                    <div class="card border-0 bg-danger bg-opacity-10 mt-3">
                        <div class="card-body p-3">
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2">
                                <div>
                                    <h6 class="fw-semibold text-danger mb-1 small">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Danger Zone
                                    </h6>
                                    <p class="small text-muted mb-0">Once you delete a user, there is no going back.</p>
                                </div>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger w-100 w-sm-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal">
                                    <i class="bi bi-trash"></i> Delete User
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal - Responsive -->
                    <div class="modal fade" id="deleteUserModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-sm mx-2 mx-md-auto">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h6 class="modal-title text-danger fw-semibold small">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Delete User
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="small mb-0">Are you sure you want to delete <strong>{{ $user->name }}</strong>?</p>
                                    <p class="small text-muted mt-2 mb-0">This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Mobile optimizations */
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
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .w-sm-auto {
            width: auto !important;
        }
        @media (min-width: 576px) {
            .w-sm-auto {
                width: auto !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Password match checker
        document.addEventListener('DOMContentLoaded', function() {
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
