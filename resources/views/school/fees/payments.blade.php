@extends('school.layouts.app')
@section('title', 'Payments')
@section('breadcrumb')
    <li class="breadcrumb-item active">Payments</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Payments</h5>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Student</th><th>Amount</th><th>Method</th><th>Transaction ID</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->feeVoucher?->student?->name ?? '-' }}</td>
                        <td class="text-success fw-semibold">Rs. {{ number_format($p->amount, 0) }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($p->method ?? 'cash') }}</span></td>
                        <td class="small text-muted">{{ $p->transaction_id ?? '-' }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($p->paid_at)->format('d M Y, h:i A') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No payments found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-white border-0">{{ $payments->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
