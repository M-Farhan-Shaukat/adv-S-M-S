@extends('school.layouts.app')
@section('title', 'Teacher Attendance')
@section('breadcrumb')
    <li class="breadcrumb-item active">Teacher Attendance</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i>Teacher Attendance</h5>
    <form method="GET" class="d-flex gap-2">
        <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Teacher</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Working Hours</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    @php $att = $attendances->get($teacher->id); @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $teacher->name }}</div>
                            <small class="text-muted">Required: {{ $teacher->daily_required_minutes }} min</small>
                        </td>
                        <td>
                            @if($att?->check_in)
                                <span class="badge bg-success bg-opacity-10 text-success">{{ $att->check_in }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($att?->check_out)
                                <span class="badge bg-info bg-opacity-10 text-info">{{ $att->check_out }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($att)
                                @php $hrs = intdiv($att->working_minutes, 60); $mins = $att->working_minutes % 60; @endphp
                                <span class="{{ $att->working_minutes >= $teacher->daily_required_minutes ? 'text-success' : 'text-warning' }} fw-semibold">
                                    {{ $hrs }}h {{ $mins }}m
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($att)
                                <span class="badge bg-{{ $att->status === 'present' ? 'success' : ($att->status === 'absent' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Not Marked</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if(!$att || !$att->check_in)
                                <form method="POST" action="{{ route('school.attendance.teachers.check-in', $school->slug) }}">
                                    @csrf
                                    <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                    <button class="btn btn-xs btn-success" title="Check In">
                                        <i class="bi bi-box-arrow-in-right"></i> In
                                    </button>
                                </form>
                                @endif
                                @if($att?->check_in && !$att->check_out)
                                <form method="POST" action="{{ route('school.attendance.teachers.check-out', $school->slug) }}">
                                    @csrf
                                    <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                    <button class="btn btn-xs btn-warning" title="Check Out">
                                        <i class="bi bi-box-arrow-right"></i> Out
                                    </button>
                                </form>
                                @endif
                                @if(!$att)
                                <form method="POST" action="{{ route('school.attendance.teachers.absent', $school->slug) }}">
                                    @csrf
                                    <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                    <button class="btn btn-xs btn-danger" title="Mark Absent">
                                        <i class="bi bi-x-circle"></i> Absent
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.5rem; font-size: 0.75rem; }</style>
@endsection
