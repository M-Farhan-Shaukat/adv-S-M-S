@extends('school.layouts.app')
@section('title', 'Add Staff')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.staff.index', $school->slug) }}">Staff</a></li>
    <li class="breadcrumb-item active">Add Staff</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-person-plus me-2 text-primary"></i>Add New Staff Member
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.staff.store', $school->slug) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Designation *</label>
                    <input type="text" name="designation" class="form-control form-control-sm" placeholder="e.g. Accountant, Guard" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Monthly Salary (Rs.) *</label>
                    <input type="number" name="salary" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control form-control-sm">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Add Staff</button>
                <a href="{{ route('school.staff.index', $school->slug) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
