@extends('school.layouts.app')
@section('title', 'Edit Student')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.students.index', $school->slug) }}">Students</a></li>
    <li class="breadcrumb-item active">Edit: {{ $student->name }}</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil me-2 text-warning"></i>Edit Student
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.students.update', [$school->slug, $student]) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $student->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $student->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Date of Birth</label>
                    <input type="date" name="dob" class="form-control form-control-sm" value="{{ old('dob', $student->dob) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Gender</label>
                    <select name="gender" class="form-select form-select-sm">
                        <option value="">Select</option>
                        <option value="male" {{ $student->gender === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $student->gender === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ $student->gender === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control form-control-sm" value="{{ old('address', $student->address) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Guardian Name</label>
                    <input type="text" name="guardian_name" class="form-control form-control-sm" value="{{ old('guardian_name', $student->guardian_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Guardian Phone</label>
                    <input type="text" name="guardian_phone" class="form-control form-control-sm" value="{{ old('guardian_phone', $student->guardian_phone) }}">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-check-lg me-1"></i>Update Student</button>
                <a href="{{ route('school.students.index', $school->slug) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
