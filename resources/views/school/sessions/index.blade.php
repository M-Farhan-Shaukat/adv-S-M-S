@extends('school.layouts.app')
@section('title', 'Sessions')
@section('breadcrumb')
    <li class="breadcrumb-item active">Sessions</li>
@endsection

@section('content')
<div class="row g-3">

    {{-- Create Form --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Session
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.sessions.store', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Session Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm"
                               placeholder="e.g. 2024-2026" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Start Date *</label>
                        <input type="date" name="start_date" class="form-control form-control-sm"
                               value="{{ old('start_date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">End Date *</label>
                        <input type="date" name="end_date" class="form-control form-control-sm"
                               value="{{ old('end_date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status *</label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="active"    {{ old('status') == 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="inactive"  {{ old('status') == 'inactive'  ? 'selected' : '' }}>Inactive</option>
                            <option value="exam"      {{ old('status') == 'exam'      ? 'selected' : '' }}>Exam</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>Create Session
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Sessions List --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">All Sessions</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                            <tr>
                                <td class="fw-semibold">{{ $session->name }}</td>
                                <td class="small text-muted">
                                    {{ \Carbon\Carbon::parse($session->start_date)->format('M Y') }}
                                    &rarr;
                                    {{ \Carbon\Carbon::parse($session->end_date)->format('M Y') }}
                                </td>
                                <td>
                                    @php
                                        $badge = match($session->status) {
                                            'active'    => 'success',
                                            'exam'      => 'warning',
                                            'completed' => 'secondary',
                                            default     => 'danger',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($session->status) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('school.sessions.edit', [$school->slug, $session]) }}"
                                           class="btn btn-xs btn-outline-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('school.sessions.destroy', [$school->slug, $session]) }}"
                                              onsubmit="return confirm('Delete this session?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No sessions yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($sessions->hasPages())
            <div class="card-footer bg-white border-0">{{ $sessions->links() }}</div>
            @endif
        </div>
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
