@extends('school.layouts.app')
@section('title', 'Add Teacher')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.teachers.index', app('school')->slug) }}">Teachers</a></li>
    <li class="breadcrumb-item active">Add Teacher</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-person-plus me-2 text-primary"></i>Add New Teacher
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.teachers.store', app('school')->slug) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email (for login)</label>
                    <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}">
                    <small class="text-muted">Leave blank if no portal access needed</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Qualification</label>
                    <input type="text" name="qualification" class="form-control form-control-sm" value="{{ old('qualification') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Monthly Salary (Rs.) *</label>
                    <input type="number" name="salary" class="form-control form-control-sm" value="{{ old('salary', 0) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Daily Required Minutes *</label>
                    <input type="number" name="daily_required_minutes" class="form-control form-control-sm" value="{{ old('daily_required_minutes', 480) }}" required>
                    <small class="text-muted">480 = 8 hours</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control form-control-sm" value="{{ old('joining_date') }}">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Add Teacher</button>
                <a href="{{ route('school.teachers.index', app('school')->slug) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
