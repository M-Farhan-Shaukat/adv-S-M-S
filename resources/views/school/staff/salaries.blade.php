@extends('school.layouts.app')
@section('title', 'Staff Salaries')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('school.staff.index', $school->slug) }}">Staff</a></li>
    <li class="breadcrumb-item active">Salaries</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-wallet2 me-2"></i>Staff Salaries</h5>
</div>

<!-- Generate -->
@can('manage salary structure')
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="POST" action="{{ route('school.staff.salaries.generate', $school->slug) }}" class="row g-2 align-items-end">
            @csrf
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
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-gear me-1"></i>Generate Salaries
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Staff</th><th>Designation</th><th>Basic</th><th>Allowances</th><th>Deductions</th><th class="text-success">Net</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($salaries as $sal)
                    <tr>
                        <td class="fw-semibold">{{ $sal->staff?->name }}</td>
                        <td class="small">{{ $sal->staff?->designation }}</td>
                        <td>Rs. {{ number_format($sal->basic_salary, 0) }}</td>
                        <td class="text-success">Rs. {{ number_format($sal->allowances, 0) }}</td>
                        <td class="text-danger">Rs. {{ number_format($sal->deductions, 0) }}</td>
                        <td class="text-success fw-bold">Rs. {{ number_format($sal->net_salary, 0) }}</td>
                        <td>
                            <span class="badge bg-{{ $sal->status === 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($sal->status) }}
                            </span>
                        </td>
                        <td>
                            @if($sal->status === 'pending')
                            @can('manage salary structure')
                            <form method="POST" action="{{ route('school.staff.salaries.paid', [$school->slug, $sal]) }}">
                                @csrf
                                <button class="btn btn-xs btn-outline-success">
                                    <i class="bi bi-check-circle"></i> Mark Paid
                                </button>
                            </form>
                            @endcan
                            @else
                            <small class="text-muted">{{ $sal->paid_date ? \Carbon\Carbon::parse($sal->paid_date)->format('d M Y') : '' }}</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No salaries generated for this period</td></tr>
                    @endforelse
                </tbody>
                @if($salaries->count())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5">Total</td>
                        <td class="text-success">Rs. {{ number_format($salaries->sum('net_salary'), 0) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
