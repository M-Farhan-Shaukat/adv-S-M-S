@extends('school.layouts.app')
@section('title', 'Add Student')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.students.index', $school->slug) }}">Students</a></li>
    <li class="breadcrumb-item active">Add Student</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-person-plus me-2 text-primary"></i>Admit New Student
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.students.store', $school->slug) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Roll Number</label>
                    <input type="text" name="roll_number" class="form-control form-control-sm" value="{{ old('roll_number') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Date of Birth</label>
                    <input type="date" name="dob" class="form-control form-control-sm" value="{{ old('dob') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Gender</label>
                    <select name="gender" class="form-select form-select-sm">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control form-control-sm" value="{{ old('address') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Guardian Name</label>
                    <input type="text" name="guardian_name" class="form-control form-control-sm" value="{{ old('guardian_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Guardian Phone</label>
                    <input type="text" name="guardian_phone" class="form-control form-control-sm" value="{{ old('guardian_phone') }}">
                </div>

                <div class="col-12"><hr class="my-1"><p class="small fw-bold text-muted mb-2">Enrollment Details</p></div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Session *</label>
                    <select name="school_session_id" class="form-select form-select-sm" required>
                        <option value="">Select Session</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Class *</label>
                    <select name="school_class_id" id="classSelect" class="form-select form-select-sm" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" data-sections="{{ $c->sections->toJson() }}">{{ $c->name }}</option>
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
document.getElementById('classSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const sections = JSON.parse(selected.dataset.sections || '[]');
    const sectionSelect = document.getElementById('sectionSelect');
    sectionSelect.innerHTML = '<option value="">Select Section</option>';
    sections.forEach(s => {
        sectionSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
    });
});
</script>
@endpush
@endsection
