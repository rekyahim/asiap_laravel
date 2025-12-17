@php
    $user = \App\Models\Pengguna::find(session('auth_uid'));
    $foto =
        $user && $user->ID_FOTO
            ? asset('assets/images/profile/' . $user->ID_FOTO)
            : asset('assets/images/profile/default.jpg');
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
        @if (request()->routeIs('sdt.index'))
            <li class="nav-item ms-2 header-search">
                <form id="header-sdt-search-form" method="GET" action="{{ route('sdt.index') }}" role="search"
                    class="m-0">
                    <div class="input-group header-search-group">
                        <span class="input-group-text">
                            <i class="ti ti-search"></i>
                        </span>

                        <input id="header-sdt-search-input" type="text" name="q" class="form-control"
                            placeholder="Cari SDT…" value="{{ request('q') }}" autocomplete="off"
                            aria-label="Cari SDT">

                        {{-- X boxed (muncul hanya saat ada teks) --}}
                        <button type="button" id="header-search-clear" class="btn-clear boxed d-none"
                            aria-label="Bersihkan">
                            <i class="ti ti-x"></i>
                        </button>

                        {{-- Tombol submit "Cari" (opsional) --}}
                        <button type="submit" id="header-search-submit"
                            class="btn btn-primary btn-submit">Cari</button>
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
