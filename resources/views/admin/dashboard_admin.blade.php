@extends('admin.layouts.app')
@section('title', 'System Dashboard')

@section('content')

{{-- Header --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-body p-4" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%);">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-white rounded-3 d-flex align-items-center justify-content-center shadow flex-shrink-0"
                 style="width:50px;height:50px">
                <i class="bi bi-shield-check fs-4 text-primary"></i>
            </div>
            <div class="text-white">
                <h5 class="fw-bold mb-1">System Overview</h5>
                <p class="mb-0 opacity-75 small">
                    {{ now()->format('l, d F Y') }}
                    &nbsp;•&nbsp;
                    <span class="badge bg-warning text-dark">SUPER ADMIN</span>
                    &nbsp;•&nbsp;
                    {{ $activeSchools }}/{{ $totalSchools }} schools active
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Summary Stats --}}
<div class="row g-3 mb-4">
    @php
        $stats = [
            ['label' => 'Total Schools',  'value' => $totalSchools,  'icon' => 'building',         'color' => 'primary'],
            ['label' => 'Active Schools', 'value' => $activeSchools, 'icon' => 'building-check',   'color' => 'success'],
            ['label' => 'Students',       'value' => $totalStudents, 'icon' => 'people',            'color' => 'info'],
            ['label' => 'Teachers',       'value' => $totalTeachers, 'icon' => 'person-badge',      'color' => 'warning'],
            ['label' => 'Staff',          'value' => $totalStaff,    'icon' => 'person-lines-fill', 'color' => 'secondary'],
            ['label' => 'Total Users',    'value' => $totalUsers,    'icon' => 'people-fill',       'color' => 'danger'],
        ];
    @endphp
    @foreach($stats as $s)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-2 p-3">
                <div class="rounded-3 p-2 bg-{{ $s['color'] }} bg-opacity-10 flex-shrink-0">
                    <i class="bi bi-{{ $s['icon'] }} fs-5 text-{{ $s['color'] }}"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold lh-1">{{ number_format($s['value']) }}</div>
                    <div class="text-muted" style="font-size:0.72rem">{{ $s['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Schools Table --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="bi bi-building me-2 text-primary"></i>Schools</span>
        <a href="{{ route('admin.schools.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Add School
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>School</th>
                        <th>Principal</th>
                        <th class="text-center">Students</th>
                        <th class="text-center">Teachers</th>
                        <th class="text-center">Staff</th>
                        <th class="text-center">Users</th>
                        <th class="text-center">Classes</th>
                        <th class="text-center">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $school->name }}</div>
                            <code class="text-muted" style="font-size:0.72rem">{{ $school->slug }}</code>
                        </td>
                        <td class="small text-muted">
                            {{ $school->users->first()?->name ?? '—' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success">{{ $school->students_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info">{{ $school->teachers_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning bg-opacity-10 text-warning">{{ $school->staff_count }}</span>
                        </td>
                        <td class="text-center">
                            {{-- Clickable: shows school users --}}
                            <a href="{{ route('admin.schools.users', $school) }}"
                               class="badge bg-primary bg-opacity-10 text-primary text-decoration-none">
                                {{ $school->users_count }}
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $school->classes_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $school->is_active ? 'success' : 'danger' }}">
                                {{ $school->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.schools.users', $school) }}"
                                   class="btn btn-xs btn-outline-primary" title="View Users">
                                    <i class="bi bi-people"></i>
                                </a>
                                <a href="{{ route('admin.schools.edit', $school) }}"
                                   class="btn btn-xs btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.schools.toggle', $school) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-outline-{{ $school->is_active ? 'danger' : 'success' }}"
                                            title="{{ $school->is_active ? 'Disable' : 'Enable' }}">
                                        <i class="bi bi-{{ $school->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">No schools yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Users by Role --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-semibold">
        <i class="bi bi-pie-chart me-2 text-info"></i>Users by Role (System-wide)
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php $totalU = array_sum($usersByRole); @endphp
            @forelse($usersByRole as $role => $count)
            @php
                $pct = $totalU > 0 ? round(($count / $totalU) * 100) : 0;
                $colors = ['admin'=>'danger','principal'=>'primary','teacher'=>'success','staff'=>'warning','student'=>'info','parent'=>'secondary'];
                $color  = $colors[strtolower($role)] ?? 'secondary';
            @endphp
            <div class="col-md-4 col-6">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small fw-semibold text-capitalize">{{ $role }}</span>
                    <span class="small text-muted">{{ $count }} ({{ $pct }}%)</span>
                </div>
                <div class="progress" style="height:6px">
                    <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted small py-2">No users yet</div>
            @endforelse
        </div>
    </div>
</div>

<style>.btn-xs { padding: 0.2rem 0.4rem; font-size: 0.75rem; }</style>
@endsection
