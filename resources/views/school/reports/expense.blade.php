@extends('school.layouts.app')
@section('title', 'Expense Report')
@section('breadcrumb')
    <li class="breadcrumb-item active">Expense Report</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-graph-down-arrow me-2 text-danger"></i>Expense Report</h5>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="period" class="form-select form-select-sm">
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="biannual">Bi-Annual</option>
                    <option value="annual">Annual</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i> Generate</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-danger fw-bold fs-3">Rs. {{ number_format($totalExpense, 0) }}</div>
                <div class="text-muted small">Total Expenses</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 small fw-semibold">By Category</div>
            <div class="card-body p-2">
                @foreach($byCategory as $cat)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">{{ $cat['name'] }}</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger">Rs. {{ number_format($cat['total'], 0) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">Expense Details</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($expenses as $e)
                    <tr>
                        <td class="fw-semibold">{{ $e->title }}</td>
                        <td class="small">{{ $e->category?->name ?? '-' }}</td>
                        <td class="text-danger fw-semibold">Rs. {{ number_format($e->amount, 0) }}</td>
                        <td class="small text-muted">{{ \Carbon\Carbon::parse($e->date)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No expenses in this period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
