@extends('admin.layouts.app')
@section('title', 'Manage Schools')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i>Schools</h5>
    <a href="{{ route('admin.schools.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add School
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>School Name</th><th>Slug (URL)</th><th>Email</th><th>Classes</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $school->name }}</div>
                            <small class="text-muted">{{ $school->phone }}</small>
                        </td>
                        <td><code class="small">{{ $school->slug }}</code></td>
                        <td class="small text-muted">{{ $school->email ?? '-' }}</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $school->classes_count }}</span></td>
                        <td>
                            <span class="badge bg-{{ $school->is_active ? 'success' : 'danger' }}">
                                {{ $school->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ url($school->slug . '/dashboard') }}" class="btn btn-xs btn-outline-primary" title="Open School">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <a href="{{ route('admin.schools.edit', $school) }}" class="btn btn-xs btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.schools.toggle', $school) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-outline-{{ $school->is_active ? 'danger' : 'success' }}">
                                        <i class="bi bi-{{ $school->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No schools yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($schools->hasPages())
    <div class="card-footer bg-white border-0">{{ $schools->links() }}</div>
    @endif
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
