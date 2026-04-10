<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ app('school')->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f0f4f8; }
        .teacher-sidebar {
            background: linear-gradient(180deg, #0f4c75 0%, #1b6ca8 100%);
            width: 240px;
            height: 100vh;
            position: fixed; top: 0; left: 0;
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s ease;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }
        .teacher-sidebar::-webkit-scrollbar { width: 4px; }
        .teacher-sidebar::-webkit-scrollbar-track { background: transparent; }
        .teacher-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.25); border-radius: 4px; }
        .teacher-sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
        .teacher-sidebar.collapsed { transform: translateX(-240px); }
        .t-nav-link {
            color: rgba(255,255,255,0.85);
            border-radius: 8px;
            padding: 0.55rem 1rem;
            margin-bottom: 2px;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: flex; align-items: center; gap: 0.5rem;
            text-decoration: none;
        }
        .t-nav-link:hover, .t-nav-link.active {
            background: rgba(255,255,255,0.18); color: #fff;
        }
        .t-nav-link i { width: 1.2rem; text-align: center; }
        .t-section { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,0.4); padding: 0.6rem 1rem 0.2rem; }
        .teacher-main { margin-left: 240px; min-height: 100vh; transition: margin-left 0.3s; }
        .teacher-main.expanded { margin-left: 0; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0.65rem 1.5rem;
            position: sticky; top: 0; z-index: 1030; }
        .sidebar-overlay { display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 1039; }
        .sidebar-overlay.show { display: block; }
        @media (max-width: 991px) {
            .teacher-sidebar { transform: translateX(-240px); }
            .teacher-sidebar.show { transform: translateX(0); }
            .teacher-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<nav class="teacher-sidebar" id="teacherSidebar">
    <div class="p-3">
        {{-- Brand --}}
        <div class="d-flex align-items-center gap-2 mb-3 pb-3" style="border-bottom:1px solid rgba(255,255,255,0.15)">
            <div class="bg-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px">
                <i class="bi bi-person-badge-fill text-primary"></i>
            </div>
            <div>
                <div class="text-white fw-bold" style="font-size:0.85rem;line-height:1.2">Teacher Portal</div>
                <small class="text-white-50" style="font-size:0.65rem">{{ app('school')->name }}</small>
            </div>
        </div>

        {{-- User --}}
        <div class="mb-3 p-2 rounded-3" style="background:rgba(255,255,255,0.1)">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary flex-shrink-0"
                     style="width:32px;height:32px;font-size:0.85rem">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-white fw-semibold text-truncate" style="font-size:0.8rem">{{ auth()->user()->name }}</div>
                    <span class="badge bg-info text-dark" style="font-size:0.6rem">Teacher</span>
                </div>
            </div>
        </div>

        @php $slug = app('school')->slug; @endphp
        <ul class="nav flex-column">
            <li><a href="{{ route('teacher.dashboard', $slug) }}" class="t-nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a></li>

            <div class="t-section">My Info</div>
            <li><a href="{{ route('teacher.attendance', $slug) }}" class="t-nav-link {{ request()->routeIs('teacher.attendance') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> My Attendance
            </a></li>
            <li><a href="{{ route('teacher.payroll', $slug) }}" class="t-nav-link {{ request()->routeIs('teacher.payroll') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> My Payroll
            </a></li>

            <div class="t-section">Academic</div>
            <li><a href="{{ route('teacher.subjects', $slug) }}" class="t-nav-link {{ request()->routeIs('teacher.subjects') ? 'active' : '' }}">
                <i class="bi bi-book"></i> My Subjects
            </a></li>
            <li><a href="{{ route('teacher.exam-schedule', $slug) }}" class="t-nav-link {{ request()->routeIs('teacher.exam-schedule') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Exam Schedule
            </a></li>
            <li><a href="{{ route('teacher.remarks', $slug) }}" class="t-nav-link {{ request()->routeIs('teacher.remarks*') ? 'active' : '' }}">
                <i class="bi bi-chat-left-text"></i> Parent Remarks
            </a></li>
        </ul>

        <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,0.15)">
            <a href="{{ route('portal.change-password', $slug) }}" class="t-nav-link mb-2">
                <i class="bi bi-key"></i> Change Password
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="t-nav-link w-100 border-0 text-start" style="background:rgba(255,255,255,0.1)">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="teacher-main" id="teacherMain">
    <div class="topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="fw-semibold text-muted small d-none d-md-inline">@yield('title')</span>
        </div>
        <span class="text-muted small d-none d-md-inline">{{ now()->format('d M Y') }}</span>
    </div>

    <div class="p-3 p-md-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm py-2">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm py-2">
                <ul class="mb-0 ps-3 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    const s = document.getElementById('teacherSidebar');
    const m = document.getElementById('teacherMain');
    const o = document.getElementById('sidebarOverlay');
    if (window.innerWidth <= 991) {
        s.classList.toggle('show');
        o.classList.toggle('show');
    } else {
        s.classList.toggle('collapsed');
        m.classList.toggle('expanded');
    }
}
</script>
@stack('scripts')
</body>
</html>
