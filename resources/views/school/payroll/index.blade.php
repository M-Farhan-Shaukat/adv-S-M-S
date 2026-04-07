@extends('school.layouts.app')
@section('title', 'Teacher Payroll')
@section('breadcrumb')
    <li class="breadcrumb-item active">Payroll</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2"></i>Teacher Payroll</h5>
</div>

<!-- Generate Form -->
@can('generate payroll')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="POST" action="{{ route('school.payroll.generate', $school->slug) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-gear me-1"></i>Generate Payroll
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- Filter -->
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

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Teacher</th>
                        <th>Total Minutes</th>
                        <th>Required</th>
                        <th>Short</th>
                        <th>Gross Salary</th>
                        <th>Deduction</th>
                        <th class="text-success">Net Salary</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->teacher?->name }}</td>
                        <td>{{ $p->total_minutes }} min</td>
                        <td>{{ $p->required_minutes }} min</td>
                        <td class="{{ $p->short_minutes > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $p->short_minutes }} min
                        </td>
                        <td>Rs. {{ number_format($p->gross_salary, 0) }}</td>
                        <td class="text-danger">Rs. {{ number_format($p->deduction, 0) }}</td>
                        <td class="text-success fw-bold">Rs. {{ number_format($p->net_salary, 0) }}</td>
                        <td>
                            <a href="{{ route('school.payroll.show', [$school->slug, $p]) }}" class="btn btn-xs btn-outline-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No payroll generated for this period</td></tr>
                    @endforelse
                </tbody>
                @if($payrolls->count())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4">Total</td>
                        <td>Rs. {{ number_format($payrolls->sum('gross_salary'), 0) }}</td>
                        <td class="text-danger">Rs. {{ number_format($payrolls->sum('deduction'), 0) }}</td>
                        <td class="text-success">Rs. {{ number_format($payrolls->sum('net_salary'), 0) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @if($payrolls->hasPages())
    <div class="card-footer bg-white border-0">{{ $payrolls->links() }}</div>
    @endif
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
