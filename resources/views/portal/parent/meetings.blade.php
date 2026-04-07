@extends('portal.layouts.app')
@section('title', 'Meetings')
@section('portal-name', 'Parent Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('parent.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
<a href="{{ route('parent.results', $slug) }}" class="portal-nav-link"><i class="bi bi-award"></i> Results</a>
<a href="{{ route('parent.fees', $slug) }}" class="portal-nav-link"><i class="bi bi-receipt"></i> Fee Vouchers</a>
<a href="{{ route('parent.exam-schedule', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar3"></i> Exam Schedule</a>
<a href="{{ route('parent.complaints', $slug) }}" class="portal-nav-link"><i class="bi bi-chat-left-text"></i> Complaints</a>
<a href="{{ route('parent.meetings', $slug) }}" class="portal-nav-link active"><i class="bi bi-calendar-event"></i> Meetings</a>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-calendar-event me-2 text-primary"></i>Meeting Schedules
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Title</th><th>Type</th><th>Date & Time</th><th>Venue</th><th>Duration</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($meetings as $m)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $m->title }}</div>
                            <small class="text-muted">{{ Str::limit($m->description, 50) }}</small>
                        </td>
                        <td><span class="badge bg-info bg-opacity-10 text-info">{{ str_replace('_', ' ', ucfirst($m->type)) }}</span></td>
                        <td class="small">{{ \Carbon\Carbon::parse($m->meeting_date)->format('d M Y, h:i A') }}</td>
                        <td class="small">{{ $m->venue ?? '-' }}</td>
                        <td class="small">{{ $m->duration_minutes }} min</td>
                        <td>
                            <span class="badge bg-{{ $m->status === 'completed' ? 'success' : ($m->status === 'cancelled' ? 'danger' : 'primary') }}">
                                {{ ucfirst($m->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No meetings scheduled</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($meetings->hasPages())
    <div class="card-footer bg-white border-0">{{ $meetings->links() }}</div>
    @endif
</div>
@endsection
