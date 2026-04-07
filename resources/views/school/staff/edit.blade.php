@extends('school.layouts.app')
@section('title', 'Edit Staff')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.staff.index', $school->slug) }}">Staff</a></li>
    <li class="breadcrumb-item active">Edit: {{ $staff->name }}</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil me-2 text-warning"></i>Edit Staff Member
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.staff.update', [$school->slug, $staff]) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $staff->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Designation *</label>
                    <input type="text" name="designation" class="form-control form-control-sm" value="{{ old('designation', $staff->designation) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $staff->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Monthly Salary (Rs.) *</label>
                    <input type="number" name="salary" class="form-control form-control-sm" value="{{ old('salary', $staff->salary) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="is_active" class="form-select form-select-sm">
                        <option value="1" {{ $staff->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$staff->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-check-lg me-1"></i>Update Staff</button>
                <a href="{{ route('school.staff.index', $school->slug) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
