@php
    $role = strtolower(auth()->user()->getRoleNames()->first() ?? 'student');
    $layout = match($role) {
        'teacher' => 'portal.teacher.layouts.app',
        'parent'  => 'portal.layouts.app',
        default   => 'portal.layouts.app',
    };
@endphp

@extends($layout)
@section('title', 'Change Password')
@section('portal-name', ucfirst($role) . ' Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
@if($role === 'teacher')
<a href="{{ route('teacher.dashboard', $slug) }}" class="t-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
@elseif($role === 'parent')
<a href="{{ route('parent.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
@else
<a href="{{ route('student.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
@endif
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:450px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-key me-2 text-primary"></i>Change Password
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('portal.change-password.update', app('school')->slug) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label small fw-semibold">Current Password *</label>
                <input type="password" name="current_password"
                       class="form-control form-control-sm @error('current_password') is-invalid @enderror" required>
                @error('current_password')<div class="invalid-feedback small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">New Password *</label>
                <input type="password" name="password"
                       class="form-control form-control-sm @error('password') is-invalid @enderror"
                       placeholder="Min 8 characters" required>
                @error('password')<div class="invalid-feedback small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Confirm New Password *</label>
                <input type="password" name="password_confirmation"
                       class="form-control form-control-sm" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">
                <i class="bi bi-check-lg me-1"></i>Update Password
            </button>
        </form>
    </div>
</div>
@endsection
