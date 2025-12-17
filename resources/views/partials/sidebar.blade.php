<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                <img src="{{ asset('assets/images/logos/ic_bapenda_nobg.png') }}" width="200" alt="ASIAP SDT" />
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>

        @php
            use Illuminate\Support\Facades\DB;

            /* ============================================================
                Helper: Highlight based on route patterns
            ============================================================ */
            if (!function_exists('sidebar_is_active')) {
                function sidebar_is_active(string $patterns): string
                {
                    foreach (explode('|', $patterns) as $p) {
                        if (request()->routeIs(trim($p))) {
                            return 'active';
                        }
                    }
                    return '';
                }
            }

            /* ============================================================
                Helper: Check if user role can access module
            ============================================================ */
            if (!function_exists('sidebar_can_modul')) {
                function sidebar_can_modul(string $slug): bool
                {
                    $roleId = (int) session('auth_role');
                    if (!$roleId) {
                        return false;
                    }

                    // Super Admin bypass semua modul
                    $isSuper = DB::table('hak_akses')
                        ->where('ID', $roleId)
                        ->where('STATUS', 1)
                        ->whereRaw("LOWER(HAKAKSES) LIKE '%super admin%'")
                        ->exists();
                    if ($isSuper) {
                        return true;
                    }

                    // Periksa pivot hakakses_modul
                    return DB::table('hakakses_modul as hm')
                        ->join('modul as m', 'm.ID', '=', 'hm.MODUL_ID')
                        ->where('hm.HAKAKSES_ID', $roleId)
                        ->where('hm.STATUS', 1)
                        ->where('m.STATUS', 1)
                        ->where(function ($q) use ($slug) {
                            $q->where('m.LOKASI_MODUL', $slug)->orWhere('m.LOKASI_MODUL', 'like', $slug . '%');
                        })
                        ->exists();
                }
            }

            /* ============================================================
                Semua menu utama
            ============================================================ */
            $menu = [
                [
                    'slug' => 'kelola_modul',
                    'route' => 'modul.index',
                    'label' => 'Manajemen Modul',
                    'icon' => 'ti ti-puzzle',
                    'active' => 'modul.*',
                ],
                [
                    'slug' => 'kelola_hakakses',
                    'route' => 'hakakses.index',
                    'label' => 'Hak Akses',
                    'icon' => 'ti ti-shield-lock',
                    'active' => 'hakakses.*',
                ],
                [
                    'slug' => 'kelola_aksesmodul',
                    'route' => 'admin.hakakses.modul.index',
                    'label' => 'Hak Akses ↔ Modul',
                    'icon' => 'ti ti-link',
                    'active' => 'admin.hakakses.modul.*',
                ],
                [
                    'slug' => 'kelola_pengguna',
                    'route' => 'pengguna.index',
                    'label' => 'Pengguna',
                    'icon' => 'ti ti-users',
                    'active' => 'pengguna.*',
                ],
                [
                    'slug' => 'kelola_sdt',
                    'route' => 'sdt.index',
                    'label' => 'Daftar SDT',
                    'icon' => 'ti ti-layout-list',
                    'active' => 'sdt.*',
                ],
                [
                    'slug' => 'riwayat_petugas',
                    'route' => 'riwayat.petugas',
                    'label' => 'Riwayat SDT Petugas',
                    'icon' => 'ti ti-history',
                    'active' => 'riwayat.petugas.*',
                ],
                [
                    'slug' => 'sdt_petugas',
                    'route' => 'petugas.sdt.index',
                    'label' => 'SDT Petugas',
                    'icon' => 'ti ti-list-check',
                    'active' => 'petugas.sdt.*',
                ],
                [
                    'slug' => 'kelola_log',
                    'route' => 'admin.log.index',
                    'label' => 'Riwayat Aktivitas',
                    'icon' => 'ti ti-file-analytics',
                    'active' => 'log.*',
                ],
            ];

            // Filter menu yg user boleh akses
            $menu = array_values(array_filter($menu, fn($m) => sidebar_can_modul($m['slug'])));

            /* ============================================================
                NEW: Override highlight manual
            ============================================================ */
            $CURRENT_MENU = $forceMenu ?? ($activeMenu ?? null);
        @endphp

        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Menu</span>
                </li>

                {{-- Dashboard --}}
                <li class="sidebar-item">
                    <a class="sidebar-link {{ sidebar_is_active('dashboard') }}" href="{{ route('dashboard') }}">
                        <span><i class="ti ti-home"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                {{-- Menu utama --}}
                @foreach ($menu as $m)
                    @php
                        $isForced = $CURRENT_MENU === $m['slug'];

                        if ($CURRENT_MENU) {
                            // Jika forced menu aktif → abaikan sidebar_is_active()
                            $activeClass = $isForced ? 'active' : '';
                        } else {
                            // Normal (tanpa override)
                            $activeClass = sidebar_is_active($m['active']);
                        }

                    @endphp

                    <li class="sidebar-item">
                        <a class="sidebar-link {{ $activeClass }}" href="{{ route($m['route']) }}">
                            <span><i class="{{ $m['icon'] }}"></i></span>
                            <span class="hide-menu">{{ $m['label'] }}</span>
                        </a>
                    </li>
                @endforeach

            </ul>
        </nav>

    </div>
</aside>
