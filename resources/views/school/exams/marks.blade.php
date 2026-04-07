@extends('school.layouts.app')
@section('title', 'Enter Marks')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.exams.index', app('school')->slug) }}">Exams</a></li>
    <li class="breadcrumb-item active">Enter Marks</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="row g-2 text-muted small">
            <div class="col-md-3"><strong>Exam:</strong> {{ $schedule->exam?->name }}</div>
            <div class="col-md-3"><strong>Subject:</strong> {{ $schedule->subject?->name }}</div>
            <div class="col-md-3"><strong>Section:</strong> {{ $schedule->section?->name }}</div>
            <div class="col-md-3"><strong>Total Marks:</strong> {{ $schedule->total_marks }} | Passing: {{ $schedule->passing_marks }}</div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('school.exams.marks.save', [app('school')->slug, $schedule]) }}">
    @csrf
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Marks (/ {{ $schedule->total_marks }})</th>
                            <th>Absent</th>
                            <th>Remarks</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        @php $mark = $marks->get($student->id); @endphp
                        <tr>
                            <td class="text-muted small">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $student->name }}</td>
                            <td>
                                <input type="number" name="marks[{{ $student->id }}][marks]"
                                    class="form-control form-control-sm" style="width:100px"
                                    min="0" max="{{ $schedule->total_marks }}"
                                    value="{{ $mark?->obtained_marks ?? '' }}"
                                    {{ $mark?->is_absent ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="marks[{{ $student->id }}][absent]"
                                        id="absent_{{ $student->id }}"
                                        {{ $mark?->is_absent ? 'checked' : '' }}
                                        onchange="toggleMarks(this, {{ $student->id }})">
                                    <label class="form-check-label small" for="absent_{{ $student->id }}">Absent</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="marks[{{ $student->id }}][remarks]"
                                    class="form-control form-control-sm" style="width:150px"
                                    value="{{ $mark?->remarks ?? '' }}" placeholder="Optional">
                            </td>
                            <td>
                                @if($mark && !$mark->is_absent)
                                    @php $pct = ($mark->obtained_marks / $schedule->total_marks) * 100; @endphp
                                    <span class="badge bg-{{ $pct >= 50 ? 'success' : 'danger' }}">
                                        {{ $mark->grade }}
                                    </span>
                                @elseif($mark?->is_absent)
                                    <span class="badge bg-secondary">Absent</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-check-lg me-1"></i>Save Marks
            </button>
            @can('publish result')
            <form method="POST" action="{{ route('school.exams.marks.publish', [app('school')->slug, $schedule]) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Publish results? Students will be able to see them.')">
                    <i class="bi bi-megaphone me-1"></i>Publish Results
                </button>
            </form>
            @endcan
        </div>
    </div>
</form>

@push('scripts')
<script>
function toggleMarks(checkbox, studentId) {
    const marksInput = document.querySelector(`input[name="marks[${studentId}][marks]"]`);
    marksInput.disabled = checkbox.checked;
    if (checkbox.checked) marksInput.value = '';
}
</script>
@endpush
@endsection
