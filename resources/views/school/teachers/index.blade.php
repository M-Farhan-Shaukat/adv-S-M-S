@extends('school.layouts.app')
@section('title', 'Teachers')
@section('breadcrumb')
    <li class="breadcrumb-item active">Teachers</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>Teachers</h5>
    @can('create teacher')
    <a href="{{ route('school.teachers.create', $school->slug) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Teacher
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i></button>
                <a href="{{ route('school.teachers.index', $school->slug) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Phone</th><th>Salary</th><th>Daily Hours</th><th>Qualification</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $teacher->name }}</div>
                            <small class="text-muted">{{ $teacher->email }}</small>
                        </td>
                        <td class="small">{{ $teacher->phone ?? '-' }}</td>
                        <td>Rs. {{ number_format($teacher->salary, 0) }}</td>
                        <td class="small">{{ intdiv($teacher->daily_required_minutes, 60) }}h {{ $teacher->daily_required_minutes % 60 }}m</td>
                        <td class="small">{{ $teacher->qualification ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $teacher->is_active ? 'success' : 'danger' }}">
                                {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @can('update teacher')
                                <a href="{{ route('school.teachers.edit', [$school->slug, $teacher]) }}" class="btn btn-xs btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('school.teachers.toggle-status', [$school->slug, $teacher]) }}">
                                    @csrf
                                    <button class="btn btn-xs btn-outline-{{ $teacher->is_active ? 'danger' : 'success' }}">
                                        <i class="bi bi-{{ $teacher->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                                @endcan
                                @can('delete teacher')
                                <form method="POST" action="{{ route('school.teachers.destroy', [$school->slug, $teacher]) }}" onsubmit="return confirm('Delete this teacher?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No teachers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($teachers->hasPages())
    <div class="card-footer bg-white border-0">{{ $teachers->withQueryString()->links() }}</div>
    @endif
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
