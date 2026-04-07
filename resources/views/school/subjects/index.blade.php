@extends('school.layouts.app')
@section('title', 'Subjects')
@section('breadcrumb')
    <li class="breadcrumb-item active">Subjects</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Subject
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.subjects.store', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Mathematics" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>Add Subject
                    </button>
                </form>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('school.subjects.assignments', $school->slug) }}" class="btn btn-outline-primary w-100">
                <i class="bi bi-person-check me-2"></i>Manage Teacher Assignments
            </a>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">All Subjects</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Subject Name</th><th>Assignments</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($subjects as $subject)
                        <tr>
                            <td class="fw-semibold">{{ $subject->name }}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $subject->assignments_count }} assigned</span></td>
                            <td>
                                @can('delete subject')
                                <form method="POST" action="{{ route('school.subjects.destroy', [$school->slug, $subject]) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">No subjects yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
