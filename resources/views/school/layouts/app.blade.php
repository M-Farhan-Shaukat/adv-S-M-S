<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'School Management') - {{ app('school')->name ?? '' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: linear-gradient(180deg, #1e3a5f 0%, #2d6a9f 100%);
            --accent: #2d6a9f;
        }
        body { background: #f0f4f8; }
        .sidebar {
            background: var(--sidebar-bg);
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s ease;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.25); border-radius: 4px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
        .sidebar.collapsed { transform: translateX(-260px); }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            border-radius: 8px;
            padding: 0.6rem 1rem;
            margin-bottom: 2px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sidebar .nav-link i { width: 1.5rem; }
        .sidebar-section {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            padding: 0.75rem 1rem 0.25rem;
        }
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        .main-content.expanded { margin-left: 0; }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .school-badge {
            background: linear-gradient(135deg, #1e3a5f, #2d6a9f);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1039;
            }
            .sidebar-overlay.show { display: block; }
        }
        .dropdown-item:active { background: var(--accent); }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="p-3">
        <!-- School Brand -->
        <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-white border-opacity-25">
            <div class="bg-white rounded-circle p-2 me-2" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-mortarboard-fill text-primary"></i>
            </div>
            <div>
                <div class="text-white fw-bold" style="font-size:0.9rem;line-height:1.2">{{ app('school')->name }}</div>
                <small class="text-white-50" style="font-size:0.7rem">Management System</small>
            </div>
        </div>

        <!-- User Info -->
        <div class="bg-white bg-opacity-10 rounded-3 p-2 mb-3">
            <div class="d-flex align-items-center">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                    <i class="bi bi-person text-primary" style="font-size:1rem"></i>
                </div>
                <div>
                    <div class="text-white fw-semibold" style="font-size:0.8rem">{{ auth()->user()->name }}</div>
                    <span class="badge bg-warning text-dark" style="font-size:0.65rem">
                        {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                    </span>
                </div>
            </div>
        </div>

        @php $school = app('school'); $slug = $school->slug;
            $userRoles   = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r));
            $isPrincipal = $userRoles->contains('principal');
        @endphp

        <ul class="nav flex-column">
            <li><a href="{{ $isPrincipal ? route('admin.dashboard') : route('school.dashboard', $slug) }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('school.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a></li>

            {{-- Principal: School Users link --}}
            @if($isPrincipal)
            <li><a href="{{ route('admin.school.users.index') }}"
                   class="nav-link {{ request()->routeIs('admin.school.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> School Users
            </a></li>
            @endif

            @canany(['view student', 'create student'])
            <div class="sidebar-section">Academic</div>
            <li><a href="{{ route('school.students.index', $slug) }}" class="nav-link {{ request()->routeIs('school.students.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Students
            </a></li>
            @endcanany

            @can('view teacher')
            <li><a href="{{ route('school.teachers.index', $slug) }}" class="nav-link {{ request()->routeIs('school.teachers.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Teachers
            </a></li>
            @endcan

            @can('create class')
            <li><a href="{{ route('school.classes.index', $slug) }}" class="nav-link {{ request()->routeIs('school.classes.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Classes
            </a></li>
            <li><a href="{{ route('school.subjects.index', $slug) }}" class="nav-link {{ request()->routeIs('school.subjects.*') ? 'active' : '' }}">
                <i class="bi bi-book"></i> Subjects
            </a></li>
            @endcan

            @canany(['mark teacher attendance', 'mark student attendance'])
            <div class="sidebar-section">Attendance</div>
            @endcanany

            @can('mark teacher attendance')
            <li><a href="{{ route('school.attendance.teachers', $slug) }}" class="nav-link {{ request()->routeIs('school.attendance.teachers*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Teacher Attendance
            </a></li>
            @endcan

            @can('mark student attendance')
            <li><a href="{{ route('school.attendance.students', $slug) }}" class="nav-link {{ request()->routeIs('school.attendance.students*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check"></i> Student Attendance
            </a></li>
            @endcan

            @canany(['generate payroll', 'view payroll'])
            <div class="sidebar-section">Payroll</div>
            <li><a href="{{ route('school.payroll.index', $slug) }}" class="nav-link {{ request()->routeIs('school.payroll.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Teacher Payroll
            </a></li>
            @endcanany

            @can('manage salary structure')
            <li><a href="{{ route('school.staff.salaries', $slug) }}" class="nav-link {{ request()->routeIs('school.staff.salaries*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Staff Salaries
            </a></li>
            @endcan

            @canany(['view fee', 'generate fee voucher'])
            <div class="sidebar-section">Fees</div>
            <li><a href="{{ route('school.fees.vouchers', $slug) }}" class="nav-link {{ request()->routeIs('school.fees.vouchers*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Fee Vouchers
            </a></li>
            <li><a href="{{ route('school.fees.payments', $slug) }}" class="nav-link {{ request()->routeIs('school.fees.payments*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Payments
            </a></li>
            <li><a href="{{ route('school.fees.structures', $slug) }}" class="nav-link {{ request()->routeIs('school.fees.structures*') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> Fee Structures
            </a></li>
            @endcanany

            @canany(['create exam', 'view exam'])
            <div class="sidebar-section">Exams</div>
            <li><a href="{{ route('school.exams.index', $slug) }}" class="nav-link {{ request()->routeIs('school.exams.*') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i> Exams
            </a></li>
            @endcanany

            @can('view staff')
            <div class="sidebar-section">Staff</div>
            <li><a href="{{ route('school.staff.index', $slug) }}" class="nav-link {{ request()->routeIs('school.staff.index') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill"></i> Staff
            </a></li>
            @endcan

            @can('manage inventory')
            <div class="sidebar-section">Inventory</div>
            <li><a href="{{ route('school.inventory.index', $slug) }}" class="nav-link {{ request()->routeIs('school.inventory.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Inventory
            </a></li>
            @endcan

            @can('view complaint')
            <div class="sidebar-section">Communication</div>
            <li><a href="{{ route('school.complaints.index', $slug) }}" class="nav-link {{ request()->routeIs('school.complaints.*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-text"></i> Complaints
            </a></li>
            <li><a href="{{ route('school.meetings.index', $slug) }}" class="nav-link {{ request()->routeIs('school.meetings.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i> Meetings
            </a></li>
            @endcan

            @can('create exam')
            <div class="sidebar-section">AI Paper Generator</div>
            <li><a href="{{ route('school.question-bank.index', $slug) }}" class="nav-link {{ request()->routeIs('school.question-bank.*') ? 'active' : '' }}">
                <i class="bi bi-robot"></i> Question Banks
            </a></li>
            <li><a href="{{ route('school.question-bank.papers', $slug) }}" class="nav-link {{ request()->routeIs('school.question-bank.papers*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Generated Papers
            </a></li>
            @endcan

            @canany(['view income report', 'view expense report'])
            <div class="sidebar-section">Reports</div>
            <li><a href="{{ route('school.reports.income', $slug) }}" class="nav-link {{ request()->routeIs('school.reports.income') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Income
            </a></li>
            <li><a href="{{ route('school.reports.expense', $slug) }}" class="nav-link {{ request()->routeIs('school.reports.expense') ? 'active' : '' }}">
                <i class="bi bi-graph-down-arrow"></i> Expenses
            </a></li>
            <li><a href="{{ route('school.reports.income-vs-expense', $slug) }}" class="nav-link {{ request()->routeIs('school.reports.income-vs-expense') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> P&L Report
            </a></li>
            @endcanany
        </ul>

        <div class="mt-4 pt-3 border-top border-white border-opacity-25">
            <form method="POST" action="{{ $isPrincipal ? route('admin.logout') : route('logout') }}">
                @csrf
                <button class="btn btn-sm w-100 text-white border-white border-opacity-25" style="background:rgba(255,255,255,0.1)">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Topbar -->
    <div class="topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="school-badge d-none d-md-inline">{{ app('school')->name }}</span>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0 small">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small d-none d-md-inline">{{ now()->format('d M Y') }}</span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="p-3 p-md-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (window.innerWidth <= 991) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        }
    }
</script>
@stack('scripts')
</body>
</html>
