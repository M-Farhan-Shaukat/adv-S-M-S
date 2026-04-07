@extends('school.layouts.app')
@section('title', 'Inventory')
@section('breadcrumb')
    <li class="breadcrumb-item active">Inventory</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Inventory</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('school.inventory.categories', $school->slug) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-tags me-1"></i>Categories
        </a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus-lg me-1"></i>Add Item
        </button>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-3">
    @foreach(['available' => ['success', 'check-circle'], 'low_stock' => ['warning', 'exclamation-triangle'], 'out_of_stock' => ['danger', 'x-circle']] as $status => [$color, $icon])
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-{{ $icon }} fs-3 text-{{ $color }}"></i>
                <div class="fw-bold mt-1">{{ $items->where('status', $status)->count() }}</div>
                <div class="text-muted small">{{ str_replace('_', ' ', ucfirst($status)) }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Item</th><th>Category</th><th>Quantity</th><th>Unit Price</th><th>Total Value</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->name }}</div>
                            <small class="text-muted">{{ $item->code }}</small>
                        </td>
                        <td class="small">{{ $item->category?->name }}</td>
                        <td>
                            <span class="{{ $item->quantity <= $item->min_quantity ? 'text-danger fw-bold' : '' }}">
                                {{ $item->quantity }} {{ $item->unit }}
                            </span>
                        </td>
                        <td class="small">Rs. {{ number_format($item->unit_price, 0) }}</td>
                        <td class="small">Rs. {{ number_format($item->quantity * $item->unit_price, 0) }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status === 'available' ? 'success' : ($item->status === 'low_stock' ? 'warning' : 'danger') }}">
                                {{ str_replace('_', ' ', ucfirst($item->status)) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-xs btn-outline-primary" onclick="openTransaction({{ $item->id }}, '{{ $item->name }}')">
                                <i class="bi bi-arrow-left-right"></i> Transaction
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No items found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
    <div class="card-footer bg-white border-0">{{ $items->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Add Inventory Item</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('school.inventory.store', $school->slug) }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Item Name *</label>
                            <input type="text" name="name" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Category *</label>
                            <select name="inventory_category_id" class="form-select form-select-sm" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Quantity *</label>
                            <input type="number" name="quantity" class="form-control form-control-sm" value="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Min Quantity</label>
                            <input type="number" name="min_quantity" class="form-control form-control-sm" value="5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Unit</label>
                            <input type="text" name="unit" class="form-control form-control-sm" placeholder="pcs, kg...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Unit Price (Rs.)</label>
                            <input type="number" name="unit_price" class="form-control form-control-sm" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Code</label>
                            <input type="text" name="code" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold" id="transactionTitle">Transaction</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('school.inventory.transaction', $school->slug) }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="inventory_item_id" id="transItemId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Type *</label>
                            <select name="type" class="form-select form-select-sm" required>
                                <option value="in">Stock In</option>
                                <option value="out">Stock Out</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Quantity *</label>
                            <input type="number" name="quantity" class="form-control form-control-sm" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Date *</label>
                            <input type="date" name="transaction_date" class="form-control form-control-sm" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Unit Price</label>
                            <input type="number" name="unit_price" class="form-control form-control-sm" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>

@push('scripts')
<script>
function openTransaction(id, name) {
    document.getElementById('transItemId').value = id;
    document.getElementById('transactionTitle').textContent = 'Transaction: ' + name;
    new bootstrap.Modal(document.getElementById('transactionModal')).show();
}
</script>
@endpush
@endsection
