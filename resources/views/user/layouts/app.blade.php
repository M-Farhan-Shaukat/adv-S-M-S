<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5, user-scalable=yes">
    <title>@yield('title', 'User Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Modern Gradient Variables */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-gradient: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 24px rgba(102, 126, 234, 0.15);
            --border-radius: 16px;
        }

        /* Mobile First Approach */
        .user-sidebar {
            background: var(--sidebar-gradient);
            min-height: auto;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-lg);
        }

        @media (min-width: 768px) {
            .user-sidebar {
                min-height: 100vh;
                border-radius: 0 20px 20px 0;
                position: fixed;
                width: inherit;
                max-width: inherit;
            }

            .col-md-3.col-lg-2 {
                position: relative;
            }

            .main-content {
                margin-left: auto;
            }
        }

        .sidebar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        @media (min-width: 768px) {
            .sidebar-brand {
                font-size: 1.3rem;
            }
        }

        .nav-link-user {
            color: rgba(255,255,255,0.9);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 6px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .nav-link-user:hover, .nav-link-user.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
            border-color: rgba(255,255,255,0.3);
            box-shadow: var(--shadow-sm);
        }

        .nav-link-user i {
            font-size: 1.2rem;
            width: 1.8rem;
            text-align: center;
            filter: drop-shadow(0 2px 2px rgba(0,0,0,0.2));
        }

        .user-badge {
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
            letter-spacing: 0.3px;
            background: rgba(255,255,255,0.2);
            color: white;
            backdrop-filter: blur(5px);
        }

        /* Mobile Header - UNIFIED STYLES */
        .mobile-header {
            background: var(--primary-gradient);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 1046;
            min-height: 70px;
        }

        .mobile-header-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .mobile-header-brand .brand-icon {
            background: white;
            border-radius: 12px;
            padding: 0.4rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .mobile-header-brand .brand-icon i {
            color: #667eea;
            font-size: 1.2rem;
        }

        /* UNIFIED Mobile Header Button Styles - HIGH SPECIFICITY */
        .mobile-header .mobile-nav-toggle {
            padding: 0.5rem !important;
            min-width: 40px !important;
            width: 40px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 10px !important;
            transition: all 0.3s ease !important;
            color: white !important;
            background: rgba(255,255,255,0.15) !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            margin: 0 !important;
            box-shadow: none !important;
            font-size: 0 !important; /* Hide any text */
            line-height: 1 !important;
        }

        .mobile-header .mobile-nav-toggle i {
            font-size: 1.3rem !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
            line-height: 1 !important;
        }

        /* Remove any potential text */
        .mobile-header .mobile-nav-toggle::before,
        .mobile-header .mobile-nav-toggle::after {
            display: none !important;
            content: none !important;
        }

        /* Override Bootstrap and any other classes */
        .mobile-header .btn.mobile-nav-toggle,
        .mobile-header button.mobile-nav-toggle,
        .mobile-header .btn-link.mobile-nav-toggle {
            padding: 0.5rem !important;
            min-width: 40px !important;
            width: 40px !important;
            height: 40px !important;
        }

        .mobile-header-brand > div:last-child {
            line-height: 1.2;
        }

        .mobile-header-brand > div:last-child .sidebar-brand {
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .mobile-header-brand > div:last-child small {
            font-size: 0.7rem;
        }

        .mobile-header .btn-link:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.05);
        }

        /* Sidebar Collapse Animation */
        .sidebar-collapse {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Mobile Sidebar Overlay */
        @media (max-width: 767.98px) {
            .sidebar-collapse {
                position: fixed;
                top: 0;
                left: 0;
                width: 280px;
                height: 100vh;
                z-index: 1045;
                transform: translateX(-100%);
                overflow-y: auto;
            }

            .sidebar-collapse.show {
                transform: translateX(0);
            }

            .sidebar-collapse .user-sidebar {
                height: 100vh;
                border-radius: 0;
                min-height: 100vh;
            }

            /* Mobile sidebar overlay backdrop */
            .sidebar-collapse.show::before {
                content: '';
                position: fixed;
                top: 0;
                left: 280px;
                right: 0;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: -1;
                backdrop-filter: blur(2px);
            }

            /* Better touch targets for mobile */
            .nav-link-user {
                padding: 0.875rem 1rem;
                min-height: 44px;
                display: flex;
                align-items: center;
            }

            /* Mobile close button improvements */
            .sidebar-collapse .btn-outline-light {
                min-width: 40px;
                min-height: 40px;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
            }
        }

        /* Main Content Area */
        .main-content {
            background: #f8fafd;
            min-height: 100vh;
        }

        /* Card Styles */
        .card {
            border-radius: var(--border-radius) !important;
            border: none !important;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        /* Alert Styling */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: var(--shadow-sm);
            background: white;
            border-left: 4px solid;
        }

        .alert-info {
            border-left-color: #0dcaf0;
        }

        .alert-info i {
            color: #0dcaf0;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46a1 100%);
        }

        /* Touch-friendly targets */
        .btn, .nav-link, .form-check-label, a {
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }

        /* Improved spacing for mobile */
        @media (max-width: 767.98px) {
            .container-fluid {
                padding-left: 0;
                padding-right: 0;
            }

            .main-content {
                padding: 1rem !important;
            }

            .user-sidebar {
                border-radius: 0;
            }

            .nav-link-user {
                padding: 0.85rem 1.25rem;
                margin-bottom: 4px;
            }

            .alert {
                margin: 0 0 1rem 0;
            }

            .card-body {
                padding: 1.25rem !important;
            }

            /* Adjust profile header for mobile */
            .profile-header .card-body {
                padding: 1rem !important;
            }
        }

        /* Tablet optimizations */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .user-sidebar .p-4 {
                padding: 1.25rem 0.75rem !important;
            }

            .nav-link-user {
                padding: 0.6rem 0.85rem;
                font-size: 0.9rem;
            }

            .sidebar-brand {
                font-size: 1.1rem;
            }
        }

        /* Smooth transitions */
        .collapse {
            transition: all 0.4s ease;
        }

        /* User info card */
        .user-info-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .user-info-card:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        .user-avatar {
            background: white;
            border-radius: 12px;
            padding: 0.5rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .user-avatar i {
            color: #667eea;
            font-size: 1.1rem;
        }

        /* Logout button */
        .btn-logout {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .btn-logout i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
@auth
    @if(auth()->user()->hasRole('User'))
        <!-- Mobile Header - UNIFIED Visible only on mobile -->
        <div class="d-md-none mobile-header">
            <div class="mobile-header-brand">

                <div>
                    <div class="sidebar-brand mb-0">Document System</div>
{{--                    <small class="text-white-50" style="font-size: 0.7rem;">User Panel</small>--}}
                </div>
            </div>
            <button class="btn btn-link mobile-nav-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSidebar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
        </div>
    @endif
@endauth

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar for User -->
        @auth
            @if(auth()->user()->hasRole('User'))
                <div class="col-md-3 col-lg-2 px-0">
                    <!-- Desktop Sidebar (visible on md and up) -->
                    <div class="user-sidebar d-none d-md-block">
                        <div class="d-flex flex-column p-4">
                            <!-- Brand -->
                            <div class="d-flex align-items-center mb-4">

                                <div>
                                    <div class="sidebar-brand">Auth System</div>
{{--                                    <small class="text-white-50">User Panel</small>--}}
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="user-info-card p-3 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="user-avatar">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="text-white fw-semibold">{{ auth()->user()->name }}</div>
                                        <small class="text-white-50" style="font-size: 0.75rem;">{{ auth()->user()->email }}</small>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="user-badge">
                                        <i class="bi bi-shield-check me-1"></i>
                                        {{ auth()->user()->role->name ?? 'User' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('user.dashboard') }}" class="nav-link-user d-flex align-items-center">
                                        <i class="bi bi-speedometer2 me-2"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('user.profile') }}" class="nav-link-user d-flex align-items-center">
                                        <i class="bi bi-person me-2"></i>
                                        My Profile
                                    </a>
                                </li>

                                <li class="nav-item mt-4">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn-logout w-100">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Mobile Sidebar (collapsible) -->
                    <div class="collapse sidebar-collapse" id="mobileSidebar">
                        <div class="user-sidebar">
                            <div class="d-flex flex-column p-4">
                                <!-- Close button for mobile -->
                                <div class="d-flex justify-content-between align-items-center mb-4 d-md-none">
                                    <div class="text-white fw-semibold">Menu</div>
                                    <button class="btn btn-outline-light rounded-3 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSidebar" aria-label="Close menu" style="width: 40px; height: 40px;">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                <!-- User Info (Mobile) -->
                                <div class="user-info-card p-3 mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="user-avatar">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="text-white fw-semibold">{{ auth()->user()->name }}</div>
                                            <small class="text-white-50" style="font-size: 0.75rem;">{{ auth()->user()->email }}</small>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span class="user-badge">
                                            <i class="bi bi-shield-check me-1"></i>
                                            {{ auth()->user()->role->name ?? 'User' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Navigation (Mobile) -->
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('user.dashboard') }}" class="nav-link-user d-flex align-items-center" onclick="closeMobileSidebar()">
                                            <i class="bi bi-speedometer2 me-2"></i>
                                            Dashboard
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('user.profile') }}" class="nav-link-user d-flex align-items-center" onclick="closeMobileSidebar()">
                                            <i class="bi bi-person me-2"></i>
                                            My Profile
                                        </a>
                                    </li>

                                    <li class="nav-item mt-4">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="btn-logout w-100">
                                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        <!-- Main Content -->
        <div class="main-content @if(auth()->check() && auth()->user()->hasRole('User')) col-md-9 col-lg-10 @else col-12 @endif p-4 p-md-5">
            <!-- Role Alert -->

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        });
    </script>
@endif


@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });

            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        });
    </script>
@endif


@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });

            Toast.fire({
                icon: 'error',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        });
    </script>
@endif


<script>
    // Close mobile sidebar when clicking on nav links
    function closeMobileSidebar() {
        if (window.innerWidth < 768) {
            const mobileSidebar = document.getElementById('mobileSidebar');
            if (mobileSidebar) {
                const bsCollapse = bootstrap.Collapse.getInstance(mobileSidebar);
                if (bsCollapse) {
                    bsCollapse.hide();
                } else {
                    // Fallback if instance doesn't exist
                    mobileSidebar.classList.remove('show');
                }
            }
        }
    }

    // Close mobile sidebar when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const mobileHeader = document.querySelector('.mobile-header');
            const mobileToggle = document.querySelector('.mobile-nav-toggle');

            if (mobileSidebar && mobileSidebar.classList.contains('show')) {
                const isClickInsideSidebar = mobileSidebar.contains(event.target);
                const isClickOnHeader = mobileHeader && mobileHeader.contains(event.target);
                const isClickOnToggle = mobileToggle && mobileToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnHeader && !isClickOnToggle) {
                    closeMobileSidebar();
                }
            }
        }
    });

    // Auto-close sidebar on window resize from mobile to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            const mobileSidebar = document.getElementById('mobileSidebar');
            if (mobileSidebar && mobileSidebar.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(mobileSidebar);
                if (bsCollapse) {
                    bsCollapse.hide();
                } else {
                    mobileSidebar.classList.remove('show');
                }
            }
        }
    });

    // Initialize tooltips if any
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Ensure mobile sidebar is properly initialized with Bootstrap
        const mobileSidebar = document.getElementById('mobileSidebar');
        if (mobileSidebar) {
            new bootstrap.Collapse(mobileSidebar, {
                toggle: false
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>
