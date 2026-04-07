@extends('school.layouts.app')
@section('title', 'Edit Teacher')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.teachers.index', app('school')->slug) }}">Teachers</a></li>
    <li class="breadcrumb-item active">Edit: {{ $teacher->name }}</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil me-2 text-warning"></i>Edit Teacher
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.teachers.update', [app('school')->slug, $teacher]) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $teacher->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $teacher->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Monthly Salary (Rs.) *</label>
                    <input type="number" name="salary" class="form-control form-control-sm" value="{{ old('salary', $teacher->salary) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Daily Required Minutes *</label>
                    <input type="number" name="daily_required_minutes" class="form-control form-control-sm" value="{{ old('daily_required_minutes', $teacher->daily_required_minutes) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Qualification</label>
                    <input type="text" name="qualification" class="form-control form-control-sm" value="{{ old('qualification', $teacher->qualification) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control form-control-sm" value="{{ old('joining_date', $teacher->joining_date) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="is_active" class="form-select form-select-sm">
                        <option value="1" {{ $teacher->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$teacher->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-check-lg me-1"></i>Update Teacher</button>
                <a href="{{ route('school.teachers.index', app('school')->slug) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
