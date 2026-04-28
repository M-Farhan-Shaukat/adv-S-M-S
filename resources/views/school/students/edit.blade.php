@extends('school.layouts.app')
@section('title', 'Edit Student')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.students.index', $school->slug) }}">Students</a></li>
    <li class="breadcrumb-item active">Edit: {{ $student->name }}</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:750px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil me-2 text-warning"></i>Edit Student: {{ $student->name }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.students.update', [$school->slug, $student]) }}">
            @csrf @method('PUT')

            {{-- Student Info --}}
            <p class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing:1px">Student Information</p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Name *</label>
                    <input type="text" name="name" class="form-control form-control-sm"
                           value="{{ old('name', $student->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control form-control-sm"
                           value="{{ old('phone', $student->phone) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Date of Birth</label>
                    <input type="date" name="dob" class="form-control form-control-sm"
                           value="{{ old('dob', $student->dob) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Gender</label>
                    <select name="gender" class="form-select form-select-sm">
                        <option value="">Select</option>
                        <option value="male"   {{ old('gender', $student->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender', $student->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="is_active" class="form-select form-select-sm">
                        <option value="1" {{ $student->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$student->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Address</label>
                    <input type="text" name="address" class="form-control form-control-sm"
                           value="{{ old('address', $student->address) }}">
                </div>
            </div>

            {{-- Guardian --}}
            <p class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing:1px">Guardian Information</p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Guardian Name</label>
                    <input type="text" name="guardian_name" class="form-control form-control-sm"
                           value="{{ old('guardian_name', $student->guardian_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Guardian Phone</label>
                    <input type="text" name="guardian_phone" class="form-control form-control-sm"
                           value="{{ old('guardian_phone', $student->guardian_phone) }}">
                </div>
            </div>

            {{-- Enrollment --}}
            <p class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing:1px">Class & Section</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Session</label>
                    <select name="school_session_id" class="form-select form-select-sm">
                        <option value="">Keep current</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}"
                                {{ old('school_session_id', $enrollment?->school_session_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Class</label>
                    <select name="school_class_id" id="classSelect" class="form-select form-select-sm">
                        <option value="">Keep current</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}"
                                    data-sections="{{ $c->sections->toJson() }}"
                                {{ old('school_class_id', $enrollment?->school_class_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Section</label>
                    <select name="section_id" id="sectionSelect" class="form-select form-select-sm">
                        <option value="">Keep current</option>
                        @if($enrollment?->class)
                            @foreach($enrollment->class->sections as $sec)
                                <option value="{{ $sec->id }}"
                                    {{ old('section_id', $enrollment->section_id) == $sec->id ? 'selected' : '' }}>
                                    {{ $sec->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                @if($enrollment)
                <div class="col-12">
                    <small class="text-muted">
                        Current: <span class="fw-semibold">{{ $enrollment->class?->name }}</span>
                        / <span class="fw-semibold">{{ $enrollment->section?->name }}</span>
                        ({{ $enrollment->session?->name }})
                    </small>
                </div>
                @endif
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Update Student
                </button>
                <a href="{{ route('school.students.index', $school->slug) }}"
                   class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Delete --}}
@can('delete student')
<div class="card border-0 mt-3" style="max-width:750px;background:#fff5f5;border-left:3px solid #dc3545 !important">
    <div class="card-body p-3 d-flex justify-content-between align-items-center">
        <div>
            <div class="fw-semibold text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Danger Zone</div>
            <div class="text-muted small">Permanently delete this student and all their records</div>
        </div>
        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <i class="bi bi-trash me-1"></i>Delete
        </button>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <i class="bi bi-trash3 text-danger fs-2 mb-2 d-block"></i>
                <h6 class="fw-bold text-danger">Delete {{ $student->name }}?</h6>
                <p class="small text-muted mb-3">This cannot be undone.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST"
                          action="{{ route('school.students.destroy', [$school->slug, $student]) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
document.getElementById('classSelect').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const sections = JSON.parse(opt.dataset.sections || '[]');
    const sel = document.getElementById('sectionSelect');
    sel.innerHTML = '<option value="">Select Section</option>';
    sections.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
});
</script>
@endpush
@endsection
