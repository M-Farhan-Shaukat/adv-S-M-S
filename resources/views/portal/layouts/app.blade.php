<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ app('school')->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --portal-color: @yield('portal-color', '#6366f1'); }
        body { background: #f8fafc; }
        .portal-sidebar {
            background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
            min-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }
        .portal-sidebar::-webkit-scrollbar { width: 4px; }
        .portal-sidebar::-webkit-scrollbar-track { background: transparent; }
        .portal-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .portal-nav-link {
            color: rgba(255,255,255,0.85);
            border-radius: 8px;
            padding: 0.6rem 1rem;
            margin-bottom: 2px;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .portal-nav-link:hover, .portal-nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 1.5rem; }
        @media (max-width: 767px) {
            .portal-sidebar { min-height: auto; }
        }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-2 d-none d-md-block">
            <div class="portal-sidebar p-3">
                <div class="text-white mb-4">
                    <div class="fw-bold">{{ app('school')->name }}</div>
                    <small class="opacity-75">@yield('portal-name', 'Portal')</small>
                </div>
                <div class="bg-white bg-opacity-10 rounded-3 p-2 mb-3">
                    <div class="text-white fw-semibold small">{{ auth()->user()->name }}</div>
                    <small class="text-white-50">{{ auth()->user()->getRoleNames()->first() }}</small>
                </div>
                @yield('sidebar-links')
                <div class="mt-4">
                    <a href="{{ route('portal.change-password', app('school')->slug) }}"
                       class="portal-nav-link mb-2">
                        <i class="bi bi-key me-2"></i>Change Password
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm w-100 text-white" style="background:rgba(255,255,255,0.15)">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main -->
        <div class="col-md-10">
            <div class="topbar d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">@yield('title')</h6>
                <span class="text-muted small">{{ now()->format('d M Y') }}</span>
            </div>
            <div class="p-3 p-md-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
