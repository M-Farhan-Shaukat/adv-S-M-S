@extends('school.layouts.app')
@section('title', 'Payroll Detail')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.payroll.index', app('school')->slug) }}">Payroll</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-header bg-white border-0 fw-bold">
        <i class="bi bi-cash-stack me-2 text-primary"></i>Payroll Slip
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-6">
                <div class="text-muted small">Teacher</div>
                <div class="fw-bold">{{ $payroll->teacher?->name }}</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Period</div>
                <div class="fw-bold">{{ date('F', mktime(0,0,0,$payroll->month,1)) }} {{ $payroll->year }}</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Total Working Minutes</div>
                <div class="fw-bold">{{ $payroll->total_minutes }} min ({{ intdiv($payroll->total_minutes, 60) }}h {{ $payroll->total_minutes % 60 }}m)</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Required Minutes</div>
                <div class="fw-bold">{{ $payroll->required_minutes }} min</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Short Minutes</div>
                <div class="fw-bold {{ $payroll->short_minutes > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $payroll->short_minutes }} min
                </div>
            </div>
        </div>

        <hr>

        <div class="row g-2">
            <div class="col-6">
                <div class="text-muted small">Gross Salary</div>
                <div class="fw-bold fs-5">Rs. {{ number_format($payroll->gross_salary, 2) }}</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Deduction</div>
                <div class="fw-bold fs-5 text-danger">- Rs. {{ number_format($payroll->deduction, 2) }}</div>
            </div>
            <div class="col-12">
                <hr class="my-2">
                <div class="text-muted small">Net Salary</div>
                <div class="fw-bold fs-3 text-success">Rs. {{ number_format($payroll->net_salary, 2) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
