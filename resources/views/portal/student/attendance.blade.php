@extends('portal.layouts.app')
@section('title', 'My Attendance')
@section('portal-name', 'Student Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('student.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
<a href="{{ route('student.results', $slug) }}" class="portal-nav-link"><i class="bi bi-award"></i> My Results</a>
<a href="{{ route('student.exam-schedule', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar3"></i> Exam Schedule</a>
<a href="{{ route('student.attendance', $slug) }}" class="portal-nav-link active"><i class="bi bi-calendar-check"></i> Attendance</a>
<a href="{{ route('student.fees', $slug) }}" class="portal-nav-link"><i class="bi bi-receipt"></i> Fee Vouchers</a>
@endsection

@section('content')
<!-- Stats -->
<div class="row g-3 mb-4">
    @php $total = array_sum($stats); @endphp
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-success">{{ $stats['present'] }}</div>
                <div class="text-muted small">Present</div>
                <div class="small text-success">{{ $total > 0 ? round(($stats['present']/$total)*100) : 0 }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-danger">{{ $stats['absent'] }}</div>
                <div class="text-muted small">Absent</div>
                <div class="small text-danger">{{ $total > 0 ? round(($stats['absent']/$total)*100) : 0 }}%</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-warning">{{ $stats['leave'] }}</div>
                <div class="text-muted small">Leave</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-primary">{{ $total }}</div>
                <div class="text-muted small">Total Days</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">Attendance Record</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Date</th><th>Status</th><th>Remarks</th></tr></thead>
                <tbody>
                    @forelse($attendances as $a)
                    <tr>
                        <td class="fw-semibold">{{ \Carbon\Carbon::parse($a->date)->format('d M Y, l') }}</td>
                        <td>
                            <span class="badge bg-{{ $a->status === 'present' ? 'success' : ($a->status === 'absent' ? 'danger' : ($a->status === 'late' ? 'warning' : 'info')) }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $a->remarks ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-4 text-muted">No attendance records</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendances->hasPages())
    <div class="card-footer bg-white border-0">{{ $attendances->links() }}</div>
    @endif
</div>
@endsection
