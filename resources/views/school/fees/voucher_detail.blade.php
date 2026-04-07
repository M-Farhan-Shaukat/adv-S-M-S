@extends('school.layouts.app')
@section('title', 'Voucher Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.fees.vouchers', app('school')->slug) }}">Vouchers</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Fee Voucher</span>
        <span class="badge bg-{{ $feeVoucher->status === 'paid' ? 'success' : ($feeVoucher->status === 'partial' ? 'warning' : 'danger') }} fs-6">
            {{ ucfirst($feeVoucher->status) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="text-muted small">Student</div>
                <div class="fw-bold">{{ $feeVoucher->student?->name }}</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Period</div>
                <div class="fw-bold">{{ date('F', mktime(0,0,0,$feeVoucher->month,1)) }} {{ $feeVoucher->year }}</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Due Date</div>
                <div class="fw-bold {{ \Carbon\Carbon::parse($feeVoucher->due_date)->isPast() && $feeVoucher->status !== 'paid' ? 'text-danger' : '' }}">
                    {{ \Carbon\Carbon::parse($feeVoucher->due_date)->format('d M Y') }}
                </div>
            </div>
        </div>

        <table class="table table-sm">
            <thead class="table-light"><tr><th>Description</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
                @foreach($feeVoucher->items as $item)
                <tr>
                    <td>{{ $item->title }}</td>
                    <td class="text-end">Rs. {{ number_format($item->total_amount, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td>Total</td>
                    <td class="text-end">Rs. {{ number_format($feeVoucher->total_amount, 0) }}</td>
                </tr>
                <tr class="text-success">
                    <td>Paid</td>
                    <td class="text-end">Rs. {{ number_format($feeVoucher->paid_amount, 0) }}</td>
                </tr>
                <tr class="fw-bold text-danger">
                    <td>Remaining</td>
                    <td class="text-end">Rs. {{ number_format($feeVoucher->total_amount - $feeVoucher->paid_amount, 0) }}</td>
                </tr>
            </tfoot>
        </table>

        @if($feeVoucher->payments->count())
        <h6 class="mt-3 fw-semibold">Payment History</h6>
        <table class="table table-sm">
            <thead class="table-light"><tr><th>Amount</th><th>Method</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($feeVoucher->payments as $p)
                <tr>
                    <td class="text-success fw-semibold">Rs. {{ number_format($p->amount, 0) }}</td>
                    <td>{{ ucfirst($p->method ?? 'cash') }}</td>
                    <td class="small">{{ \Carbon\Carbon::parse($p->paid_at)->format('d M Y, h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
