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
    <nav class="navbar navbar-expand-lg navbar-light">

        <ul class="navbar-nav align-items-center">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="#"><i
                        class="ti ti-menu-2"></i></a>
            </li>
        </ul>

        {{-- Search SDT di header (hanya di halaman index SDT) --}}
        @if ($headerSearch)
            <li class="nav-item ms-2 header-search">
                <form method="GET" action="{{ $headerSearch['action'] }}" class="m-0">
                    <div class="input-group header-search-group">
                        <span class="input-group-text">
                            <i class="ti ti-search"></i>
                        </span>

                        <input type="text" name="{{ $headerSearch['name'] }}" class="form-control"
                            placeholder="{{ $headerSearch['placeholder'] }}"
                            value="{{ request($headerSearch['name']) }}" autocomplete="off">

                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </form>
            </li>
        @endif
        </ul>


        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center">

                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover d-flex gap-2 align-items-center" href="#"
                        data-bs-toggle="dropdown">
                        <img src="{{ $foto }}" class="rounded-circle" width="35" height="35">
                        <span class="small text-muted">{{ $user->NAMA }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up">
                        <li><a href="{{ route('profile.show') }}" class="dropdown-item"><i class="ti ti-user"></i> My
                                Profile</a></li>
                        <li><a href="{{ route('profile.change.password') }}" class="dropdown-item"><i
                                    class="ti ti-lock"></i> Ganti Password</a></li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="px-3">@csrf
                                <button class="btn btn-outline-primary w-100">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</header>
