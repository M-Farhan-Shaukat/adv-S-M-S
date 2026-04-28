@extends('school.layouts.app')
@section('title', 'Sections - ' . $class->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.classes.index', $school->slug) }}">Classes</a></li>
    <li class="breadcrumb-item active">{{ $class->name }} — Sections</li>
@endsection

@section('content')
<div class="row g-3">
    {{-- Add Section --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Section to {{ $class->name }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.classes.sections.store', [$school->slug, $class]) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Section Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm"
                               placeholder="e.g. A, B, C" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>Add Section
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Sections List --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                Sections in {{ $class->name }}
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr><th>Section Name</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $section)
                        <tr>
                            <td class="fw-semibold">{{ $section->name }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-xs btn-outline-warning"
                                            onclick="openRename({{ $section->id }}, '{{ addslashes($section->name) }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST"
                                          action="{{ route('school.classes.sections.destroy', [$school->slug, $section]) }}"
                                          onsubmit="return confirm('Delete section {{ $section->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center py-4 text-muted">No sections yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Rename Modal --}}
<div class="modal fade" id="renameModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-pencil me-2 text-warning"></i>Rename Section</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="renameForm">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <input type="text" name="name" id="renameInput"
                           class="form-control form-control-sm" required>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-warning">
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
function openRename(id, name) {
    document.getElementById('renameInput').value = name;
    document.getElementById('renameForm').action =
        '{{ url($school->slug . "/classes/sections") }}/' + id;
    new bootstrap.Modal(document.getElementById('renameModal')).show();
}
</script>
@endpush
