@extends('school.layouts.app')
@section('title', 'Teacher Assignments')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.subjects.index', $school->slug) }}">Subjects</a></li>
    <li class="breadcrumb-item active">Teacher Assignments</li>
@endsection

@section('content')
<div class="row g-3">

    {{-- Assign Form --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-person-check me-2 text-primary"></i>Assign Teacher to Subject
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.subjects.assign', $school->slug) }}">
                    @csrf

                    {{-- 1. Class first --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Class *</label>
                        <select name="school_class_id" id="assignClassSelect"
                                class="form-select form-select-sm" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}"
                                        data-sections="{{ $c->sections->toJson() }}">
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Section (loads from class) --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Section *</label>
                        <select name="section_id" id="assignSectionSelect"
                                class="form-select form-select-sm" required>
                            <option value="">Select Class First</option>
                        </select>
                    </div>

                    {{-- 3. Subject (loads via AJAX by class) --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject *</label>
                        <select name="subject_id" id="assignSubjectSelect"
                                class="form-select form-select-sm" required>
                            <option value="">Select Class First</option>
                        </select>
                    </div>

                    {{-- 4. Teacher --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Teacher *</label>
                        <select name="teacher_id" class="form-select form-select-sm" required>
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 5. Session --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Session *</label>
                        <select name="school_session_id" class="form-select form-select-sm" required>
                            <option value="">Select Session</option>
                            @foreach(\App\Models\SchoolSession::where('school_id', $school->id)->orderBy('name')->get() as $s)
                                <option value="{{ $s->id }}"
                                    {{ $session?->id == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-check-lg me-1"></i>Assign
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Assignments List --}}
    <div class="col-lg-8">

        {{-- Class filter --}}
        <form method="GET" class="d-flex gap-2 mb-3">
            <select name="class_id" class="form-select form-select-sm" style="max-width:200px"
                    onchange="this.form.submit()">
                <option value="">All Classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
            @if($classId)
                <a href="{{ route('school.subjects.assignments', $school->slug) }}"
                   class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $a)
                            <tr>
                                <td class="fw-semibold">{{ $a->subject?->name }}</td>
                                <td class="small">{{ $a->schoolClass?->name }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $a->section?->name }}</span></td>
                                <td class="small">{{ $a->teacher?->name ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No assignments yet
                                    @if($classId) for this class @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($assignments->hasPages())
            <div class="card-footer bg-white border-0">{{ $assignments->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
const subjectAjaxUrl = "{{ route('school.subjects.by-class', $school->slug) }}";

document.getElementById('assignClassSelect').addEventListener('change', function () {
    const classId  = this.value;
    const opt      = this.options[this.selectedIndex];
    const sections = JSON.parse(opt.dataset.sections || '[]');

    // Populate sections
    const secSel = document.getElementById('assignSectionSelect');
    secSel.innerHTML = '<option value="">Select Section</option>';
    sections.forEach(s => secSel.insertAdjacentHTML('beforeend',
        `<option value="${s.id}">${s.name}</option>`));

    // Load subjects via AJAX
    const subSel = document.getElementById('assignSubjectSelect');
    subSel.innerHTML = '<option value="">Loading...</option>';

    if (!classId) {
        subSel.innerHTML = '<option value="">Select Class First</option>';
        return;
    }

    fetch(`${subjectAjaxUrl}?class_id=${classId}`)
        .then(r => r.json())
        .then(data => {
            subSel.innerHTML = '<option value="">Select Subject</option>';
            if (data.length === 0) {
                subSel.innerHTML = '<option value="">No subjects for this class</option>';
                return;
            }
            data.forEach(s => subSel.insertAdjacentHTML('beforeend',
                `<option value="${s.id}">${s.name}</option>`));
        });
});
</script>
@endpush
@endsection
