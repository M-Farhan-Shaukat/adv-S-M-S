@extends('portal.layouts.app')
@section('title', 'Student Dashboard')
@section('portal-name', 'Student Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('student.dashboard', $slug) }}" class="portal-nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('student.results', $slug) }}" class="portal-nav-link {{ request()->routeIs('student.results') ? 'active' : '' }}">
    <i class="bi bi-award"></i> My Results
</a>
<a href="{{ route('student.exam-schedule', $slug) }}" class="portal-nav-link {{ request()->routeIs('student.exam-schedule') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Exam Schedule
</a>
<a href="{{ route('student.attendance', $slug) }}" class="portal-nav-link {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
    <i class="bi bi-calendar-check"></i> Attendance
</a>
<a href="{{ route('student.fees', $slug) }}" class="portal-nav-link {{ request()->routeIs('student.fees') ? 'active' : '' }}">
    <i class="bi bi-receipt"></i> Fee Vouchers
</a>
@endsection

@section('content')
<!-- Student Info -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
    <div class="card-body text-white">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center" style="width:60px;height:60px">
                <i class="bi bi-person fs-3 text-primary"></i>
            </div>
            <div>
                <h5 class="mb-1">{{ $student->name }}</h5>
                @if($student->currentEnrollment)
                <div class="opacity-90 small">
                    {{ $student->currentEnrollment->class?->name }} - {{ $student->currentEnrollment->section?->name }}
                    @if($student->currentEnrollment->is_class_monitor)
                        <span class="badge bg-warning text-dark ms-2"><i class="bi bi-star-fill"></i> Class Monitor</span>
                    @endif
                </div>
                @endif
                <div class="opacity-75 small">Roll No: {{ $student->roll_number ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-danger">{{ $pendingFees }}</div>
                <div class="text-muted small">Pending Fees</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-primary">{{ $recentMarks->count() }}</div>
                <div class="text-muted small">Recent Results</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Results -->
@if($recentMarks->count())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-award me-2 text-warning"></i>Recent Results
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Subject</th><th>Marks</th><th>Grade</th></tr></thead>
                <tbody>
                    @foreach($recentMarks as $mark)
                    <tr>
                        <td>{{ $mark->examSchedule?->subject?->name }}</td>
                        <td>{{ $mark->obtained_marks }} / {{ $mark->examSchedule?->total_marks }}</td>
                        <td><span class="badge bg-{{ in_array($mark->grade, ['A+','A','B']) ? 'success' : ($mark->grade === 'F' ? 'danger' : 'warning') }}">{{ $mark->grade }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
