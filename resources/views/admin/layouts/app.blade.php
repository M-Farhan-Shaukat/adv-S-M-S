<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f0f4f8; }
        .admin-sidebar {
            background: linear-gradient(180deg, #1e3a5f 0%, #2d6a9f 100%);
            width: 250px;
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
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.25); border-radius: 4px; }
        .admin-sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
        .admin-sidebar.collapsed { transform: translateX(-250px); }
        .sidebar-nav-link {
            color: rgba(255,255,255,0.85);
            border-radius: 8px;
            padding: 0.55rem 1rem;
            margin-bottom: 2px;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .sidebar-nav-link:hover, .sidebar-nav-link.active {
            background: rgba(255,255,255,0.18);
            color: #fff;
        }
        .sidebar-nav-link i { width: 1.2rem; text-align: center; }
        .sidebar-section-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.4);
            padding: 0.6rem 1rem 0.2rem;
        }
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.65rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .admin-main {
            margin-left: 250px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        .admin-main.expanded { margin-left: 0; }
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1039;
        }
        .sidebar-overlay.show { display: block; }
        @media (max-width: 991px) {
            .admin-sidebar { transform: translateX(-250px); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

@auth
@php
    $userRoles = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r));
    $isAdminUser = $userRoles->intersect(['admin','manager','staff','principal'])->isNotEmpty();
    $currentRole = $userRoles->first() ?? '';
@endphp

@if($isAdminUser)

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav class="admin-sidebar" id="adminSidebar">
    <div class="p-3">
        {{-- Brand --}}
        <div class="d-flex align-items-center gap-2 mb-3 pb-3" style="border-bottom:1px solid rgba(255,255,255,0.15)">
            <div class="bg-white rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:36px;height:36px">
                <i class="bi bi-mortarboard-fill text-primary"></i>
            </div>
            <div>
                <div class="text-white fw-bold" style="font-size:0.9rem;line-height:1.2">School MS</div>
                <small class="text-white-50" style="font-size:0.65rem">Management System</small>
            </div>
        </div>

        {{-- User Info --}}
        <div class="mb-3 p-2 rounded-3" style="background:rgba(255,255,255,0.1)">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary flex-shrink-0"
                     style="width:32px;height:32px;font-size:0.85rem">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-white fw-semibold text-truncate" style="font-size:0.8rem">{{ auth()->user()->name }}</div>
                    <span class="badge bg-warning text-dark" style="font-size:0.6rem">{{ strtoupper($currentRole) }}</span>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        @php
            $authRoles = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r));
            $isAdmin   = $authRoles->contains('admin');
        @endphp

        <ul class="nav flex-column">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            @if($isAdmin)
            {{-- ===== SUPER ADMIN ===== --}}
            <div class="sidebar-section-label">System</div>
            <li>
                <a href="{{ route('admin.schools.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.schools.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Schools
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
            </li>
            @endif
        </ul>

        {{-- Logout --}}
        <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,0.15)">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="sidebar-nav-link w-100 border-0 text-start"
                        style="background:rgba(255,255,255,0.1)">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="admin-main" id="adminMain">
    <!-- Topbar -->
    <div class="admin-topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary" onclick="toggleSidebar()">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="fw-semibold text-muted small d-none d-md-inline">
                @yield('title', 'Admin Panel')
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small d-none d-md-inline">{{ now()->format('d M Y') }}</span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger small">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="px-3 px-md-4 pt-3">
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
    </div>

    <!-- Page Content -->
    <div class="p-3 p-md-4">
        @yield('content')
    </div>
</div>

@else
{{-- Fallback: show content without sidebar --}}
<div class="p-4">@yield('content')</div>
@endif

@endauth

{{-- Guest fallback --}}
@guest
<div class="p-4">@yield('content')</div>
@endguest

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const main    = document.getElementById('adminMain');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar) return;
    if (window.innerWidth <= 991) {
        sidebar.classList.toggle('show');
        overlay && overlay.classList.toggle('show');
    } else {
        sidebar.classList.toggle('collapsed');
        main && main.classList.toggle('expanded');
    }
}
function togglePassword() {
    const p = document.getElementById('password');
    const i = document.getElementById('passwordIcon');
    if (!p) return;
    p.type = p.type === 'password' ? 'text' : 'password';
    i && i.classList.toggle('bi-eye');
    i && i.classList.toggle('bi-eye-slash');
}
</script>
@stack('scripts')
</body>
</html>
