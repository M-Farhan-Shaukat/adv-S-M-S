@extends('school.layouts.app')
@section('title', 'Subjects')
@section('breadcrumb')
    <li class="breadcrumb-item active">Subjects</li>
@endsection

@section('content')
<div class="row g-3">
    {{-- Add Subject Form --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Subject
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.subjects.store', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Class *</label>
                        <select name="school_class_id" id="classSelect" class="form-select form-select-sm" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm"
                               placeholder="e.g. Mathematics" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>Add Subject
                    </button>
                </form>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('school.subjects.assignments', $school->slug) }}"
               class="btn btn-outline-primary w-100 btn-sm">
                <i class="bi bi-person-check me-2"></i>Manage Teacher Assignments
            </a>
        </div>
    </div>

    {{-- Subjects List grouped by class --}}
    <div class="col-md-8">
        @php
            $grouped = $subjects->groupBy(fn($s) => $s->schoolClass?->name ?? 'No Class');
        @endphp

        @forelse($grouped as $className => $classSubjects)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-building me-2 text-primary"></i>{{ $className }}
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $classSubjects->count() }} subjects</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Subject Name</th><th>Assignments</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($classSubjects as $subject)
                        <tr>
                            <td class="fw-semibold">{{ $subject->name }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ $subject->assignments_count }} assigned
                                </span>
                            </td>
                            <td>
                                @can('delete subject')
                                <form method="POST"
                                      action="{{ route('school.subjects.destroy', [$school->slug, $subject]) }}"
                                      onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-book fs-1 mb-3 d-block opacity-25"></i>
                No subjects yet. Add subjects using the form.
            </div>
        </div>
        @endforelse

        @if($subjects->hasPages())
        <div class="mt-2">{{ $subjects->links() }}</div>
        @endif
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
