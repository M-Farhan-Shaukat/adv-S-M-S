@extends('admin.layouts.app')
@section('title', 'Add School')

@section('content')
<div class="card border-0 shadow-sm" style="max-width:650px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-building-add me-2 text-primary"></i>Add New School
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.schools.store') }}">
            @csrf
            <p class="small fw-bold text-muted text-uppercase mb-2" style="letter-spacing:1px">School Details</p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">School Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">URL Slug *</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text text-muted">yoursite.com/</span>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug') }}" placeholder="abc-school" required>
                    </div>
                    <small class="text-muted">Lowercase letters, numbers, hyphens only</small>
                    @error('slug')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone') }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control form-control-sm" value="{{ old('address') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Fee Voucher Day *</label>
                    <input type="number" name="fee_voucher_day" class="form-control form-control-sm"
                           value="{{ old('fee_voucher_day', 1) }}" min="1" max="28" required>
                    <small class="text-muted">Day of month to send fee vouchers</small>
                </div>
            </div>

            <p class="small fw-bold text-muted text-uppercase mb-2" style="letter-spacing:1px">Principal Account</p>
            <div class="alert alert-info border-0 py-2 small">
                <i class="bi bi-info-circle me-1"></i>
                A principal account will be created automatically. Login credentials will be emailed.
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Principal Name *</label>
                    <input type="text" name="principal_name" class="form-control form-control-sm @error('principal_name') is-invalid @enderror"
                           value="{{ old('principal_name') }}" required>
                    @error('principal_name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Principal Email *</label>
                    <input type="email" name="principal_email" class="form-control form-control-sm @error('principal_email') is-invalid @enderror"
                           value="{{ old('principal_email') }}" required>
                    @error('principal_email')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Create School
                </button>
                <a href="{{ route('admin.schools.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate slug from name
document.querySelector('input[name="name"]').addEventListener('input', function() {
    const slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    document.querySelector('input[name="slug"]').value = slug;
});
</script>
@endpush
@endsection
