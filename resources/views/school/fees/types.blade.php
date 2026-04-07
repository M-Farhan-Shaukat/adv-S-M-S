@extends('school.layouts.app')
@section('title', 'Fee Types')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.fees.structures', $school->slug) }}">Fee Structures</a></li>
    <li class="breadcrumb-item active">Fee Types</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Fee Type
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.fees.types.store', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Type Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Tuition, Admission, Exam" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Add Type</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">All Fee Types</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Created</th></tr></thead>
                    <tbody>
                        @forelse($types as $t)
                        <tr>
                            <td class="fw-semibold">{{ $t->name }}</td>
                            <td class="small text-muted">{{ $t->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center py-4 text-muted">No fee types</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
