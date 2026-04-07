@extends('school.layouts.app')
@section('title', 'Student Attendance')
@section('breadcrumb')
    <li class="breadcrumb-item active">Student Attendance</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar2-check me-2"></i>Student Attendance</h5>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Section</label>
                <select name="section_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ $sectionId == $section->id ? 'selected' : '' }}>
                            {{ $section->schoolClass?->name }} - {{ $section->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

@if($students->isNotEmpty())
<form method="POST" action="{{ route('school.attendance.students.mark', $school->slug) }}">
    @csrf
    <input type="hidden" name="section_id" value="{{ $sectionId }}">
    <input type="hidden" name="date" value="{{ $date }}">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Roll No</th>
                            <th>
                                <div class="d-flex gap-2">
                                    <span>Present</span>
                                    <span>Absent</span>
                                    <span>Leave</span>
                                    <span>Late</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        @php $att = $attendances->get($student->id); @endphp
                        <tr>
                            <td class="text-muted small">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $student->name }}</td>
                            <td class="small text-muted">{{ $student->roll_number ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-3">
                                    @foreach(['present' => 'success', 'absent' => 'danger', 'leave' => 'warning', 'late' => 'info'] as $status => $color)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                            name="attendance[{{ $student->id }}]"
                                            value="{{ $status }}"
                                            id="att_{{ $student->id }}_{{ $status }}"
                                            {{ ($att?->status ?? 'present') === $status ? 'checked' : '' }}>
                                        <label class="form-check-label text-{{ $color }} small" for="att_{{ $student->id }}_{{ $status }}">
                                            {{ ucfirst($status) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-check-lg me-1"></i>Save Attendance
            </button>
        </div>
    </div>
</form>
@elseif($sectionId)
<div class="alert alert-info border-0">No students found in this section.</div>
@else
<div class="alert alert-secondary border-0">Please select a section to mark attendance.</div>
@endif
@endsection
