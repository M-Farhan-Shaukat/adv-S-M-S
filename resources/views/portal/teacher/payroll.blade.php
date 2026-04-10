@extends('portal.teacher.layouts.app')
@section('title', 'My Payroll')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-cash-stack me-2 text-success"></i>My Payroll History
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Month</th><th>Working Min</th><th>Required</th><th>Short</th><th>Gross</th><th>Deduction</th><th class="text-success">Net Salary</th></tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $p)
                    <tr>
                        <td class="fw-semibold">{{ date('F', mktime(0,0,0,$p->month,1)) }} {{ $p->year }}</td>
                        <td class="small">{{ $p->total_minutes }} min</td>
                        <td class="small">{{ $p->required_minutes }} min</td>
                        <td class="small {{ $p->short_minutes > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $p->short_minutes }} min
                        </td>
                        <td class="small">Rs. {{ number_format($p->gross_salary, 0) }}</td>
                        <td class="small text-danger">Rs. {{ number_format($p->deduction, 0) }}</td>
                        <td class="fw-bold text-success">Rs. {{ number_format($p->net_salary, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No payroll records yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payrolls->hasPages())
    <div class="card-footer bg-white border-0">{{ $payrolls->links() }}</div>
    @endif
</div>
@endsection
