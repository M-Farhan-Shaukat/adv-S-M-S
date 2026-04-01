@extends('user.layouts.app')

@section('title', 'User Dashboard')

@section('content')
    <!-- Enhanced Dashboard Header with subtle pattern -->
    <div class="dashboard-header mb-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg overflow-hidden" style="background: linear-gradient(135deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);">
                    <div class="card-body p-4 p-md-5 position-relative">
                        <!-- Decorative elements -->
                        <div class="position-absolute top-0 end-0 opacity-10 d-none d-md-block">
                            <svg width="180" height="180" viewBox="0 0 100 100" fill="white">
                                <circle cx="80" cy="20" r="20"/>
                                <circle cx="20" cy="80" r="30"/>
                                <circle cx="90" cy="90" r="15"/>
                            </svg>
                        </div>
                        <div class="row align-items-center position-relative">
                            <div class="col-12 col-lg-8">
                                <div class="d-flex align-items-center flex-wrap flex-md-nowrap">
                                    <div class="avatar-circle bg-white shadow-lg me-3 mb-2 mb-md-0" style="width: 70px; height: 70px; border-radius: 20px; transform: rotate(-5deg);">
                                        @php
                                            $colors = ['primary', 'info', 'success', 'warning', 'danger'];
                                            $randomColor = $colors[array_rand($colors)];
                                        @endphp
                                        <i class="bi bi-person-circle fs-1 text-{{ $randomColor }}"></i>
                                    </div>
                                    <div class="text-white">
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <h1 class="h3 fw-bold mb-0">Welcome back, {{ auth()->user()->name }}!</h1>
                                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm">
                                                <i class="bi bi-shield-check me-1"></i>
                                                {{ auth()->user()->role->name ?? 'User' }}
                                            </span>
                                        </div>
                                        <p class="mb-0 mt-2 opacity-90 d-flex align-items-center flex-wrap">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            {{ now()->format('l, F j, Y') }}
                                            <span class="mx-2 d-none d-sm-inline">•</span>
                                            <i class="bi bi-clock me-2 ms-0 ms-sm-2"></i>
                                            {{ now()->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                        Document Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <img id="previewImage" class="img-fluid rounded-3 d-none" style="max-height: 400px;">
                    <iframe id="previewPdf" class="w-100 d-none" style="height: 500px;" frameborder="0"></iframe>
                    <div id="previewNotAvailable" class="py-5 d-none">
                        <div class="empty-state-wrapper">
                            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                <i class="bi bi-eye-slash fs-1 text-muted"></i>
                            </div>
                            <h5>Preview Not Available</h5>
                            <p class="text-muted">This file type cannot be previewed online.</p>
                            <a href="#" class="btn btn-primary mt-2" id="downloadFromPreview">Download File</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<!-- Enhanced Styles -->
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);
        --shadow-hover: 0 10px 30px rgba(0,0,0,0.15);
    }

    /* Dashboard Header */
    .dashboard-header .card {
        border-radius: 30px !important;
    }

    .avatar-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .avatar-circle:hover {
        transform: rotate(0deg) scale(1.1) !important;
    }

    .opacity-10 {
        opacity: 0.1;
    }

    /* Stat Cards */
    .stat-card {
        border-radius: 20px !important;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.5s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover) !important;
    }

    .stat-card:hover::after {
        opacity: 1;
    }

    .stat-icon-wrapper {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    @media (min-width: 768px) {
        .stat-icon-wrapper {
            width: 60px;
            height: 60px;
        }
    }

    .stat-card:hover .stat-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }

    .stat-number {
        color: #2d3748;
        line-height: 1.2;
    }

    /* Dot for status badges */
    .dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }

    /* File icon wrapper */
    .file-icon-wrapper {
        transition: all 0.3s ease;
    }

    .file-icon-wrapper:hover {
        transform: scale(1.2) rotate(5deg);
    }

    /* Recent Upload Item */
    .recent-upload-item {
        transition: all 0.3s ease;
    }

    .recent-upload-item:hover {
        transform: translateX(8px);
        background-color: rgba(13, 110, 253, 0.03);
        border-radius: 12px;
        padding-left: 8px;
    }

    .recent-upload-icon {
        transition: all 0.3s ease;
    }

    .recent-upload-item:hover .recent-upload-icon {
        transform: scale(1.1);
    }

    /* Empty State */
    .empty-state-wrapper {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Hover Shadow */
    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
        transform: translateY(-2px);
    }

    /* Transition */
    .transition {
        transition: all 0.3s ease;
    }

    /* Button hover effects */
    .btn-primary, .btn-outline-info, .btn-outline-secondary {
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3a4ab0, #b13eaa);
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(65, 88, 208, 0.4);
    }

    .btn-outline-info:hover,
    .btn-outline-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #4158D0, #C850C0);
        border-radius: 10px;
    }

    /* Mobile optimizations */
    @media (max-width: 767.98px) {
        .dashboard-header .card-body {
            padding: 1.5rem !important;
        }

        .avatar-circle {
            width: 50px !important;
            height: 50px !important;
        }

        .avatar-circle i {
            font-size: 2rem !important;
        }

        .stat-card .card-body {
            padding: 1rem !important;
        }

        .stat-icon-wrapper {
            width: 40px !important;
            height: 40px !important;
            padding: 0.5rem !important;
        }

        .stat-icon-wrapper i {
            font-size: 1.2rem !important;
        }

        .stat-number {
            font-size: 1.3rem !important;
        }
    }

    @media (max-width: 575.98px) {
        .badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
        }

        .btn-sm.rounded-circle {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    }

    /* Smooth animations */
    .card, .btn, .badge {
        transition: all 0.3s ease;
    }

    /* Modal improvements */
    .modal-content {
        border-radius: 25px;
    }

    /* Table improvements */
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #4a5568;
        border-bottom-width: 1px;
    }

    /* Tooltip styles (if needed) */
    [data-bs-toggle="tooltip"] {
        cursor: pointer;
    }
</style>

<!-- JavaScript remains exactly the same -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Preview functionality
        document.querySelectorAll('.preview-btn').forEach(button => {
            button.addEventListener('click', function () {
                let url = this.dataset.url;
                let mime = this.dataset.mime;

                let img = document.getElementById('previewImage');
                let pdf = document.getElementById('previewPdf');
                let notAvailable = document.getElementById('previewNotAvailable');
                let downloadBtn = document.getElementById('downloadFromPreview');

                img.classList.add('d-none');
                pdf.classList.add('d-none');
                notAvailable.classList.add('d-none');

                if (mime.startsWith('image/')) {
                    img.src = url;
                    img.classList.remove('d-none');
                } else if (mime === 'application/pdf') {
                    pdf.src = url;
                    pdf.classList.remove('d-none');
                } else {
                    downloadBtn.href = url;
                    notAvailable.classList.remove('d-none');
                }

                new bootstrap.Modal(document.getElementById('previewModal')).show();
            });
        });
    });
</script>
