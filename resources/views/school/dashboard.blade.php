@extends('school.layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                    <i class="bi bi-people fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['students'] }}</div>
                    <div class="text-muted small">Students</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10">
                    <i class="bi bi-person-badge fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['teachers'] }}</div>
                    <div class="text-muted small">Teachers</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                    <i class="bi bi-receipt fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_fees'] }}</div>
                    <div class="text-muted small">Pending Fees</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                    <i class="bi bi-chat-left-text fs-4 text-danger"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats['open_complaints'] }}</div>
                    <div class="text-muted small">Open Complaints</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-success fw-bold fs-3">Rs. {{ number_format($stats['monthly_income'], 0) }}</div>
                <div class="text-muted small">This Month Income</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-danger fw-bold fs-3">Rs. {{ number_format($stats['monthly_expense'], 0) }}</div>
                <div class="text-muted small">This Month Expense</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                @php $profit = $stats['monthly_income'] - $stats['monthly_expense']; @endphp
                <div class="fw-bold fs-3 {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                    Rs. {{ number_format(abs($profit), 0) }}
                </div>
                <div class="text-muted small">{{ $profit >= 0 ? 'Net Profit' : 'Net Loss' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-credit-card me-2 text-primary"></i>Recent Payments
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Student</th><th>Amount</th><th>Date</th></tr></thead>
                        <tbody>
                            @forelse($recentPayments as $p)
                            <tr>
                                <td>{{ $p->feeVoucher?->student?->name ?? '-' }}</td>
                                <td class="text-success fw-semibold">Rs. {{ number_format($p->amount, 0) }}</td>
                                <td class="text-muted small">{{ \Carbon\Carbon::parse($p->paid_at)->format('d M') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No payments yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-chat-left-text me-2 text-danger"></i>Recent Complaints
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Subject</th><th>By</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($recentComplaints as $c)
                            <tr>
                                <td>{{ Str::limit($c->subject, 30) }}</td>
                                <td class="small">{{ $c->user?->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $c->status === 'resolved' ? 'success' : ($c->status === 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($c->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No complaints</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
