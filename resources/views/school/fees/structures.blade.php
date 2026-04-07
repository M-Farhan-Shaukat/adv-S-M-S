@extends('school.layouts.app')
@section('title', 'Fee Structures')
@section('breadcrumb')
    <li class="breadcrumb-item active">Fee Structures</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Fee Structure
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.fees.structures.store', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Class *</label>
                        <select name="school_class_id" class="form-select form-select-sm" required>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fee Type *</label>
                        <select name="fee_type_id" class="form-select form-select-sm" required>
                            @foreach($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Tuition Fee" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Amount (Rs.) *</label>
                        <input type="number" name="amount" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Add Structure</button>
                </form>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('school.fees.types', $school->slug) }}" class="btn btn-outline-secondary w-100 btn-sm">
                <i class="bi bi-tags me-1"></i>Manage Fee Types
            </a>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">All Fee Structures</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Class</th><th>Fee Type</th><th>Name</th><th>Amount</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse($structures as $s)
                            <tr>
                                <td class="fw-semibold">{{ $s->schoolClass?->name }}</td>
                                <td><span class="badge bg-info bg-opacity-10 text-info">{{ $s->feeType?->name }}</span></td>
                                <td>{{ $s->name }}</td>
                                <td class="text-success fw-semibold">Rs. {{ number_format($s->amount, 0) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('school.fees.structures.destroy', [$school->slug, $s]) }}" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No fee structures</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
