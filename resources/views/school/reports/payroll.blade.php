@extends('school.layouts.app')
@section('title', 'Payroll Report')
@section('breadcrumb')
    <li class="breadcrumb-item active">Payroll Report</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2"></i>Payroll Report</h5>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="month" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i> View</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-primary fw-bold fs-3">Rs. {{ number_format($totalTeacher, 0) }}</div>
                <div class="text-muted small">Teacher Payroll</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-success fw-bold fs-3">Rs. {{ number_format($totalStaff, 0) }}</div>
                <div class="text-muted small">Staff Salaries</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-danger fw-bold fs-3">Rs. {{ number_format($totalTeacher + $totalStaff, 0) }}</div>
                <div class="text-muted small">Total Payroll</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 fw-semibold">Teacher Payroll</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Teacher</th><th>Gross</th><th>Deduction</th><th class="text-success">Net</th></tr></thead>
                <tbody>
                    @forelse($teacherPayrolls as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->teacher?->name }}</td>
                        <td>Rs. {{ number_format($p->gross_salary, 0) }}</td>
                        <td class="text-danger">Rs. {{ number_format($p->deduction, 0) }}</td>
                        <td class="text-success fw-bold">Rs. {{ number_format($p->net_salary, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-3 text-muted">No payroll data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">Staff Salaries</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Staff</th><th>Designation</th><th>Basic</th><th class="text-success">Net</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($staffSalaries as $s)
                    <tr>
                        <td class="fw-semibold">{{ $s->staff?->name }}</td>
                        <td class="small">{{ $s->staff?->designation }}</td>
                        <td>Rs. {{ number_format($s->basic_salary, 0) }}</td>
                        <td class="text-success fw-bold">Rs. {{ number_format($s->net_salary, 0) }}</td>
                        <td><span class="badge bg-{{ $s->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($s->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-3 text-muted">No salary data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
