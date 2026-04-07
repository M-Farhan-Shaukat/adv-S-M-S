@extends('portal.layouts.app')
@section('title', 'Complaints')
@section('portal-name', 'Parent Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('parent.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
<a href="{{ route('parent.results', $slug) }}" class="portal-nav-link"><i class="bi bi-award"></i> Results</a>
<a href="{{ route('parent.fees', $slug) }}" class="portal-nav-link"><i class="bi bi-receipt"></i> Fee Vouchers</a>
<a href="{{ route('parent.exam-schedule', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar3"></i> Exam Schedule</a>
<a href="{{ route('parent.complaints', $slug) }}" class="portal-nav-link active"><i class="bi bi-chat-left-text"></i> Complaints</a>
<a href="{{ route('parent.meetings', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar-event"></i> Meetings</a>
@endsection

@section('content')
<!-- Submit Complaint -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-plus-circle me-2 text-primary"></i>Submit New Complaint
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('parent.complaints.store', app('school')->slug) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Subject *</label>
                    <input type="text" name="subject" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="academic">Academic</option>
                        <option value="behavioral">Behavioral</option>
                        <option value="facility">Facility</option>
                        <option value="staff">Staff</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Description *</label>
                    <textarea name="description" class="form-control form-control-sm" rows="3" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-send me-1"></i>Submit Complaint
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Complaints List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">My Complaints</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Subject</th><th>Type</th><th>Status</th><th>Date</th><th>Resolution</th></tr>
                </thead>
                <tbody>
                    @forelse($complaints as $c)
                    <tr>
                        <td class="fw-semibold">{{ $c->subject }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($c->type) }}</span></td>
                        <td>
                            <span class="badge bg-{{ $c->status === 'resolved' ? 'success' : ($c->status === 'pending' ? 'warning' : 'info') }}">
                                {{ ucfirst($c->status) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $c->created_at->format('d M Y') }}</td>
                        <td class="small">{{ $c->resolution ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No complaints submitted</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($complaints->hasPages())
    <div class="card-footer bg-white border-0">{{ $complaints->links() }}</div>
    @endif
</div>
@endsection
