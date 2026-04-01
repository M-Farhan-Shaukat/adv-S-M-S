@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-2 px-md-3">
        <!-- Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
            <div>
                <h4 class="fw-bold mb-0 fs-6 fs-md-5">User Profile</h4>
                <p class="text-muted mb-0 small d-none d-sm-block">Detailed information about the user</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill small">
                    <i class="bi bi-person-badge me-1"></i> Profile
                </span>
                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-10 px-2 py-1 rounded-pill small"
                      style="color: {{ $user->is_active ? '#198754' : '#dc3545' }} !important;">
                    <i class="bi bi-circle-fill me-1"></i>
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <div class="col-12">
                <!-- Profile Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body p-3 p-md-4">
                        <!-- Profile Header -->
                        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3 mb-4">
                            <!-- User Avatar -->
                            <div class="position-relative">
                                <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center"
                                     style="width: 70px; height: 70px;">
                                    <span class="fw-bold text-white fs-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="position-absolute bottom-0 end-0">
                                    <span class="badge rounded-pill bg-white border p-1 shadow-sm">
                                        <i class="bi bi-check-circle-fill {{ $user->is_active ? 'text-success' : 'text-secondary' }}"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="flex-grow-1 text-center text-sm-start">
                                <h4 class="fw-bold mb-1 fs-5">{{ $user->name }}</h4>
                                <div class="d-flex flex-wrap gap-2 mb-2 justify-content-center justify-content-sm-start">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                        <i class="bi bi-shield me-1"></i> {{ $user->role->name ?? 'No Role' }}
                                    </span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                        <i class="bi bi-fingerprint me-1"></i> #{{ $user->id }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-calendar-plus me-1"></i> Member since {{ $user->created_at->format('M d, Y') }}
                                </p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mt-2 mt-sm-0 justify-content-center justify-content-sm-end w-100 w-sm-auto">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <a href="{{ route('admin.users') }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>

                        <!-- User Information Tabs - Scrollable on mobile -->
                        <div class="overflow-auto pb-2">
                            <ul class="nav nav-tabs nav-tabs-light flex-nowrap" id="profileTabs" role="tablist" style="min-width: max-content;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button">
                                        <i class="bi bi-person me-1"></i> Personal
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button">
                                        <i class="bi bi-shield-lock me-1"></i> Account
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button">
                                        <i class="bi bi-clock-history me-1"></i> Activity
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3">
                            <!-- Personal Information Tab -->
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-envelope text-primary"></i>
                                                </div>
                                                <div class="min-width-0">
                                                    <small class="text-muted text-uppercase">Email</small>
                                                    <p class="fw-semibold mb-0 small text-break">{{ $user->email }}</p>
                                                    @if($user->email_verified_at)
                                                        <span class="badge bg-success bg-opacity-10 text-success mt-1 small">
                                                            Verified
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-telephone text-primary"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted text-uppercase">Phone</small>
                                                    <p class="fw-semibold mb-0 small">{{ $user->phone ?? 'Not provided' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-calendar text-primary"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted text-uppercase">Age</small>
                                                    <p class="fw-semibold mb-0 small">{{ $user->age ?? '—' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-building text-primary"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted text-uppercase">City</small>
                                                    <p class="fw-semibold mb-0 small">{{ $user->city ?? '—' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-postcard text-primary"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted text-uppercase">Postal</small>
                                                    <p class="fw-semibold mb-0 small">{{ $user->postal_code ?? '—' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-card-text text-primary"></i>
                                                </div>
                                                <div class="min-width-0">
                                                    <small class="text-muted text-uppercase">CNIC / ID</small>
                                                    <p class="fw-semibold mb-0 small text-break">{{ $user->cnic ?? 'Not provided' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Details Tab -->
                            <div class="tab-pane fade" id="account" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-shield text-primary"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted text-uppercase">Role</small>
                                                    <p class="fw-semibold mb-0 small">{{ $user->role->name ?? 'No Role' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-circle-fill {{ $user->is_active ? 'text-success' : 'text-danger' }}"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted text-uppercase">Status</small>
                                                    <div>
                                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }} small">
                                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-calendar-plus text-primary"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted text-uppercase">Created</small>
                                                    <p class="fw-semibold mb-0 small">{{ $user->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card p-2 bg-light rounded-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="info-icon">
                                                    <i class="bi bi-calendar-check text-primary"></i>
                                                </div>
                                                <div>
                                                    <small class="text-muted text-uppercase">Updated</small>
                                                    <p class="fw-semibold mb-0 small">{{ $user->updated_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Tab -->
                            <div class="tab-pane fade" id="activity" role="tabpanel">
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <div class="rounded-circle bg-light d-inline-flex p-3">
                                            <i class="bi bi-clock-history fs-3 text-muted"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-semibold mb-2 small">No Activity Logs</h6>
                                    <p class="small text-muted mb-0">Activity tracking will be available soon.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Cards -->
                <div class="row g-2 g-md-3">
                    <!-- Quick Actions -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body p-3">
                                <h6 class="fw-semibold mb-3 small">
                                    <i class="bi bi-lightning-charge text-primary me-2"></i>
                                    Quick Actions
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#statusModal{{ $user->id }}">
                                        <i class="bi bi-{{ $user->is_active ? 'slash-circle' : 'check-circle' }}"></i>
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    @if(auth()->user()->hasPermission('delete_users') && auth()->id() !== $user->id)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $user->id }}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Meta -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body p-3">
                                <h6 class="fw-semibold mb-3 small">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    User Meta
                                </h6>
                                <div class="d-flex flex-column gap-1 small">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">User ID:</span>
                                        <span class="fw-medium">#{{ $user->id }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Email Verified:</span>
                                        @if($user->email_verified_at)
                                            <span class="text-success">{{ $user->email_verified_at->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-warning">Not verified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Modal - Responsive -->
    <div class="modal fade" id="statusModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm mx-2 mx-md-auto">
            <div class="modal-content border-0 shadow">
                <div class="modal-body p-3 p-md-4 text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>

                    <div class="d-flex justify-content-center mb-2">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3
                            {{ $user->is_active ? 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10' }}"
                             style="width: 60px; height: 60px;">
                            <i class="bi bi-{{ $user->is_active ? 'exclamation-triangle' : 'check-circle' }}
                                {{ $user->is_active ? 'text-warning' : 'text-success' }} fs-3"></i>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-1 fs-6 {{ $user->is_active ? 'text-warning' : 'text-success' }}">
                        {{ $user->is_active ? 'Deactivate' : 'Activate' }} User
                    </h5>
                    <p class="small text-muted mb-3">{{ $user->is_active ? 'Revoke access' : 'Grant access' }}</p>

                    <div class="bg-light rounded-3 p-2 mb-3 text-start">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center shadow-sm"
                                 style="width: 35px; height: 35px;">
                                <span class="fw-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $user->name }}</div>
                                <small class="text-muted">ID: #{{ $user->id }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="small fw-medium mb-1">
                            Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?
                        </p>
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <form action="{{ route('admin.users.status', $user) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm px-3 {{ $user->is_active ? 'btn-warning' : 'btn-success' }} text-white">
                                Yes, {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal - Responsive -->
    @if(auth()->user()->hasPermission('delete_users') && auth()->id() !== $user->id)
        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm mx-2 mx-md-auto">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body p-3 p-md-4 text-center">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>

                        <div class="d-flex justify-content-center mb-2">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3 bg-danger bg-opacity-10"
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-trash3 text-danger fs-3"></i>
                            </div>
                        </div>

                        <h5 class="fw-bold text-danger mb-1 fs-6">Delete User</h5>
                        <p class="small text-muted mb-3">This action cannot be undone</p>

                        <div class="bg-light rounded-3 p-2 mb-3 text-start">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-white d-flex align-items-center justify-content-center shadow-sm"
                                     style="width: 35px; height: 35px;">
                                    <span class="fw-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="fw-semibold small">{{ $user->name }}</div>
                                    <small class="text-muted">ID: #{{ $user->id }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="small fw-medium text-danger mb-1">
                                Are you sure you want to delete this user?
                            </p>
                        </div>

                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger px-3">
                                    Yes, Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(145deg, #0d6efd, #0b5ed7);
        }
        .info-icon {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .nav-tabs-light .nav-link {
            border: none;
            color: #6c757d;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            white-space: nowrap;
        }
        .nav-tabs-light .nav-link.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .min-width-0 {
            min-width: 0;
            word-break: break-word;
        }
        @media (max-width: 575.98px) {
            .rounded-circle[style*="width: 70px"] {
                width: 60px !important;
                height: 60px !important;
            }
            .fs-3 {
                font-size: 1.5rem !important;
            }
        }
    </style>
@endpush
