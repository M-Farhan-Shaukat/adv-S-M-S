@extends('school.layouts.app')
@section('title', 'Complaints')
@section('breadcrumb')
    <li class="breadcrumb-item active">Complaints</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-chat-left-text me-2"></i>Complaints</h5>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Subject</th><th>By</th><th>Type</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($complaints as $c)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $c->subject }}</div>
                            <small class="text-muted">{{ Str::limit($c->description, 60) }}</small>
                        </td>
                        <td class="small">{{ $c->user?->name }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($c->type) }}</span></td>
                        <td>
                            <span class="badge bg-{{ $c->status === 'resolved' ? 'success' : ($c->status === 'pending' ? 'warning' : ($c->status === 'in_progress' ? 'info' : 'danger')) }}">
                                {{ str_replace('_', ' ', ucfirst($c->status)) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $c->created_at->format('d M Y') }}</td>
                        <td>
                            @can('resolve complaint')
                            @if($c->status !== 'resolved')
                            <button class="btn btn-xs btn-outline-success" onclick="openResolve({{ $c->id }})">
                                <i class="bi bi-check-circle"></i> Resolve
                            </button>
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No complaints</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($complaints->hasPages())
    <div class="card-footer bg-white border-0">{{ $complaints->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-check-circle me-2"></i>Resolve Complaint</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="resolveForm">
                @csrf
                <div class="modal-body">
                    <label class="form-label small fw-semibold">Resolution *</label>
                    <textarea name="resolution" class="form-control form-control-sm" rows="4" required></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">Mark Resolved</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>

@push('scripts')
<script>
function openResolve(id) {
    document.getElementById('resolveForm').action = `/{{ app('school')->slug }}/complaints/${id}/resolve`;
    new bootstrap.Modal(document.getElementById('resolveModal')).show();
}
</script>
@endpush
@endsection
