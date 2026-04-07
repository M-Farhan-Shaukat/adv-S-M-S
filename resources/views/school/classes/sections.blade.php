@extends('school.layouts.app')
@section('title', 'Sections - ' . $class->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.classes.index', $school->slug) }}">Classes</a></li>
    <li class="breadcrumb-item active">{{ $class->name }} - Sections</li>
@endsection

@section('content')
<div class="row g-3">
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
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. A, B, C" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>Add Section
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">Sections in {{ $class->name }}</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Section Name</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($sections as $section)
                        <tr>
                            <td class="fw-semibold">{{ $section->name }}</td>
                            <td>
                                @can('delete section')
                                <form method="POST" action="{{ route('school.classes.sections.destroy', [$school->slug, $section]) }}" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
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
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
