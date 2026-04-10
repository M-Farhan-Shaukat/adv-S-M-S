@extends('portal.teacher.layouts.app')
@section('title', 'Parent Remarks')

@section('content')
@php $slug = app('school')->slug; @endphp

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-chat-left-text me-2 text-primary"></i>Parent Remarks on Your Subjects
    </div>
    <div class="card-body p-0">
        @forelse($remarks as $r)
        <div class="p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="fw-semibold">{{ $r->subject?->name }}</span>
                    <span class="text-muted small ms-2">— {{ $r->student?->name }}</span>
                    <span class="badge bg-{{ $r->type === 'positive' ? 'success' : ($r->type === 'negative' ? 'danger' : 'info') }} ms-2">
                        {{ ucfirst($r->type) }}
                    </span>
                </div>
                <small class="text-muted">{{ $r->created_at->format('d M Y') }}</small>
            </div>
            <p class="mb-2 small">{{ $r->remark }}</p>

            @if($r->teacher_response)
                <div class="bg-light rounded p-2 small">
                    <strong>Your Response:</strong> {{ $r->teacher_response }}
                </div>
            @else
                <button class="btn btn-xs btn-outline-primary" onclick="openRespond({{ $r->id }})">
                    <i class="bi bi-reply me-1"></i>Respond
                </button>
            @endif
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-left fs-1 mb-3 d-block"></i>
            No parent remarks yet
        </div>
        @endforelse
    </div>
    @if($remarks->hasPages())
    <div class="card-footer bg-white border-0">{{ $remarks->links() }}</div>
    @endif
</div>

{{-- Respond Modal --}}
<div class="modal fade" id="respondModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Respond to Remark</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="respondForm">
                @csrf
                <div class="modal-body">
                    <label class="form-label small fw-semibold">Your Response *</label>
                    <textarea name="teacher_response" class="form-control form-control-sm" rows="4" required></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Submit Response</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>

@push('scripts')
<script>
function openRespond(id) {
    document.getElementById('respondForm').action = `/{{ app('school')->slug }}/teacher-portal/remarks/${id}/respond`;
    new bootstrap.Modal(document.getElementById('respondModal')).show();
}
</script>
@endpush
@endsection
