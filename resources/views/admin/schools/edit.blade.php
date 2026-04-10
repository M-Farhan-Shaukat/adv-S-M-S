@extends('admin.layouts.app')
@section('title', 'Edit School')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil me-2 text-warning"></i>Edit School: {{ $school->name }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.schools.update', $school) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">School Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $school->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">URL Slug</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="{{ $school->slug }}" disabled>
                    <small class="text-muted">Slug cannot be changed</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email', $school->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $school->phone) }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control form-control-sm" value="{{ old('address', $school->address) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Fee Voucher Day *</label>
                    <input type="number" name="fee_voucher_day" class="form-control form-control-sm"
                           value="{{ old('fee_voucher_day', $school->fee_voucher_day) }}" min="1" max="28" required>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-check-lg me-1"></i>Update</button>
                <a href="{{ route('admin.schools.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
