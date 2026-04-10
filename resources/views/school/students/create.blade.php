@extends('school.layouts.app')
@section('title', 'Admit Student')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.students.index', $school->slug) }}">Students</a></li>
    <li class="breadcrumb-item active">Admit Student</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:750px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-person-plus me-2 text-primary"></i>Admit New Student
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.students.store', $school->slug) }}">
            @csrf

            {{-- Student Info --}}
            <p class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing:1px">Student Information</p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Roll Number</label>
                    <input type="text" name="roll_number" class="form-control form-control-sm" value="{{ old('roll_number') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">
                        Student Email
                        <span class="text-muted fw-normal">(for portal login)</span>
                    </label>
                    <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="student@email.com">
                    @error('email')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    <small class="text-muted">If provided, login credentials will be emailed to student</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Date of Birth</label>
                    <input type="date" name="dob" class="form-control form-control-sm" value="{{ old('dob') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Gender</label>
                    <select name="gender" class="form-select form-select-sm">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control form-control-sm" value="{{ old('address') }}">
                </div>
            </div>

            {{-- Parent/Guardian Info --}}
            <p class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing:1px">
                Parent / Guardian Information
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Guardian Name *</label>
                    <input type="text" name="guardian_name" class="form-control form-control-sm @error('guardian_name') is-invalid @enderror"
                           value="{{ old('guardian_name') }}" required>
                    @error('guardian_name')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Guardian Phone</label>
                    <input type="text" name="guardian_phone" class="form-control form-control-sm" value="{{ old('guardian_phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">
                        Guardian Email
                        <span class="text-muted fw-normal">(for parent portal)</span>
                    </label>
                    <input type="email" name="guardian_email" class="form-control form-control-sm @error('guardian_email') is-invalid @enderror"
                           value="{{ old('guardian_email') }}" placeholder="parent@email.com">
                    @error('guardian_email')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    <small class="text-muted">If provided, parent portal credentials will be emailed</small>
                </div>
            </div>

            {{-- Enrollment --}}
            <p class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing:1px">Enrollment Details</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Session *</label>
                    <select name="school_session_id" class="form-select form-select-sm" required>
                        <option value="">Select Session</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}" {{ old('school_session_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Class *</label>
                    <select name="school_class_id" id="classSelect" class="form-select form-select-sm" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" data-sections="{{ $c->sections->toJson() }}"
                                {{ old('school_class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Section *</label>
                    <select name="section_id" id="sectionSelect" class="form-select form-select-sm" required>
                        <option value="">Select Section</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Admit Student
                </button>
                <a href="{{ route('school.students.index', $school->slug) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('classSelect').addEventListener('change', function () {
    const sections = JSON.parse(this.options[this.selectedIndex].dataset.sections || '[]');
    const sel = document.getElementById('sectionSelect');
    sel.innerHTML = '<option value="">Select Section</option>';
    sections.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
});
</script>
@endpush
@endsection
