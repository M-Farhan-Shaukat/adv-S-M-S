@extends('portal.teacher.layouts.app')
@section('title', 'My Attendance')

@section('content')
@php $slug = app('school')->slug; @endphp

{{-- Month Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Month</label>
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Year</label>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success">{{ $stats['present'] }}</div>
                <div class="text-muted small">Present</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-danger">{{ $stats['absent'] }}</div>
                <div class="text-muted small">Absent</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-warning">{{ $stats['leave'] }}</div>
                <div class="text-muted small">Leave</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary">
                    {{ intdiv($stats['total_minutes'], 60) }}h {{ $stats['total_minutes'] % 60 }}m
                </div>
                <div class="text-muted small">Total Hours</div>
            </div>
        </div>
    </div>
</div>

{{-- Attendance Records --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        Attendance - {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Working Hours</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($attendances as $a)
                    <tr>
                        <td class="fw-semibold small">{{ \Carbon\Carbon::parse($a->date)->format('d M Y, l') }}</td>
                        <td class="small">{{ $a->check_in ?? '-' }}</td>
                        <td class="small">{{ $a->check_out ?? '-' }}</td>
                        <td class="small {{ $a->working_minutes >= $teacher->daily_required_minutes ? 'text-success' : 'text-warning' }}">
                            {{ intdiv($a->working_minutes, 60) }}h {{ $a->working_minutes % 60 }}m
                        </td>
                        <td>
                            <span class="badge bg-{{ $a->status === 'present' ? 'success' : ($a->status === 'absent' ? 'danger' : 'warning') }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No attendance records for this month</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
