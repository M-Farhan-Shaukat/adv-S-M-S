@extends('school.layouts.app')
@section('title', 'Fee Vouchers')
@section('breadcrumb')
    <li class="breadcrumb-item active">Fee Vouchers</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Fee Vouchers</h5>
</div>

<!-- Generate Vouchers -->
@can('generate fee voucher')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 small fw-semibold text-muted">Generate Vouchers</div>
    <div class="card-body py-2">
        <form method="POST" action="{{ route('school.fees.vouchers.generate', $school->slug) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Year</label>
                <input type="number" name="year" class="form-control form-control-sm" value="{{ now()->year }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Due Date</label>
                <input type="date" name="due_date" class="form-control form-control-sm" value="{{ now()->addDays(10)->toDateString() }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Class (optional)</label>
                <select name="school_class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach(\App\Models\SchoolClass::where('school_id', app('school')->id)->get() as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Generate
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" onclick="document.getElementById('sendForm').submit()">
                    <i class="bi bi-send me-1"></i>Send Emails
                </button>
            </div>
        </form>
        <form id="sendForm" method="POST" action="{{ route('school.fees.vouchers.send', $school->slug) }}" class="d-none">
            @csrf
            <input type="hidden" name="month" value="{{ now()->month }}">
            <input type="hidden" name="year" value="{{ now()->year }}">
        </form>
    </div>
</div>
@endcan

<!-- Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
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
                    <tr>
                        <th>Student</th>
                        <th>Month/Year</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $v)
                    <tr>
                        <td class="fw-semibold">{{ $v->student?->name }}</td>
                        <td class="small">{{ date('F', mktime(0,0,0,$v->month,1)) }} {{ $v->year }}</td>
                        <td>Rs. {{ number_format($v->total_amount, 0) }}</td>
                        <td class="text-success">Rs. {{ number_format($v->paid_amount, 0) }}</td>
                        <td class="{{ ($v->total_amount - $v->paid_amount) > 0 ? 'text-danger' : 'text-success' }}">
                            Rs. {{ number_format($v->total_amount - $v->paid_amount, 0) }}
                        </td>
                        <td class="small {{ \Carbon\Carbon::parse($v->due_date)->isPast() && $v->status !== 'paid' ? 'text-danger fw-semibold' : '' }}">
                            {{ \Carbon\Carbon::parse($v->due_date)->format('d M Y') }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $v->status === 'paid' ? 'success' : ($v->status === 'partial' ? 'warning' : 'danger') }}">
                                {{ ucfirst($v->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('school.fees.vouchers.show', [$school->slug, $v]) }}" class="btn btn-xs btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($v->status !== 'paid')
                                @can('collect fee')
                                <button class="btn btn-xs btn-outline-success" onclick="openPayment({{ $v->id }}, {{ $v->total_amount - $v->paid_amount }})">
                                    <i class="bi bi-cash"></i> Pay
                                </button>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No vouchers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vouchers->hasPages())
    <div class="card-footer bg-white border-0">{{ $vouchers->withQueryString()->links() }}</div>
    @endif
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-cash me-2"></i>Collect Payment</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('school.fees.payments.store', $school->slug) }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="fee_voucher_id" id="payVoucherId">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Amount (Remaining: <span id="payRemaining"></span>)</label>
                        <input type="number" name="amount" id="payAmount" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Method</label>
                        <select name="method" class="form-select form-select-sm" required>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="jazzcash">JazzCash</option>
                            <option value="easypaisa">EasyPaisa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>

@push('scripts')
<script>
function openPayment(id, remaining) {
    document.getElementById('payVoucherId').value = id;
    document.getElementById('payRemaining').textContent = 'Rs. ' + remaining.toLocaleString();
    document.getElementById('payAmount').value = remaining;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}
</script>
@endpush
@endsection
