@extends('school.layouts.app')
@section('title', 'Student Profile')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.students.index', $school->slug) }}">Students</a></li>
    <li class="breadcrumb-item active">{{ $student->name }}</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                    <i class="bi bi-person fs-1 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
                @if($student->currentEnrollment)
                <div class="text-muted small mb-2">
                    {{ $student->currentEnrollment->class?->name }} - {{ $student->currentEnrollment->section?->name }}
                    @if($student->currentEnrollment->is_class_monitor)
                        <span class="badge bg-warning text-dark ms-1"><i class="bi bi-star-fill"></i> Monitor</span>
                    @endif
                </div>
                @endif
                <span class="badge bg-{{ $student->is_active ? 'success' : 'danger' }}">
                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-0 fw-semibold small">Quick Actions</div>
            <div class="card-body d-grid gap-2">
                @can('update student')
                <a href="{{ route('school.students.edit', [$school->slug, $student]) }}" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Edit Student
                </a>
                @endcan
                @can('promote student')
                <button class="btn btn-outline-success btn-sm" onclick="openPromote({{ $student->id }}, '{{ $student->name }}')">
                    <i class="bi bi-arrow-up-circle me-1"></i>Promote
                </button>
                @endcan
                @can('assign class monitor')
                <form method="POST" action="{{ route('school.students.set-monitor', [$school->slug, $student]) }}">
                    @csrf
                    <button class="btn btn-outline-warning btn-sm w-100">
                        <i class="bi bi-star me-1"></i>Set as Class Monitor
                    </button>
                </form>
                @endcan
                @can('update student')
                <form method="POST" action="{{ route('school.students.toggle-status', [$school->slug, $student]) }}">
                    @csrf
                    <button class="btn btn-outline-{{ $student->is_active ? 'danger' : 'success' }} btn-sm w-100">
                        <i class="bi bi-{{ $student->is_active ? 'x-circle' : 'check-circle' }} me-1"></i>
                        {{ $student->is_active ? 'Disable' : 'Enable' }}
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 fw-semibold">Personal Information</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-6"><span class="text-muted small">Roll Number</span><div>{{ $student->roll_number ?? '-' }}</div></div>
                    <div class="col-md-6"><span class="text-muted small">Email</span><div>{{ $student->email ?? '-' }}</div></div>
                    <div class="col-md-6"><span class="text-muted small">Phone</span><div>{{ $student->phone ?? '-' }}</div></div>
                    <div class="col-md-6"><span class="text-muted small">Date of Birth</span><div>{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : '-' }}</div></div>
                    <div class="col-md-6"><span class="text-muted small">Gender</span><div>{{ ucfirst($student->gender ?? '-') }}</div></div>
                    <div class="col-12"><span class="text-muted small">Address</span><div>{{ $student->address ?? '-' }}</div></div>
                    <div class="col-md-6"><span class="text-muted small">Guardian Name</span><div>{{ $student->guardian_name ?? '-' }}</div></div>
                    <div class="col-md-6"><span class="text-muted small">Guardian Phone</span><div>{{ $student->guardian_phone ?? '-' }}</div></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">Enrollment History</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Session</th><th>Class</th><th>Section</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($student->enrollments->sortByDesc('id') as $e)
                        <tr>
                            <td class="small">{{ $e->session?->name }}</td>
                            <td>{{ $e->class?->name }}</td>
                            <td>{{ $e->section?->name }}</td>
                            <td>
                                @if($e->is_current)
                                    <span class="badge bg-success">Current</span>
                                @else
                                    <span class="badge bg-secondary">Past</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Promote Modal -->
<div class="modal fade" id="promoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Promote Student</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="promoteForm">
                @csrf
                <div class="modal-body">
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
                            @foreach(\App\Models\SchoolClass::where('school_id', app('school')->id)->get() as $c)
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

@push('scripts')
<script>
function openPromote(id, name) {
    document.getElementById('promoteForm').action = `/{{ app('school')->slug }}/students/${id}/promote`;
    new bootstrap.Modal(document.getElementById('promoteModal')).show();
}
</script>
@endpush
@endsection
