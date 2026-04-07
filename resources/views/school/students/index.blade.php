@extends('school.layouts.app')
@section('title', 'Students')
@section('breadcrumb')
    <li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Students</h5>
    @can('create student')
    <a href="{{ route('school.students.create', $school->slug) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Student
    </a>
    @endcan
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('school.students.index', $school->slug) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Class / Section</th>
                        <th>Roll No</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold">{{ $student->name }}</div>
                            <small class="text-muted">{{ $student->email }}</small>
                        </td>
                        <td>
                            @if($student->currentEnrollment)
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $student->currentEnrollment->class?->name }}</span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $student->currentEnrollment->section?->name }}</span>
                                @if($student->currentEnrollment->is_class_monitor)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Monitor</span>
                                @endif
                            @else
                                <span class="text-muted small">Not enrolled</span>
                            @endif
                        </td>
                        <td class="small">{{ $student->roll_number ?? '-' }}</td>
                        <td class="small">{{ $student->phone ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $student->is_active ? 'success' : 'danger' }}">
                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('school.students.show', [$school->slug, $student]) }}" class="btn btn-xs btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('update student')
                                <a href="{{ route('school.students.edit', [$school->slug, $student]) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('promote student')
                                <button class="btn btn-xs btn-outline-success" title="Promote"
                                    onclick="openPromote({{ $student->id }}, '{{ $student->name }}')">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </button>
                                @endcan
                                @can('update student')
                                <form method="POST" action="{{ route('school.students.toggle-status', [$school->slug, $student]) }}">
                                    @csrf
                                    <button class="btn btn-xs btn-outline-{{ $student->is_active ? 'danger' : 'success' }}" title="{{ $student->is_active ? 'Disable' : 'Enable' }}">
                                        <i class="bi bi-{{ $student->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No students found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($students->hasPages())
    <div class="card-footer bg-white border-0">{{ $students->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Promote Modal -->
<div class="modal fade" id="promoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-arrow-up-circle me-2"></i>Promote Student</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="promoteForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Promoting: <strong id="promoteName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">New Session</label>
                        <select name="school_session_id" class="form-select form-select-sm" required>
                            @foreach(\App\Models\SchoolSession::where('school_id', app('school')->id)->get() as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">New Class</label>
                        <select name="school_class_id" class="form-select form-select-sm" required>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">New Section</label>
                        <select name="section_id" class="form-select form-select-sm" required>
                            @foreach(\App\Models\Section::where('school_id', app('school')->id)->get() as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">Promote</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }
</style>

@push('scripts')
<script>
function openPromote(id, name) {
    document.getElementById('promoteName').textContent = name;
    document.getElementById('promoteForm').action = `/{{ app('school')->slug }}/students/${id}/promote`;
    new bootstrap.Modal(document.getElementById('promoteModal')).show();
}
</script>
@endpush
@endsection
