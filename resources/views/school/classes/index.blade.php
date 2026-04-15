@extends('school.layouts.app')
@section('title', 'Classes')
@section('breadcrumb')
    <li class="breadcrumb-item active">Classes</li>
@endsection

@section('content')
<div class="row g-3">
    <!-- Add Class Form -->
    @can('create class')
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Class
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.classes.store', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Class Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm"
                               placeholder="e.g. Grade 1" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Code</label>
                        <input type="text" name="code" class="form-control form-control-sm"
                               placeholder="e.g. G1" value="{{ old('code') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Session *</label>
                        <select name="school_session_id" class="form-select form-select-sm" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ old('school_session_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>Create Class
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan

    <!-- Classes List -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">All Classes</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Class Name</th><th>Code</th><th>Session</th><th>Sections</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $class)
                            <tr>
                                <td class="fw-semibold">{{ $class->name }}</td>
                                <td class="small text-muted">{{ $class->code ?? '-' }}</td>
                                <td class="small text-muted">{{ $class->session?->name ?? '-' }}</td>
                                <td>
                                    @foreach($class->sections as $sec)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary me-1">{{ $sec->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('school.classes.sections', [$school->slug, $class]) }}"
                                           class="btn btn-xs btn-outline-primary">
                                            <i class="bi bi-diagram-3"></i> Sections
                                        </a>
                                        @can('update class')
                                        <button class="btn btn-xs btn-outline-warning"
                                                onclick="openEditModal({{ $class->id }}, '{{ addslashes($class->name) }}', '{{ $class->code }}', {{ $class->school_session_id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @endcan
                                        @can('delete class')
                                        <form method="POST"
                                              action="{{ route('school.classes.destroy', [$school->slug, $class]) }}"
                                              onsubmit="return confirm('Delete this class?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No classes created yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($classes->hasPages())
            <div class="card-footer bg-white border-0">{{ $classes->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-pencil me-2 text-warning"></i>Edit Class</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editClassForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Class Name *</label>
                        <input type="text" name="name" id="editClassName" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Code</label>
                        <input type="text" name="code" id="editClassCode" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Session *</label>
                        <select name="school_session_id" id="editClassSession" class="form-select form-select-sm" required>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="bi bi-check-lg me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection

@push('scripts')
<script>
function openEditModal(id, name, code, sessionId) {
    document.getElementById('editClassName').value    = name;
    document.getElementById('editClassCode').value    = code || '';
    document.getElementById('editClassSession').value = sessionId;
    document.getElementById('editClassForm').action   =
        `{{ url($school->slug . '/classes') }}/${id}`;
    new bootstrap.Modal(document.getElementById('editClassModal')).show();
}
</script>
@endpush
