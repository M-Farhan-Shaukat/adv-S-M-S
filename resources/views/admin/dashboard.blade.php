@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Welcome Header -->
    <div class="dashboard-header mb-3 mb-md-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-body p-3 p-lg-4">
                        <div class="row align-items-center">
                            <div class="col-12 col-lg-8">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-container me-2 me-md-3">
                                        <div class="avatar-circle bg-primary bg-opacity-10 text-primary p-2">
                                            <i class="bi bi-shield-check fs-4 fs-md-3"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h1 class="h5 h4-md h3-lg fw-bold mb-1 text-dark">Welcome, {{ auth()->user()->name }}!</h1>
                                        <p class="text-muted mb-0 small">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ now()->format('l, F j, Y') }}
                                            •
                                            <span class="badge ms-1
                                                @if(auth()->user()->hasRole('Admin')) bg-danger
                                                @elseif(auth()->user()->hasRole('Manager')) bg-warning
                                                @elseif(auth()->user()->hasRole('Staff')) bg-info
                                                @else bg-secondary @endif">
                                                {{ auth()->user()->role->name ?? 'Admin' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 text-lg-end mt-2 mt-lg-0">
                                <div class="stats-card bg-white bg-opacity-25 p-2 rounded-3 d-inline-block">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-white bg-opacity-25 me-2 p-1">
                                            <i class="bi bi-clock text-white small"></i>
                                        </div>
                                        <div class="text-white small">
                                            <small class="d-block opacity-75">System</small>
                                            <strong class="d-block">100%</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-2 g-md-3 g-lg-4 mb-3 mb-md-4">



        <!-- Total Users -->
        <div class="col-6 col-xl-3">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body p-2 p-md-3">
                    <div class="d-flex justify-content-between align-items-start mb-1 mb-md-2">
                        <div class="stat-icon bg-info bg-opacity-10 p-1 p-md-2">
                            <i class="bi bi-people text-info fs-6 fs-md-5"></i>
                        </div>
                        @if(auth()->user()->hasPermission('manage_users'))
                            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-info py-0 px-1 px-md-2">
                                <small>Manage</small>
                            </a>
                        @endif
                    </div>
                    <h3 class="stat-number mb-0 fs-5 fs-md-4 fs-lg-3">{{ $totalUsers }}</h3>
                    <p class="stat-label text-muted mb-0 small">Total Users</p>
                    <div class="mt-1 mt-md-2">
                        <div class="user-avatars d-flex gap-1">
                            @foreach($recentUsers->take(3) as $user)
                                <div class="avatar-sm" data-bs-toggle="tooltip" title="{{ $user->name }}">
                                    <div class="avatar-circle bg-light text-dark border"
                                         style="width: 25px; height: 25px; font-size: 0.7rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>
                            @endforeach
                            @if($totalUsers > 3)
                                <div class="avatar-sm">
                                    <div class="avatar-circle bg-light text-dark border"
                                         style="width: 25px; height: 25px; font-size: 0.7rem;">
                                        +{{ $totalUsers - 3 }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Users by Role Chart -->
    @if(count($usersByRole) > 0)
        <div class="row mt-2 mt-md-3 mt-lg-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-2 pt-md-3 pb-1 pb-md-2 px-2 px-md-3">
                        <h5 class="card-title mb-0 fs-6 fs-md-5">
                            <i class="bi bi-pie-chart text-success me-2"></i>
                            Users by Role
                        </h5>
                    </div>
                    <div class="card-body p-2 p-md-3">
                        <div class="row g-2 g-md-3">
                            @foreach($usersByRole as $roleName => $count)
                                <div class="col-6 col-md-3">
                                    <div class="bg-light rounded-3 p-2 p-md-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-semibold small">{{ $roleName }}</span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                {{ $count }}
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 4px;">
                                            @php
                                                $percentage = $totalUsers > 0 ? ($count / $totalUsers) * 100 : 0;
                                                $colorClass = match($roleName) {
                                                    'Admin' => 'bg-danger',
                                                    'Manager' => 'bg-warning',
                                                    'Staff' => 'bg-info',
                                                    default => 'bg-primary'
                                                };
                                            @endphp
                                            <div class="progress-bar {{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small class="text-muted d-block mt-1 small">{{ round($percentage, 1) }}%</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .dashboard-header .card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
        }
        .stat-card {
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        }
        .avatar-circle {
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 767.98px) {
            .stat-number {
                font-size: 1.25rem;
            }
            .dashboard-header .card {
                border-radius: 8px;
            }
        }
    </style>
@endsection
