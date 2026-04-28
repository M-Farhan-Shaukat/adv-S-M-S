@extends('school.layouts.app')
@section('title', 'Students')
@section('breadcrumb')
    <li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Students
        <span class="badge bg-primary bg-opacity-10 text-primary ms-1">{{ $students->total() }}</span>
    </h5>
    @can('create student')
    <a href="{{ route('school.students.create', $school->slug) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Student
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search name or roll no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('school.students.index', $school->slug) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Students grouped by class --}}
@php
    $grouped = $students->getCollection()->groupBy(fn($s) => $s->currentEnrollment?->class?->name ?? 'Not Enrolled');
    $classOrder = $classes->pluck('name')->toArray();
    $grouped = $grouped->sortBy(fn($v, $k) => array_search($k, $classOrder) !== false ? array_search($k, $classOrder) : 999);
@endphp

@forelse($grouped as $className => $classStudents)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold">
            <i class="bi bi-building me-2 text-primary"></i>{{ $className }}
        </span>
        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $classStudents->count() }} students</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Name</th>
                        <th>Section</th>
                        <th>Roll No</th>
                        <th>Guardian</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classStudents as $i => $student)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $student->name }}</div>
                            @if($student->email)
                                <small class="text-muted">{{ $student->email }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                {{ $student->currentEnrollment?->section?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $student->roll_number ?? '-' }}</td>
                        <td class="small">
                            <div>{{ $student->guardian_name ?? '-' }}</div>
                            @if($student->guardian_phone)
                                <small class="text-muted">{{ $student->guardian_phone }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $student->is_active ? 'success' : 'danger' }}">
                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('school.students.show', [$school->slug, $student]) }}"
                                   class="btn btn-xs btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('update student')
                                <a href="{{ route('school.students.edit', [$school->slug, $student]) }}"
                                   class="btn btn-xs btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('school.students.toggle-status', [$school->slug, $student]) }}">
                                    @csrf
                                    <button class="btn btn-xs btn-outline-{{ $student->is_active ? 'danger' : 'success' }}"
                                            title="{{ $student->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="bi bi-{{ $student->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                                @endcan
                                @can('delete student')
                                <form method="POST"
                                      action="{{ route('school.students.destroy', [$school->slug, $student]) }}"
                                      onsubmit="return confirm('Delete {{ $student->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@empty
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-people fs-1 mb-3 d-block opacity-25"></i>
        No students found.
        @can('create student')
        <a href="{{ route('school.students.create', $school->slug) }}" class="d-block mt-2">Add first student</a>
        @endcan
    </div>
</div>
@endforelse

@if($students->hasPages())
<div class="mt-2">{{ $students->withQueryString()->links() }}</div>
@endif

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
