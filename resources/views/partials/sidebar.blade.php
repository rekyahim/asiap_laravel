<aside class="left-sidebar collapsed" id="appSidebar">
    <div>

        {{-- LOGO --}}
        <div class="brand-logo d-flex align-items-center justify-content-between px-3">
            <a href="{{ route('dashboard') }}" class="logo-img">
                <img src="{{ asset('assets/images/logos/ic_bapenda.png') }}" width="200" alt="ASIAP SDT">
            </a>

            {{-- BACK BUTTON (mobile only) --}}
            <a href="#" class="sidebartoggler sidebar-back-btn d-xl-none" aria-label="Tutup sidebar">
                <i class="ti ti-chevron-left"></i>
            </a>
        </div>

        <body class="sidebar-mini">

            @php
                use Illuminate\Support\Facades\DB;

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

                if (!function_exists('sidebar_can_modul')) {
                    function sidebar_can_modul(string $slug): bool
                    {
                        $roleId = (int) session('auth_role');
                        if (!$roleId) {
                            return false;
                        }

                        $isSuper = DB::table('hak_akses')
                            ->where('ID', $roleId)
                            ->where('STATUS', 1)
                            ->whereRaw("LOWER(HAKAKSES) LIKE '%super admin%'")
                            ->exists();

                        if ($isSuper) {
                            return true;
                        }

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

                $menu = array_values(array_filter($menu, fn($m) => sidebar_can_modul($m['slug'])));
                $CURRENT_MENU = $forceMenu ?? ($activeMenu ?? null);
            @endphp

            <nav class="sidebar-nav scroll-sidebar">
                <ul class="pt-3">

                    <li class="nav-small-cap text-muted px-3 mb-2">MENU</li>

                    <li class="sidebar-item">
                        <a class="sidebar-link {{ sidebar_is_active('dashboard') }}" href="{{ route('dashboard') }}">
                            <i class="ti ti-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    @foreach ($menu as $m)
                        @php
                            $activeClass = $CURRENT_MENU
                                ? ($CURRENT_MENU === $m['slug']
                                    ? 'active'
                                    : '')
                                : sidebar_is_active($m['active']);
                        @endphp

                        <li class="sidebar-item">
                            <a class="sidebar-link {{ $activeClass }}" href="{{ route($m['route']) }}">
                                <i class="{{ $m['icon'] }}"></i>
                                <span>{{ $m['label'] }}</span>
                            </a>
                        </li>
                    @endforeach

                </ul>
            </nav>

    </div>
</aside>

{{-- OVERLAY --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const body = document.body;
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const backBtn = document.querySelector('.sidebar-back-btn');

        /* ===============================
           OFFCANVAS – MOBILE
        =============================== */
        const openSidebar = () => {
            sidebar.classList.remove('collapsed');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            if (backBtn) backBtn.style.display = 'flex';
        };

        const closeSidebar = () => {
            sidebar.classList.add('collapsed');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            if (backBtn) backBtn.style.display = 'none';
        };

        // tombol hamburger & back (mobile)
        document.querySelectorAll('.sidebartoggler').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();

                // desktop: jangan pakai offcanvas
                if (window.innerWidth >= 1200) return;

                sidebar.classList.contains('collapsed') ?
                    openSidebar() :
                    closeSidebar();
            });
        });

        // overlay click
        overlay.addEventListener('click', closeSidebar);

        // klik menu → auto close di mobile
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1200) closeSidebar();
            });
        });

        // resize ke desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1200) closeSidebar();
        });

        /* ===============================
           MINI SIDEBAR – DESKTOP
           (DELAYED BINDING)
        =============================== */
        const initMiniSidebar = () => {
            const miniBtn = document.getElementById('sidebarMiniToggle');
            if (!miniBtn) {
                console.warn('sidebarMiniToggle NOT FOUND');
                return;
            }

            // restore
            if (localStorage.getItem('sidebar-mini') === '1') {
                body.classList.add('sidebar-mini');
            }

            miniBtn.addEventListener('click', e => {
                e.preventDefault();
                body.classList.toggle('sidebar-mini');

                localStorage.setItem(
                    'sidebar-mini',
                    body.classList.contains('sidebar-mini') ? '1' : '0'
                );
            });
        };

        // delay binding (header rendered after sidebar)
        setTimeout(initMiniSidebar, 0);
    });
</script>
