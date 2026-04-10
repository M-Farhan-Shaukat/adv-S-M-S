@extends('portal.teacher.layouts.app')
@section('title', 'Teacher Dashboard')

@section('content')
@php $slug = app('school')->slug; @endphp

{{-- Welcome --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-body p-4" style="background: linear-gradient(135deg, #0f4c75, #1b6ca8)">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary flex-shrink-0"
                 style="width:55px;height:55px;font-size:1.3rem">
                {{ strtoupper(substr($teacher->name, 0, 1)) }}
            </div>
            <div class="text-white">
                <h5 class="fw-bold mb-1">Welcome, {{ $teacher->name }}</h5>
                <div class="opacity-75 small">
                    <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, d F Y') }}
                    &nbsp;•&nbsp;
                    <i class="bi bi-mortarboard me-1"></i>{{ $assignments->count() }} Subject(s) Assigned
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Today's Attendance Status --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                @if($todayAttendance)
                    @if($todayAttendance->check_in && !$todayAttendance->check_out)
                        <i class="bi bi-clock-history fs-2 text-warning mb-2 d-block"></i>
                        <div class="fw-bold text-warning">Checked In</div>
                        <div class="text-muted small">{{ $todayAttendance->check_in }}</div>
                    @elseif($todayAttendance->check_out)
                        <i class="bi bi-check-circle-fill fs-2 text-success mb-2 d-block"></i>
                        <div class="fw-bold text-success">Done for Today</div>
                        <div class="text-muted small">
                            {{ $todayAttendance->check_in }} – {{ $todayAttendance->check_out }}
                            ({{ intdiv($todayAttendance->working_minutes, 60) }}h {{ $todayAttendance->working_minutes % 60 }}m)
                        </div>
                    @else
                        <i class="bi bi-x-circle-fill fs-2 text-danger mb-2 d-block"></i>
                        <div class="fw-bold text-danger">Absent Today</div>
                    @endif
                @else
                    <i class="bi bi-question-circle fs-2 text-muted mb-2 d-block"></i>
                    <div class="fw-bold text-muted">Not Marked Yet</div>
                    <div class="text-muted small">{{ today()->format('d M Y') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-pencil-square fs-2 text-warning mb-2 d-block"></i>
                <div class="fs-3 fw-bold text-warning">{{ $pendingMarks }}</div>
                <div class="text-muted small">Pending Mark Entries</div>
                @if($pendingMarks > 0)
                    <a href="{{ route('teacher.exam-schedule', $slug) }}" class="btn btn-sm btn-outline-warning mt-2">Enter Marks</a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <i class="bi bi-cash-stack fs-2 text-success mb-2 d-block"></i>
                @if($latestPayroll)
                    <div class="fs-4 fw-bold text-success">Rs. {{ number_format($latestPayroll->net_salary, 0) }}</div>
                    <div class="text-muted small">{{ date('F', mktime(0,0,0,$latestPayroll->month,1)) }} {{ $latestPayroll->year }} Salary</div>
                @else
                    <div class="text-muted">No payroll yet</div>
                @endif
                <a href="{{ route('teacher.payroll', $slug) }}" class="btn btn-sm btn-outline-success mt-2">View Payroll</a>
            </div>
        </div>
    </div>
</div>

{{-- Assigned Subjects --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-book me-2 text-primary"></i>My Subjects
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Subject</th><th>Class</th><th>Section</th></tr></thead>
                        <tbody>
                            @forelse($assignments as $a)
                            <tr>
                                <td class="fw-semibold">{{ $a->subject?->name }}</td>
                                <td class="small">{{ $a->schoolClass?->name }}</td>
                                <td class="small">{{ $a->section?->name }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-3 text-muted small">No subjects assigned</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-calendar3 me-2 text-danger"></i>Upcoming Exams
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Subject</th><th>Section</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                            @forelse($upcomingExams as $s)
                            <tr>
                                <td class="fw-semibold small">{{ $s->subject?->name }}</td>
                                <td class="small">{{ $s->section?->name }}</td>
                                <td class="small">{{ \Carbon\Carbon::parse($s->exam_date)->format('d M') }}</td>
                                <td>
                                    <a href="{{ route('teacher.marks.enter', [$slug, $s]) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted small">No upcoming exams</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
