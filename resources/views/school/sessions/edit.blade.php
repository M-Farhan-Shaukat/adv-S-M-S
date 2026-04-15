@extends('school.layouts.app')
@section('title', 'Edit Session')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.sessions.index', $school->slug) }}">Sessions</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-pencil me-2 text-warning"></i>Edit Session: {{ $session->name }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.sessions.update', [$school->slug, $session]) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label small fw-semibold">Session Name *</label>
                <input type="text" name="name" class="form-control form-control-sm"
                       value="{{ old('name', $session->name) }}" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-semibold">Start Date *</label>
                    <input type="date" name="start_date" class="form-control form-control-sm"
                           value="{{ old('start_date', $session->start_date) }}" required>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-semibold">End Date *</label>
                    <input type="date" name="end_date" class="form-control form-control-sm"
                           value="{{ old('end_date', $session->end_date) }}" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">Status *</label>
                <select name="status" class="form-select form-select-sm" required>
                    @foreach(['active','inactive','exam','completed'] as $s)
                    <option value="{{ $s }}" {{ old('status', $session->status) == $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="bi bi-check-lg me-1"></i>Update
                </button>
                <a href="{{ route('school.sessions.index', $school->slug) }}"
                   class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
