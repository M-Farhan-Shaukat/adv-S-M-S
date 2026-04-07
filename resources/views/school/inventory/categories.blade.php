@extends('school.layouts.app')
@section('title', 'Inventory Categories')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.inventory.index', $school->slug) }}">Inventory</a></li>
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Add Category
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.inventory.categories.store', $school->slug) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category Name *</label>
                        <input type="text" name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Add Category</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">All Categories</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Name</th><th>Items</th><th>Description</th></tr></thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td class="fw-semibold">{{ $cat->name }}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $cat->items_count }}</span></td>
                            <td class="small text-muted">{{ $cat->description ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">No categories</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
