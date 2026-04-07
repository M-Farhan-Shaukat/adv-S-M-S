@extends('admin.layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil me-2"></i>Edit User: {{ $user->name }}</h5>
    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:650px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email *</label>
                    <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm"
                           value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Role *</label>
                    <select name="role" class="form-select form-select-sm @error('role') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ (old('role', $userRole) === $role->name) ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">New Password</label>
                    <div class="input-group input-group-sm">
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Leave empty to keep current">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">School</label>
                    <select name="school_id" class="form-select form-select-sm">
                        <option value="">-- No School --</option>
                        @foreach(\App\Models\School::where('is_active', true)->get() as $sch)
                            <option value="{{ $sch->id }}" {{ $user->school_id == $sch->id ? 'selected' : '' }}>
                                {{ $sch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-4 bg-light p-2 rounded-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                   {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="isActive">Active</label>
                        </div>
                        @if(!$user->email_verified_at)
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="email_verified" id="emailVerified">
                            <label class="form-check-label small fw-semibold" for="emailVerified">Mark Email Verified</label>
                        </div>
                        @else
                        <span class="small text-success align-self-center">
                            <i class="bi bi-check-circle-fill me-1"></i>Email Verified
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Update User
                </button>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Danger Zone --}}
@if(auth()->id() !== $user->id)
<div class="card border-0 mt-3" style="max-width:650px;background:#fff5f5;border-left:3px solid #dc3545 !important">
    <div class="card-body p-3 d-flex justify-content-between align-items-center">
        <div>
            <div class="fw-semibold text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Danger Zone</div>
            <div class="text-muted small">Permanently delete this user</div>
        </div>
        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="bi bi-trash me-1"></i>Delete
        </button>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <i class="bi bi-trash3 text-danger fs-2 mb-2 d-block"></i>
                <h6 class="fw-bold text-danger">Delete {{ $user->name }}?</h6>
                <p class="small text-muted mb-3">This cannot be undone.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
