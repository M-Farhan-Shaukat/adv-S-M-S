@extends('school.layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Welcome Header --}}
<div class="card border-0 shadow-lg mb-4 overflow-hidden">
    <div class="card-body p-4" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-3 d-flex align-items-center justify-content-center shadow"
                         style="width:55px;height:55px;min-width:55px">
                        <i class="bi bi-shield-check fs-3 text-primary"></i>
                    </div>
                    <div class="text-white">
                        <h4 class="fw-bold mb-1">Welcome, {{ auth()->user()->name }}</h4>
                        <p class="mb-0 opacity-75 small">
                            <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, d F Y') }}
                            &nbsp;•&nbsp;
                            <span class="badge bg-warning text-dark">
                                {{ strtoupper(auth()->user()->getRoleNames()->first() ?? 'Admin') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="text-white opacity-75 small">
                    <i class="bi bi-building me-1"></i>{{ $totalSchools }} School(s) Active
                </div>
                <div class="text-white small mt-1">
                    <i class="bi bi-clock me-1"></i>{{ now()->format('h:i A') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 flex-shrink-0">
                    <i class="bi bi-building fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ $totalSchools }}</div>
                    <div class="text-muted small mt-1">Schools</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 flex-shrink-0">
                    <i class="bi bi-people fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ $totalStudents }}</div>
                    <div class="text-muted small mt-1">Students</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-info bg-opacity-10 flex-shrink-0">
                    <i class="bi bi-person-badge fs-4 text-info"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ $totalTeachers }}</div>
                    <div class="text-muted small mt-1">Teachers</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning bg-opacity-10 flex-shrink-0">
                    <i class="bi bi-person-lines-fill fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold lh-1">{{ $totalStaff }}</div>
                    <div class="text-muted small mt-1">Staff</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Financial Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <div class="text-success fw-bold fs-4">Rs. {{ number_format($monthlyIncome, 0) }}</div>
                <div class="text-muted small">This Month Income</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <div class="text-danger fw-bold fs-4">Rs. {{ number_format($monthlyExpense, 0) }}</div>
                <div class="text-muted small">This Month Expense</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                @php $profit = $monthlyIncome - $monthlyExpense; @endphp
                <div class="fw-bold fs-4 {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                    Rs. {{ number_format(abs($profit), 0) }}
                </div>
                <div class="text-muted small">{{ $profit >= 0 ? 'Net Profit' : 'Net Loss' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <div class="text-warning fw-bold fs-4">{{ $pendingFees }}</div>
                <div class="text-muted small">Pending Fee Vouchers</div>
            </div>
        </div>
    </div>
</div>

{{-- Income vs Expense Chart + Attendance --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-bar-chart-line me-2 text-primary"></i>Income vs Expense (Last 6 Months)
            </div>
            <div class="card-body">
                <canvas id="incomeChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 fw-semibold small">
                <i class="bi bi-calendar-check me-2 text-success"></i>Today's Teacher Attendance
            </div>
            <div class="card-body">
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <div class="fs-3 fw-bold text-success">{{ $todayPresent }}</div>
                            <div class="small text-muted">Present</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                            <div class="fs-3 fw-bold text-danger">{{ $todayAbsent }}</div>
                            <div class="small text-muted">Absent</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-semibold small">
                <i class="bi bi-chat-left-text me-2 text-danger"></i>Complaints
            </div>
            <div class="card-body text-center py-3">
                <div class="fs-2 fw-bold text-danger">{{ $openComplaints }}</div>
                <div class="text-muted small">Open / Pending</div>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-danger mt-2">View All</a>
            </div>
        </div>
    </div>
</div>

{{-- Users by Role + Recent Payments --}}
<div class="row g-3 mb-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-pie-chart me-2 text-info"></i>Users by Role
            </div>
            <div class="card-body">
                @forelse($usersByRole as $role => $count)
                @php
                    $total = array_sum($usersByRole);
                    $pct   = $total > 0 ? round(($count / $total) * 100) : 0;
                    $colors = ['admin'=>'danger','principal'=>'primary','teacher'=>'success','staff'=>'warning','student'=>'info','parent'=>'secondary'];
                    $color  = $colors[strtolower($role)] ?? 'secondary';
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold text-capitalize">{{ $role }}</span>
                        <span class="small text-muted">{{ $count }} ({{ $pct }}%)</span>
                    </div>
                    <div class="progress" style="height:6px">
                        <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted small text-center py-3">No users yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-credit-card me-2 text-success"></i>Recent Payments
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Student</th><th>Amount</th><th>Method</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $p)
                            <tr>
                                <td class="fw-semibold small">{{ $p->feeVoucher?->student?->name ?? '-' }}</td>
                                <td class="text-success fw-semibold small">Rs. {{ number_format($p->amount, 0) }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ ucfirst($p->method ?? 'cash') }}</span></td>
                                <td class="text-muted small">{{ \Carbon\Carbon::parse($p->paid_at)->format('d M') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted small">No payments yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Complaints + Upcoming Meetings --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-chat-left-text me-2 text-danger"></i>Recent Complaints
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Subject</th><th>By</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentComplaints as $c)
                            <tr>
                                <td class="small fw-semibold">{{ \Illuminate\Support\Str::limit($c->subject, 30) }}</td>
                                <td class="small text-muted">{{ $c->user?->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $c->status === 'resolved' ? 'success' : ($c->status === 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($c->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-3 text-muted small">No complaints</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 fw-semibold">
                <i class="bi bi-calendar-event me-2 text-primary"></i>Upcoming Meetings
            </div>
            <div class="card-body p-0">
                @forelse($upcomingMeetings as $m)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="bg-primary bg-opacity-10 rounded-3 text-center p-2 flex-shrink-0" style="min-width:48px">
                        <div class="fw-bold text-primary lh-1">{{ \Carbon\Carbon::parse($m->meeting_date)->format('d') }}</div>
                        <div class="text-primary" style="font-size:0.65rem">{{ \Carbon\Carbon::parse($m->meeting_date)->format('M') }}</div>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold small text-truncate">{{ $m->title }}</div>
                        <div class="text-muted" style="font-size:0.75rem">
                            <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($m->meeting_date)->format('h:i A') }}
                            @if($m->venue) &nbsp;•&nbsp; <i class="bi bi-geo-alt me-1"></i>{{ $m->venue }} @endif
                        </div>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info flex-shrink-0" style="font-size:0.65rem">
                        {{ str_replace('_', ' ', ucfirst($m->type)) }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">No upcoming meetings</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Schools Overview --}}
@if($schools->count())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-building me-2 text-primary"></i>Schools Overview
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>School Name</th><th>Slug (URL)</th><th>Classes</th><th>Sessions</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @foreach($schools as $school)
                    <tr>
                        <td class="fw-semibold">{{ $school->name }}</td>
                        <td><code class="small">{{ $school->slug }}</code></td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $school->classes_count }}</span></td>
                        <td><span class="badge bg-info bg-opacity-10 text-info">{{ $school->sessions_count }}</span></td>
                        <td>
                            <span class="badge bg-{{ $school->is_active ? 'success' : 'danger' }}">
                                {{ $school->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ url($school->slug . '/dashboard') }}" class="btn btn-xs btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right"></i> Open
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Recent Users --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-people me-2 text-info"></i>Recent Users</span>
        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-primary"
                                     style="width:32px;height:32px;font-size:0.8rem">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="fw-semibold small">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="small text-muted">{{ $user->email }}</td>
                        <td>
                            @foreach($user->getRoleNames() as $role)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $role }}</span>
                            @endforeach
                        </td>
                        <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-3 text-muted">No users found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(array_column($incomeChart, 'label')),
            datasets: [
                {
                    label: 'Income',
                    data: @json(array_column($incomeChart, 'income')),
                    backgroundColor: 'rgba(34,197,94,0.7)',
                    borderRadius: 6,
                },
                {
                    label: 'Expense',
                    data: @json(array_column($incomeChart, 'expense')),
                    backgroundColor: 'rgba(239,68,68,0.7)',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rs. ' + ctx.parsed.y.toLocaleString()
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => 'Rs. ' + val.toLocaleString()
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
