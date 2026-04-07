@extends('school.layouts.app')
@section('title', 'Schedule Meeting')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.meetings.index', $school->slug) }}">Meetings</a></li>
    <li class="breadcrumb-item active">Schedule Meeting</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-calendar-plus me-2 text-primary"></i>Schedule New Meeting
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.meetings.store', $school->slug) }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold">Title *</label>
                    <input type="text" name="title" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Type *</label>
                    <select name="type" class="form-select form-select-sm" required>
                        <option value="parent_teacher">Parent-Teacher</option>
                        <option value="staff">Staff</option>
                        <option value="general">General</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" class="form-control form-control-sm" value="30" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Date & Time *</label>
                    <input type="datetime-local" name="meeting_date" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Venue</label>
                    <input type="text" name="venue" class="form-control form-control-sm" placeholder="e.g. Conference Room">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Invite Participants</label>
                    <select name="participants[]" class="form-select form-select-sm" multiple size="5">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->getRoleNames()->first() }})</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Schedule</button>
                <a href="{{ route('school.meetings.index', $school->slug) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
