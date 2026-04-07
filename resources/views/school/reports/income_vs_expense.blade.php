@extends('school.layouts.app')
@section('title', 'Income vs Expense Report')
@section('breadcrumb')
    <li class="breadcrumb-item active">P&L Report</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line me-2"></i>Income vs Expense Report</h5>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Period</label>
                <select name="period" class="form-select form-select-sm" onchange="toggleCustom(this.value)">
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="biannual">Bi-Annual</option>
                    <option value="annual">Annual</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
            <div class="col-md-2 custom-dates">
                <label class="form-label small fw-semibold mb-1">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-2 custom-dates">
                <label class="form-label small fw-semibold mb-1">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i> Generate</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-success fw-bold fs-3">Rs. {{ number_format($income, 0) }}</div>
                <div class="text-muted small">Total Income</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="text-danger fw-bold fs-3">Rs. {{ number_format($expense, 0) }}</div>
                <div class="text-muted small">Total Expense</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="fw-bold fs-3 {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                    Rs. {{ number_format(abs($profit), 0) }}
                </div>
                <div class="text-muted small">{{ $profit >= 0 ? 'Net Profit' : 'Net Loss' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Breakdown -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">Monthly Breakdown</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th class="text-success">Income</th>
                        <th class="text-danger">Expense</th>
                        <th>Net</th>
                        <th>Visual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($months as $m)
                    @php $net = $m['income'] - $m['expense']; @endphp
                    <tr>
                        <td class="fw-semibold">{{ $m['label'] }}</td>
                        <td class="text-success">Rs. {{ number_format($m['income'], 0) }}</td>
                        <td class="text-danger">Rs. {{ number_format($m['expense'], 0) }}</td>
                        <td class="{{ $net >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                            Rs. {{ number_format(abs($net), 0) }}
                        </td>
                        <td style="width:200px">
                            @php $maxVal = max(array_column($months, 'income') + array_column($months, 'expense')); @endphp
                            <div class="d-flex gap-1 align-items-center">
                                <div class="bg-success rounded" style="height:8px;width:{{ $maxVal > 0 ? ($m['income']/$maxVal)*100 : 0 }}%;min-width:2px"></div>
                                <div class="bg-danger rounded" style="height:8px;width:{{ $maxVal > 0 ? ($m['expense']/$maxVal)*100 : 0 }}%;min-width:2px"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
