@extends('school.layouts.app')
@section('title', 'Staff')
@section('breadcrumb')
    <li class="breadcrumb-item active">Staff</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Staff</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('school.staff.salaries', $school->slug) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-wallet2 me-1"></i>Salaries
        </a>
        @can('create staff')
        <a href="{{ route('school.staff.create', $school->slug) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Staff
        </a>
        @endcan
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Designation</th><th>Phone</th><th>Salary</th><th>Joining Date</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($staff as $s)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $s->name }}</div>
                            <small class="text-muted">{{ $s->email }}</small>
                        </td>
                        <td class="small">{{ $s->designation }}</td>
                        <td class="small">{{ $s->phone ?? '-' }}</td>
                        <td>Rs. {{ number_format($s->salary, 0) }}</td>
                        <td class="small">{{ $s->joining_date ? \Carbon\Carbon::parse($s->joining_date)->format('d M Y') : '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $s->is_active ? 'success' : 'danger' }}">
                                {{ $s->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @can('update staff')
                                <a href="{{ route('school.staff.edit', [$school->slug, $s]) }}" class="btn btn-xs btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete staff')
                                <form method="POST" action="{{ route('school.staff.destroy', [$school->slug, $s]) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No staff found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($staff->hasPages())
    <div class="card-footer bg-white border-0">{{ $staff->withQueryString()->links() }}</div>
    @endif
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
