@extends('school.layouts.app')
@section('title', 'Meetings')
@section('breadcrumb')
    <li class="breadcrumb-item active">Meetings</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2"></i>Meeting Schedules</h5>
    @can('schedule meeting')
    <a href="{{ route('school.meetings.create', $school->slug) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Schedule Meeting
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Title</th><th>Type</th><th>Date & Time</th><th>Venue</th><th>Duration</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($meetings as $m)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $m->title }}</div>
                            <small class="text-muted">By: {{ $m->organizer?->name }}</small>
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
                        <td>
                            @can('schedule meeting')
                            @if($m->status === 'scheduled')
                            <div class="d-flex gap-1">
                                <form method="POST" action="{{ route('school.meetings.status', [$school->slug, $m]) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button class="btn btn-xs btn-outline-success">Done</button>
                                </form>
                                <form method="POST" action="{{ route('school.meetings.status', [$school->slug, $m]) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button class="btn btn-xs btn-outline-danger">Cancel</button>
                                </form>
                            </div>
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No meetings scheduled</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($meetings->hasPages())
    <div class="card-footer bg-white border-0">{{ $meetings->links() }}</div>
    @endif
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
