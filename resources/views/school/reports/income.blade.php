@extends('school.layouts.app')
@section('title', 'Income Report')
@section('breadcrumb')
    <li class="breadcrumb-item active">Income Report</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Income Report</h5>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Period</label>
                <select name="period" class="form-select form-select-sm">
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="biannual">Bi-Annual</option>
                    <option value="annual">Annual</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i> Generate</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-success fw-bold fs-3">Rs. {{ number_format($totalIncome, 0) }}</div>
                <div class="text-muted small">Total Income ({{ \Carbon\Carbon::parse($from)->format('d M') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }})</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-primary fw-bold fs-3">{{ $payments->count() }}</div>
                <div class="text-muted small">Total Transactions</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-info fw-bold fs-3">Rs. {{ $payments->count() > 0 ? number_format($totalIncome / $payments->count(), 0) : 0 }}</div>
                <div class="text-muted small">Average per Transaction</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">Payment Details</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Student</th><th>Amount</th><th>Method</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr>
                        <td>{{ $p->feeVoucher?->student?->name ?? '-' }}</td>
                        <td class="text-success fw-semibold">Rs. {{ number_format($p->amount, 0) }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($p->method ?? 'cash') }}</span></td>
                        <td class="small text-muted">{{ \Carbon\Carbon::parse($p->paid_at)->format('d M Y, h:i A') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No payments in this period</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
