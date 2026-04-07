@extends('portal.layouts.app')
@section('title', 'Exam Schedule')
@section('portal-name', 'Student Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('student.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
<a href="{{ route('student.results', $slug) }}" class="portal-nav-link"><i class="bi bi-award"></i> My Results</a>
<a href="{{ route('student.exam-schedule', $slug) }}" class="portal-nav-link active"><i class="bi bi-calendar3"></i> Exam Schedule</a>
<a href="{{ route('student.attendance', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar-check"></i> Attendance</a>
<a href="{{ route('student.fees', $slug) }}" class="portal-nav-link"><i class="bi bi-receipt"></i> Fee Vouchers</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-calendar3 me-2 text-primary"></i>Upcoming Exams
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Exam</th><th>Subject</th><th>Date</th><th>Time</th><th>Total Marks</th><th>Passing</th><th>Room</th></tr>
                </thead>
                <tbody>
                    @forelse($schedules as $s)
                    <tr>
                        <td class="fw-semibold">{{ $s->exam?->name }}</td>
                        <td>{{ $s->subject?->name }}</td>
                        <td>
                            <span class="{{ \Carbon\Carbon::parse($s->exam_date)->isToday() ? 'text-danger fw-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y') }}
                                @if(\Carbon\Carbon::parse($s->exam_date)->isToday())
                                    <span class="badge bg-danger ms-1">Today!</span>
                                @endif
                            </span>
                        </td>
                        <td class="small">{{ $s->start_time }} - {{ $s->end_time }}</td>
                        <td>{{ $s->total_marks }}</td>
                        <td class="text-success">{{ $s->passing_marks }}</td>
                        <td class="small">{{ $s->room ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No upcoming exams</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
