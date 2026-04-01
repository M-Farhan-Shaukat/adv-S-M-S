@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid px-2 px-md-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
            <div>
                <h3 class="fw-bold mb-0 fs-5 fs-md-4">Users</h3>
                <p class="text-muted mb-0 small d-none d-sm-block">Manage system users and their permissions</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Create User
            </a>
        </div>

        <!-- Filter Card - Responsive -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-2 p-md-3">
                <div class="d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-center">
                    <div class="col-auto">
                        <form method="GET" action="{{ route('admin.users') }}" id="perPageForm" class="d-flex align-items-center">
                            <label class="text-muted me-2 small">Show:</label>
                            <select name="per_page" onchange="this.form.submit()" class="form-select form-select-sm" style="width: auto; min-width: 70px;">
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                    </div>
                    <div class="ms-md-auto w-100 w-md-auto">
                        <form method="GET" action="{{ route('admin.users') }}" class="d-flex">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       class="form-control border-start-0"
                                       placeholder="Search users..."
                                       style="min-width: 200px;">
                                @if(request('search'))
                                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table - Card layout on mobile, table on desktop -->
        <div class="card shadow-sm border-0">
            <!-- Desktop Table View (hidden on mobile) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="px-4">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                         style="width: 32px; height: 32px; background-color: #{{ substr(md5($user->name), 0, 6) }} !important;">
                                        <span class="fw-bold small">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $user->name }}</div>
                                        @if($user->email_verified_at)
                                            <small class="text-success small">
                                                <i class="bi bi-check-circle-fill me-1" style="font-size: 0.75rem;"></i>Verified
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="small">{{ $user->email }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ $user->role->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-end px-4">
                                <!-- View Button -->
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-sm btn-outline-info border-0"
                                   title="View Details"
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary border-0"
                                   title="Edit User"
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <!-- Delete Button -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger border-0"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $user->id }}"
                                        title="Delete User">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <!-- Toggle Status Button -->
                                <button type="button"
                                        class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} border-0"
                                        data-bs-toggle="modal"
                                        data-bs-target="#statusModal{{ $user->id }}"
                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }} User">
                                    <i class="bi bi-{{ $user->is_active ? 'slash-circle' : 'check-circle' }}"></i>
                                </button>

                                <!-- Delete Modal for Desktop -->
                                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-body p-4 text-center position-relative">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>

                                                <div class="d-flex justify-content-center mb-3">
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3 bg-danger bg-opacity-10"
                                                         style="width: 64px; height: 64px;">
                                                        <i class="bi bi-trash3 text-danger fs-3"></i>
                                                    </div>
                                                </div>

                                                <h5 class="fw-bold text-danger mb-1">Delete User</h5>
                                                <p class="small text-muted mb-3">This action cannot be undone</p>

                                                <div class="bg-light rounded-3 p-3 mb-3 text-start">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center shadow-sm"
                                                             style="width: 40px; height: 40px;">
                                                            <span class="fw-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">{{ $user->name }}</div>
                                                            <small class="text-muted">ID: #{{ $user->id }}</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <p class="small fw-medium text-danger mb-3">
                                                    Are you sure you want to delete this user?
                                                </p>

                                                <div class="d-flex gap-2 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger px-4">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Modal for Desktop -->
                                <div class="modal fade" id="statusModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-body p-4 text-center">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>

                                                <div class="d-flex justify-content-center mb-3">
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3
                                                        {{ $user->is_active ? 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10' }}"
                                                         style="width: 64px; height: 64px;">
                                                        <i class="bi bi-{{ $user->is_active ? 'exclamation-triangle' : 'check-circle' }}
                                                            {{ $user->is_active ? 'text-warning' : 'text-success' }} fs-3"></i>
                                                    </div>
                                                </div>

                                                <h5 class="fw-bold mb-1 {{ $user->is_active ? 'text-warning' : 'text-success' }}">
                                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }} User
                                                </h5>
                                                <p class="small text-muted mb-3">
                                                    {{ $user->is_active ? 'This will revoke system access' : 'This will grant system access' }}
                                                </p>

                                                <div class="bg-light rounded-3 p-3 mb-3 text-start">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center shadow-sm"
                                                             style="width: 40px; height: 40px;">
                                                            <span class="fw-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold">{{ $user->name }}</div>
                                                            <small class="text-muted">ID: #{{ $user->id }}</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <p class="small fw-medium mb-3">
                                                    Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?
                                                </p>

                                                <div class="d-flex gap-2 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.users.status', $user) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-sm px-4 {{ $user->is_active ? 'btn-warning' : 'btn-success' }} text-white">
                                                            Yes, {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-people fs-1 text-muted mb-3"></i>
                                    <h5 class="fw-normal text-muted mb-3">No users found</h5>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg"></i> Create User
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (visible on mobile) -->
            <div class="d-block d-md-none p-2">
                @forelse($users as $user)
                    <div class="card border-0 bg-light mb-2 p-2">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                     style="width: 40px; height: 40px; background-color: #{{ substr(md5($user->name), 0, 6) }} !important;">
                                    <span class="fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                            <div>
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ $user->role->name ?? 'No Role' }}
                                </span>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill ms-1">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-sm btn-outline-info border-0"
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary border-0"
                                   title="Edit User">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger border-0"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModalMobile{{ $user->id }}"
                                        title="Delete User">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} border-0"
                                        data-bs-toggle="modal"
                                        data-bs-target="#statusModalMobile{{ $user->id }}"
                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }} User">
                                    <i class="bi bi-{{ $user->is_active ? 'slash-circle' : 'check-circle' }}"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal - Mobile -->
                    <div class="modal fade" id="deleteModalMobile{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-sm mx-2">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-body p-3 text-center">
                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>

                                    <div class="d-flex justify-content-center mb-2">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-2 bg-danger bg-opacity-10"
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-trash3 text-danger fs-4"></i>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-danger mb-1">Delete User</h6>
                                    <p class="small text-muted mb-2">This action cannot be undone</p>

                                    <div class="bg-light rounded-3 p-2 mb-2 text-start">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center shadow-sm"
                                                 style="width: 30px; height: 30px;">
                                                <span class="fw-bold text-primary small">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold small">{{ $user->name }}</div>
                                                <small class="text-muted">#{{ $user->id }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="small fw-medium text-danger mb-2">Are you sure you want to delete this user?</p>

                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger px-3">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Modal - Mobile -->
                    <div class="modal fade" id="statusModalMobile{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-sm mx-2">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-body p-3 text-center">
                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>

                                    <div class="d-flex justify-content-center mb-2">
                                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-2
                                            {{ $user->is_active ? 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10' }}"
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-{{ $user->is_active ? 'exclamation-triangle' : 'check-circle' }}
                                                {{ $user->is_active ? 'text-warning' : 'text-success' }} fs-4"></i>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-1 {{ $user->is_active ? 'text-warning' : 'text-success' }}">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </h6>
                                    <p class="small text-muted mb-2">
                                        {{ $user->is_active ? 'Revoke system access' : 'Grant system access' }}
                                    </p>

                                    <div class="bg-light rounded-3 p-2 mb-2 text-start">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center shadow-sm"
                                                 style="width: 30px; height: 30px;">
                                                <span class="fw-bold text-primary small">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold small">{{ $user->name }}</div>
                                                <small class="text-muted">#{{ $user->id }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="small fw-medium mb-2">
                                        Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this user?
                                    </p>

                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">Cancel</button>
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
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-people fs-1 text-muted mb-2"></i>
                        <h5 class="fw-normal text-muted mb-2 small">No users found</h5>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg"></i> Create User
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="card-footer bg-white border-0 py-2 py-md-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                        <div class="text-muted small order-2 order-md-1">
                            <i class="bi bi-list-ul me-1"></i>
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
                        </div>
                        <div class="order-1 order-md-2">
                            {{ $users->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media (max-width: 767.98px) {
            .container-fluid {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            .card-body {
                padding: 0.75rem !important;
            }
            .badge {
                font-size: 0.7rem;
            }
        }
        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }
        .modal-dialog {
            margin: 0.5rem auto;
        }
        .btn-outline-info.border-0:hover,
        .btn-outline-primary.border-0:hover,
        .btn-outline-danger.border-0:hover,
        .btn-outline-warning.border-0:hover,
        .btn-outline-success.border-0:hover {
            background-color: rgba(0,0,0,0.05);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Enable Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        })
    </script>
@endpush
