@php
    $user = \App\Models\Pengguna::find(session('auth_uid'));
    $foto =
        $user && $user->ID_FOTO
            ? asset('assets/images/profile/' . $user->ID_FOTO)
            : asset('assets/images/profile/default.jpg');
@endphp

@php
    $headerSearch = null;

    if (request()->routeIs('sdt.index', 'riwayat.petugas')) {
        $headerSearch = [
            'action' => route('sdt.index'),
            'name' => 'q',
            'placeholder' => 'Cari SDT / Tahun / Petugas…',
        ];
    } elseif (request()->routeIs('petugas.sdt.index')) {
        $headerSearch = [
            'action' => route('petugas.sdt.index'),
            'name' => 'q',
            'placeholder' => 'Cari tugas SDT…',
        ];
    } elseif (request()->routeIs('modul.index')) {
        $headerSearch = [
            'action' => route('modul.index'),
            'name' => 'q',
            'placeholder' => 'Cari modul…',
        ];
    } elseif (request()->routeIs('hakakses.index', 'admin.hakakses.modul.edit')) {
        $headerSearch = [
            'action' => url()->current(),
            'name' => 'q',
            'placeholder' => 'Cari hak akses / modul…',
        ];
    } elseif (request()->routeIs('pengguna.index')) {
        $headerSearch = [
            'action' => route('pengguna.index'),
            'name' => 'q',
            'placeholder' => 'Cari pengguna…',
        ];
    }
@endphp

<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light px-3">

        {{-- LEFT --}}
        
        <div class="d-flex align-items-center gap-2 flex-grow-1">
            <a href="#" class="nav-link d-none d-xl-block" id="sidebarMiniToggle" aria-label="Toggle sidebar">
                <i class="ti ti-layout-sidebar-left fs-5"></i>
            </a>

            {{-- Hamburger (mobile) --}}
            <a href="#" class="nav-link sidebartoggler d-xl-none">
                <i class="ti ti-menu-2 fs-5"></i>
            </a>

            {{-- SEARCH HEADER (TAMPILAN LAMA) --}}
            @if ($headerSearch)
                <form method="GET" action="{{ $headerSearch['action'] }}" class="header-search ms-2 flex-grow-1">
                    <div class="input-group header-search-group">

                        <span class="input-group-text">
                            <i class="ti ti-search"></i>
                        </span>

                        <input type="text" name="{{ $headerSearch['name'] }}" class="form-control"
                            placeholder="{{ $headerSearch['placeholder'] }}"
                            value="{{ request($headerSearch['name']) }}" autocomplete="off">

                        <button type="submit" class="btn btn-primary">
                            Cari
                        </button>

                    </div>
                </form>
            @endif
        </div>

        {{-- RIGHT --}}
        <ul class="navbar-nav ms-auto align-items-center">

            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                    <img src="{{ $foto }}" class="rounded-circle" width="35" height="35">
                    <span class="d-none d-md-inline small text-muted">
                        {{ $user->NAMA }}
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="ti ti-user"></i> My Profile
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile.change.password') }}" class="dropdown-item">
                            <i class="ti ti-lock"></i> Ganti Password
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="px-3">
                            @csrf
                            <button class="btn btn-outline-primary w-100">
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>

        </ul>
    </nav>
</header>
