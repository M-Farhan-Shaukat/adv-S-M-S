@extends('portal.layouts.app')
@section('title', 'Exam Schedule')
@section('portal-name', 'Parent Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('parent.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
<a href="{{ route('parent.results', $slug) }}" class="portal-nav-link"><i class="bi bi-award"></i> Results</a>
<a href="{{ route('parent.fees', $slug) }}" class="portal-nav-link"><i class="bi bi-receipt"></i> Fee Vouchers</a>
<a href="{{ route('parent.exam-schedule', $slug) }}" class="portal-nav-link active"><i class="bi bi-calendar3"></i> Exam Schedule</a>
<a href="{{ route('parent.complaints', $slug) }}" class="portal-nav-link"><i class="bi bi-chat-left-text"></i> Complaints</a>
<a href="{{ route('parent.meetings', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar-event"></i> Meetings</a>
@endsection

@section('content')
@if($children->count() > 1)
<div class="mb-3">
    <form method="GET" class="d-flex gap-2 align-items-center">
        <label class="small fw-semibold">Child:</label>
        <select name="student_id" class="form-select form-select-sm" style="width:200px" onchange="this.form.submit()">
            @foreach($children as $child)
                <option value="{{ $child->id }}" {{ $studentId == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
            @endforeach
        </select>
    </form>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-calendar3 me-2 text-primary"></i>Upcoming Exams
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Exam</th><th>Subject</th><th>Date</th><th>Time</th><th>Total Marks</th><th>Room</th></tr>
                </thead>
                <tbody>
                    @forelse($schedules as $s)
                    <tr>
                        <td class="fw-semibold">{{ $s->exam?->name }}</td>
                        <td>{{ $s->subject?->name }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y') }}</td>
                        <td class="small">{{ $s->start_time }} - {{ $s->end_time }}</td>
                        <td>{{ $s->total_marks }}</td>
                        <td class="small">{{ $s->room ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No upcoming exams</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
