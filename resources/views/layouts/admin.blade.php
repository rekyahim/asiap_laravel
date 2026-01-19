<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ASIAP SDT')</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- ====== Global CSS ====== --}}
    <style>
        /* ================= BREADCRUMB GENERIC ================= */
        .page-breadcrumb {
            margin: -.25rem 0 1rem 0
        }

        .crumbs {
            font-size: 1.0rem
        }

        .crumb {
            color: #6c757d;
            text-decoration: none;
            transition: color .15s ease
        }

        .crumb:hover {
            color: #212529;
            text-decoration: underline
        }

        .crumb.active {
            font-weight: 600;
            color: #212529;
            pointer-events: none;
            text-decoration: none
        }

        .crumb-sep {
            margin: 0 .35rem;
            color: #adb5bd
        }

        /* ================= TABLE HEADER GENERIC ================= */
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: .85rem;
            letter-spacing: .5px
        }

        /* ================= AKSI BUTTON GENERIC ================= */
        .aksi-btns .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            color: #fff;
        }

        .aksi-btns .btn-icon i {
            font-size: 1rem;
            line-height: 1;
        }

        /* hover effect */
        @media (prefers-reduced-motion:no-preference) {

            .aksi-btns .btn-icon:hover,
            .aksi-btns .btn-icon:focus {
                transform: translateY(-1px);
                filter: brightness(.98);
                background-color: inherit !important;
                color: #fff !important;
            }
        }

        /* ================= AKSI BUTTON FIX ================= */
        .aksi-btns .btn-icon {
            color: #fff;
        }

        .aksi-btns .btn-icon i {
            color: inherit;
        }


        .btn-add {
            background: #5D87FF;
            /* hijau */
        }

        .btn-brand {
            background: #5965e8;
            border-color: #5965e8;
        }

        .btn-brand:hover {
            background: #7380ff;
            border-color: #7380ff;
        }

        /* ===== GLOBAL CARD (MASTER) ===== */
        .app-card {
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06), 0 2px 6px rgba(0, 0, 0, .04);
            border: 1px solid rgba(0, 0, 0, .03);
            border-radius: .75rem
        }

        /* ================= PAGINATION ================= */
        .pagination .page-link {
            border-radius: .3rem
        }

        .pagination .page-item.active .page-link {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary)
        }

        /* ================= HEADER SEARCH ================= */
        .header-search {
            width: 100%;
            max-width: 520px;
            margin-right: 16px;
        }

        .header-search .header-search-group {
            --bd: rgba(0, 0, 0, .08);
            display: flex;
            align-items: stretch;
            border: 1px solid var(--bd);
            border-radius: .75rem;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
            overflow: hidden;
            transition: box-shadow .18s ease, border-color .18s ease;
        }

        .header-search .header-search-group:focus-within {
            border-color: #0d6efd;
            box-shadow: 0 6px 18px rgba(13, 110, 253, .15);
        }

        .header-search .input-group-text {
            background: transparent;
            border: 0;
            color: #6c757d;
        }

        .header-search .form-control {
            border: 0;
            box-shadow: none !important;
        }

        @media (max-width: 992px) {
            .header-search {
                max-width: 420px;
            }
        }

        @media (max-width: 500px) {
            .header-search {
                margin-right: 8px;
            }

            .header-search-group button {
                display: none;
            }
        }

        /* ================= HEADER Z-INDEX ================= */
        .app-header {
            position: sticky;
            top: 0;
            z-index: 1100;
        }

        /* ================= SIDEBAR BACK BUTTON ================= */
        .brand-logo {
            position: relative;
        }

        .sidebar-back-btn {
            position: absolute;
            top: 18px;
            right: -16px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #fff;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
            z-index: 1050;
        }

        .sidebar-back-btn i {
            font-size: 18px;
        }

        /* ================= SIDEBAR & OVERLAY ================= */
        .left-sidebar {
            z-index: 1050;
        }

        .sidebar-overlay {
            z-index: 1040;
        }

        /* =========================================================
   COLLAPSIBLE SIDEBAR – IKUT SISTEM TEMPLATE
========================================================= */

        /* SIDEBAR NORMAL */
        .page-wrapper:not(.mini-sidebar) .left-sidebar {
            width: 260px !important;
        }

        /* SIDEBAR MINI */
        .page-wrapper.mini-sidebar .left-sidebar {
            width: 78px !important;
        }

        /* SEMBUNYIKAN TEKS MENU */
        .page-wrapper.mini-sidebar .sidebar-link span,
        .page-wrapper.mini-sidebar .nav-small-cap {
            display: none !important;
        }

        /* ICON CENTER */
        .page-wrapper.mini-sidebar .sidebar-link {
            justify-content: center;
        }

        .page-wrapper.mini-sidebar .sidebar-link i {
            font-size: 20px;
        }

        /* LOGO MINI */
        .page-wrapper.mini-sidebar .logo-img img {
            width: 40px;
        }

        /* HIDE BACK BUTTON DI MINI */
        .page-wrapper.mini-sidebar .sidebar-back-btn {
            display: none !important;
        }

        /* ============================================
   COLLAPSIBLE SIDEBAR – IKUT TEMPLATE
============================================ */

        /* sidebar width */
        .page-wrapper:not(.mini-sidebar) .left-sidebar {
            width: 260px !important;
        }

        .page-wrapper.mini-sidebar .left-sidebar {
            width: 78px !important;
        }

        /* sembunyikan teks menu saat mini */
        .page-wrapper.mini-sidebar .sidebar-link span,
        .page-wrapper.mini-sidebar .nav-small-cap {
            display: none !important;
        }

        /* icon center */
        .page-wrapper.mini-sidebar .sidebar-link {
            justify-content: center;
        }

        .page-wrapper.mini-sidebar .sidebar-link i {
            font-size: 20px;
        }

        /* logo mini */
        .page-wrapper.mini-sidebar .logo-img img {
            width: 40px;
        }

        /* hide back button di mini */
        .page-wrapper.mini-sidebar .sidebar-back-btn {
            display: none !important;
        }
    </style>

    {{-- Halaman boleh push CSS tambahan di sini --}}
    @stack('styles')
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        <div class="body-wrapper">

            {{-- Header --}}
            @include('partials.header')

            {{-- Konten halaman --}}
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- JS dari template --}}
    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
    {{-- TAMBAHKAN INI: Setup Global CSRF Token untuk semua AJAX --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    {{-- Halaman boleh push JS tambahan di sini --}}
    @stack('scripts')
</body>

</html>
