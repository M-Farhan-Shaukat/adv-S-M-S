@extends('portal.layouts.app')
@section('title', 'Fee Vouchers')
@section('portal-name', 'Parent Portal')

@section('sidebar-links')
@php $slug = app('school')->slug; @endphp
<a href="{{ route('parent.dashboard', $slug) }}" class="portal-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
<a href="{{ route('parent.results', $slug) }}" class="portal-nav-link"><i class="bi bi-award"></i> Results</a>
<a href="{{ route('parent.fees', $slug) }}" class="portal-nav-link active"><i class="bi bi-receipt"></i> Fee Vouchers</a>
<a href="{{ route('parent.exam-schedule', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar3"></i> Exam Schedule</a>
<a href="{{ route('parent.complaints', $slug) }}" class="portal-nav-link"><i class="bi bi-chat-left-text"></i> Complaints</a>
<a href="{{ route('parent.meetings', $slug) }}" class="portal-nav-link"><i class="bi bi-calendar-event"></i> Meetings</a>
@endsection

@section('content')
@if($children->count() > 1)
<div class="mb-3">
    <form method="GET" class="d-flex gap-2 align-items-center">
        <label class="small fw-semibold">Child:</label>
        <select name="student_id" class="form-select form-select-sm" style="width:200px" onchange="this.form.submit()">
            @foreach($children as $child)
                <option value="{{ $child->id }}" {{ $studentId == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
            @endforeach
        </select>
    </form>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-receipt me-2 text-primary"></i>Fee Vouchers
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Month</th><th>Total</th><th>Paid</th><th>Remaining</th><th>Due Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $v)
                    <tr>
                        <td class="fw-semibold">{{ date('F', mktime(0,0,0,$v->month,1)) }} {{ $v->year }}</td>
                        <td>Rs. {{ number_format($v->total_amount, 0) }}</td>
                        <td class="text-success">Rs. {{ number_format($v->paid_amount, 0) }}</td>
                        <td class="{{ ($v->total_amount - $v->paid_amount) > 0 ? 'text-danger fw-semibold' : 'text-success' }}">
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
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No fee vouchers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($vouchers->hasPages())
    <div class="card-footer bg-white border-0">{{ $vouchers->links() }}</div>
    @endif
</div>
@endsection
