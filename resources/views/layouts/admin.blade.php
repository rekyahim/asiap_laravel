<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ASIAP SDT')</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- ====== Global CSS ====== --}}
    <style>
        .pagination .page-link {
            border-radius: .3rem
        }

        .pagination .page-item.active .page-link {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary)
        }

        /* ===== Header Search ===== */
        .header-search .header-search-group {
            --bd: rgba(0, 0, 0, .08);
            display: flex;
            align-items: stretch;
            gap: 0;
            border: 1px solid var(--bd);
            border-radius: .75rem;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
            transition: box-shadow .18s ease, border-color .18s ease;
            overflow: hidden;
            width: clamp(320px, 40vw, 600px);
        }

        .header-search .header-search-group:focus-within {
            border-color: #0d6efd;
            box-shadow: 0 6px 18px rgba(13, 110, 253, .15);
        }

        .header-search .input-group-text {
            background: transparent;
            border: 0;
            color: #6c757d;
            padding-inline: .6rem .25rem;
        }

        .header-search .form-control {
            border: 0;
            padding: .55rem .5rem;
            box-shadow: none !important;
        }

        .header-search .form-control::placeholder {
            color: #9aa1a7;
        }

        .header-search .btn-clear.boxed {
            margin-right: .35rem;
            border: 0 solid rgba(0, 0, 0, .08);
            background: transparent;
            color: #6c757d;
            padding: .35rem .55rem;
            border-radius: .5rem;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .header-search .btn-clear.boxed:hover {
            background: transparent;
            color: #343a40;
            border-color: rgba(0, 0, 0, .12);
        }

        .header-search .btn-submit {
            border: 0;
            border-left: 1px solid rgba(0, 0, 0, .06);
            border-radius: 0;
            padding: .55rem 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .header-search .btn-submit:focus {
            box-shadow: none;
        }

        @media (max-width:576px) {
            .header-search .header-search-group {
                width: clamp(240px, 70vw, 100%);
            }
        }

        /* ========== Tombol aksi ========== */
        .aksi-btns .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
        }

        .aksi-btns .btn-icon i {
            font-size: 1rem;
            line-height: 1;
        }

        @media (prefers-reduced-motion:no-preference) {
            .aksi-btns .btn-icon {
                transition: transform .12s ease, filter .12s ease, box-shadow .12s ease;
            }

            .aksi-btns .btn-icon:hover {
                transform: translateY(-1px);
                filter: brightness(.98);
                box-shadow: 0 4px 12px rgba(0, 0, 0, .10);
            }
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

    {{-- Halaman boleh push JS tambahan di sini --}}
    @stack('scripts')
</body>

</html>
