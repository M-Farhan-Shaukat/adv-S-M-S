@extends('portal.layouts.app')
@section('title', 'Parent Dashboard')
@section('portal-name', 'Parent Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('parent.dashboard', $slug) }}" class="portal-nav-link {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('parent.results', $slug) }}" class="portal-nav-link {{ request()->routeIs('parent.results') ? 'active' : '' }}">
    <i class="bi bi-award"></i> Results
</a>
<a href="{{ route('parent.fees', $slug) }}" class="portal-nav-link {{ request()->routeIs('parent.fees') ? 'active' : '' }}">
    <i class="bi bi-receipt"></i> Fee Vouchers
</a>
<a href="{{ route('parent.exam-schedule', $slug) }}" class="portal-nav-link {{ request()->routeIs('parent.exam-schedule') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Exam Schedule
</a>
<a href="{{ route('parent.complaints', $slug) }}" class="portal-nav-link {{ request()->routeIs('parent.complaints') ? 'active' : '' }}">
    <i class="bi bi-chat-left-text"></i> Complaints
</a>
<a href="{{ route('parent.meetings', $slug) }}" class="portal-nav-link {{ request()->routeIs('parent.meetings') ? 'active' : '' }}">
    <i class="bi bi-calendar-event"></i> Meetings
</a>
@endsection

@section('content')
<!-- Children Cards -->
<div class="row g-3 mb-4">
    @foreach($children as $child)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:50px;height:50px">
                        <i class="bi bi-person fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $child->name }}</div>
                        @if($child->currentEnrollment)
                        <small class="text-muted">
                            {{ $child->currentEnrollment->class?->name }} - {{ $child->currentEnrollment->section?->name }}
                        </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-danger">{{ $pendingFees }}</div>
                <div class="text-muted small">Pending Fee Vouchers</div>
                <a href="{{ route('parent.fees', app('school')->slug) }}" class="btn btn-sm btn-outline-danger mt-2">View</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-primary">{{ $meetings->count() }}</div>
                <div class="text-muted small">Upcoming Meetings</div>
                <a href="{{ route('parent.meetings', app('school')->slug) }}" class="btn btn-sm btn-outline-primary mt-2">View</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fs-3 fw-bold text-success">{{ $children->count() }}</div>
                <div class="text-muted small">Children Enrolled</div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Meetings -->
@if($meetings->count())
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-calendar-event me-2 text-primary"></i>Upcoming Meetings
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Title</th><th>Date</th><th>Venue</th><th>Type</th></tr></thead>
                <tbody>
                    @foreach($meetings as $m)
                    <tr>
                        <td class="fw-semibold">{{ $m->title }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($m->meeting_date)->format('d M Y, h:i A') }}</td>
                        <td class="small">{{ $m->venue ?? '-' }}</td>
                        <td><span class="badge bg-info bg-opacity-10 text-info">{{ str_replace('_', ' ', ucfirst($m->type)) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
