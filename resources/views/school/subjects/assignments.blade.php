@extends('school.layouts.app')
@section('title', 'Subject Assignments')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.subjects.index', $school->slug) }}">Subjects</a></li>
    <li class="breadcrumb-item active">Assignments</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-person-check me-2 text-primary"></i>Assign Teacher to Subject
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.subjects.assign', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject *</label>
                        <select name="subject_id" class="form-select form-select-sm" required>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Teacher *</label>
                        <select name="teacher_id" class="form-select form-select-sm" required>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Class *</label>
                        <select name="school_class_id" id="assignClassSelect" class="form-select form-select-sm" required>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" data-sections="{{ $c->sections->toJson() }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Section *</label>
                        <select name="section_id" id="assignSectionSelect" class="form-select form-select-sm" required>
                            @if($classes->first())
                                @foreach($classes->first()->sections as $sec)
                                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Session *</label>
                        <select name="school_session_id" class="form-select form-select-sm" required>
                            <option value="{{ $session?->id }}">{{ $session?->name }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-check-lg me-1"></i>Assign
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">Current Assignments</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Subject</th><th>Teacher</th><th>Class</th><th>Section</th></tr></thead>
                        <tbody>
                            @forelse($assignments as $a)
                            <tr>
                                <td class="fw-semibold">{{ $a->subject?->name }}</td>
                                <td>{{ $a->teacher?->name }}</td>
                                <td>{{ $a->schoolClass?->name }}</td>
                                <td>{{ $a->section?->name }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No assignments yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('assignClassSelect').addEventListener('change', function() {
    const sections = JSON.parse(this.options[this.selectedIndex].dataset.sections || '[]');
    const sel = document.getElementById('assignSectionSelect');
    sel.innerHTML = '';
    sections.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.name}</option>`);
});
</script>
@endpush
@endsection
