<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<!-- Mobile Header with Sidebar Toggle - Only visible on mobile -->
@auth
    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Staff'))
        <div class="d-md-none bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle p-1 me-2">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h6 class="mb-0">Admin Panel</h6>
                <small class="ms-2">
                    @if(auth()->user()->hasRole('Admin'))
                        <span class="badge bg-danger">Admin</span>
                    @elseif(auth()->user()->hasRole('Manager'))
                        <span class="badge bg-warning">Manager</span>
                    @elseif(auth()->user()->hasRole('Staff'))
                        <span class="badge bg-info">Staff</span>
                    @endif
                </small>
            </div>
            <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
        </div>
    @endif
@endauth

<div class="d-flex flex-column flex-md-row">
    <!-- Sidebar: Desktop (visible on md and up) / Mobile (collapsible) -->
    @auth
        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Staff'))
            <div class="bg-dark text-white collapse collapse-horizontal d-md-block" id="mobileSidebar" style="width: 15%;">
                <div class="p-3 vh-100 d-md-block" style="width: 100%; max-width: 280px; overflow-y: auto;">
                    <div class="d-flex align-items-center mb-4 d-md-flex">
                        <div class="bg-primary rounded-circle p-2 me-2">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-0">Admin Panel</h5>
                            <small class="text-white-50">
                                @if(auth()->user()->hasRole('Admin'))
                                    <span class="badge bg-danger">Admin</span>
                                @elseif(auth()->user()->hasRole('Manager'))
                                    <span class="badge bg-warning">Manager</span>
                                @elseif(auth()->user()->hasRole('Staff'))
                                    <span class="badge bg-info">Staff</span>
                                @endif
                            </small>
                        </div>
                        <button class="btn btn-sm btn-outline-light d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSidebar">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <ul class="nav flex-column mt-4">
                        <!-- Dashboard - All roles -->
                        <li class="nav-item mb-2">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>

                        <!-- Upload Attachments - Admin only -->


                        <!-- Manage Users - Admin only -->
                        @if(auth()->user()->hasPermission('manage_users'))
                            <li class="nav-item mb-2">
                                <a href="{{ route('admin.users') }}" class="nav-link text-white">
                                    <i class="bi bi-people me-2"></i> Manage Users
                                </a>
                            </li>
                        @endif



                        <hr class="text-white-50 my-3">

                        <li class="nav-item">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-light w-100">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    @endauth

    <!-- Main content -->
    <div class="flex-grow-1 p-3 p-md-4" style="min-width: 0; overflow-x: hidden;">
               @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');

        if (password) {
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    }

    // Close mobile sidebar when clicking on a nav link (on mobile)
    document.addEventListener('DOMContentLoaded', function() {
        const mobileSidebar = document.getElementById('mobileSidebar');
        if (mobileSidebar) {
            const navLinks = mobileSidebar.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth < 768) {
                        const bsCollapse = bootstrap.Collapse.getInstance(mobileSidebar);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                });
            });
        }
    });
</script>

</body>
</html>
