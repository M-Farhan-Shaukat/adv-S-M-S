@extends($layout ?? 'admin.layouts.app')
@section('title', 'Create User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Create New User</h5>
    <a href="{{ route($routePrefix . 'index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:650px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route($routePrefix . 'store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name"
                           class="form-control form-control-sm @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email *</label>
                    <input type="email" name="email"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm"
                           value="{{ old('phone') }}" placeholder="03xx-xxxxxxx">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Role *</label>
                    <select name="role" class="form-select form-select-sm @error('role') is-invalid @enderror" required>
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Password *</label>
                    <div class="input-group input-group-sm">
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min 6 characters" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- School dropdown: only super admin can pick a school --}}
                @if(!($isPrincipal ?? false))
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">School (optional)</label>
                    <select name="school_id" class="form-select form-select-sm">
                        <option value="">-- No School --</option>
                        @foreach(\App\Models\School::where('is_active', true)->get() as $sch)
                            <option value="{{ $sch->id }}">{{ $sch->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Assign to a school</small>
                </div>
                @else
                {{-- Principal: school is auto-assigned, just show it --}}
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">School</label>
                    <input type="text" class="form-control form-control-sm bg-light"
                           value="{{ auth()->user()->school?->name }}" disabled>
                    <small class="text-muted">User will be assigned to your school</small>
                </div>
                @endif

                <div class="col-12">
                    <div class="d-flex gap-4 bg-light p-2 rounded-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="email_verified" id="emailVerified">
                            <label class="form-check-label small fw-semibold" for="emailVerified">
                                Mark Email Verified
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Create User
                </button>
                <a href="{{ route($routePrefix . 'index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
