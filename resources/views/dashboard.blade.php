{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.admin') {{-- pakai layout yang sudah ada di proyek kamu --}}

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    @once
        {{-- Memuat Bootstrap Icons --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        {{-- Memuat Chart.js untuk Grafik --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endonce

    @push('styles')
        <style>
            :root {
                --brand: #5965e8;
                --brand-2: #7380ff;
                --border: #eef2f7;
                /* Warna-warna baru untuk kartu statistik dan grafik */
                --primary-soft: #f3f5ff;
                --primary-hard: #5965e8;
                --success-soft: #e6f8f0;
                --success-hard: #0ab39c;
                --warning-soft: #fff8e6;
                --warning-hard: #f0a60a;
                --danger-soft: #fdefef;
                --danger-hard: #f06548;
            }

            .shadow-soft {
                box-shadow: 0 .5rem 1.25rem rgba(18, 26, 41, .08) !important;
            }

            .card {
                border: 0;
                border-radius: 18px;
                background: #fff;
                height: 100%;
            }

            .card-header {
                border-bottom: 1px solid var(--border);
                background: #fff;
                padding: 1rem 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            /* Style baru untuk stat-card */
            .stat-card .icon {
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 14px;
                font-size: 1.3rem;
            }

            .stat-card .icon.icon-primary {
                background: var(--primary-soft);
                color: var(--primary-hard);
            }

            .stat-card .icon.icon-success {
                background: var(--success-soft);
                color: var(--success-hard);
            }

            .stat-card .icon.icon-warning {
                background: var(--warning-soft);
                color: var(--warning-hard);
            }

            .stat-card .icon.icon-danger {
                background: var(--danger-soft);
                color: var(--danger-hard);
            }

            /* Style shortcut tetap sama */
            .shortcut a {
                text-decoration: none;
            }

            .shortcut .item {
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 12px 14px;
                transition: .15s;
            }

            .shortcut .item:hover {
                transform: translateY(-2px);
                box-shadow: 0 .5rem 1.25rem rgba(18, 26, 41, .08);
            }

            code.slug {
                background: #f8f9fe;
                color: #6a6f86;
                padding: .1rem .3rem;
                border-radius: 6px;
            }

            /* Style untuk canvas chart agar responsif */
            .chart-container {
                position: relative;
                min-height: 300px;
                width: 100%;
            }
        </style>
    @endpush

    @php
        // --- AWAL DATA DUMMY ---
        // Sesuai permintaan Anda, semua data didefinisikan di sini.
        // Anda tidak perlu setup controller.

        // (1) Dummy User
        $user = (object) [
            'NAMA' => 'Admin',
        ];
        // Jika ingin tes kondisi "belum login", ganti $user menjadi:
        // $user = null;

        // (2) Dummy Statistik
        $stats = [
            'modul_aktif' => 8,
            'hakakses_aktif' => 4,
            'pengguna_aktif' => 120,
            'mapping_aktif' => 32,
        ];

        // (3) Dummy Shortcut
        $shortcuts = [
            ['slug' => 'kelola_modul', 'label' => 'Kelola Modul'],
            ['slug' => 'kelola_hakakses', 'label' => 'Kelola Hak Akses'],
            ['slug' => 'kelola_aksesmodul', 'label' => 'Mapping Akses Modul'],
            ['slug' => 'kelola_pengguna', 'label' => 'Manajemen Pengguna'],
            ['slug' => 'riwayat_petugas', 'label' => 'Riwayat Petugas'],
        ];

        // (4) Dummy Data untuk Route
        $routeMap = [
            'kelola_modul' => 'kelola_modul',
            'kelola_hakakses' => 'kelola_hakakses',
            'kelola_aksesmodul' => 'kelola_aksesmodul',
            'kelola_pengguna' => 'kelola_pengguna',
            'riwayat_petugas' => 'riwayat_petugas',
        ];
        $firstHakId = 1; // ID dummy untuk 'kelola_aksesmodul'

        // (5) Dummy Data untuk Grafik
        $userActivity = [
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'data' => [15, 22, 18, 25, 20, 30, 28],
        ];

        $roleDistribution = [
            'labels' => ['Administrator', 'Petugas', 'Manajemen', 'Tamu'],
            'data' => [5, 18, 7, 3],
            'colors' => ['#5965e8', '#0ab39c', '#f0a60a', '#6c757d'],
        ];
        // --- AKHIR DATA DUMMY ---
    @endphp

    <div class="container-fluid">
        {{-- KARTU SELAMAT DATANG --}}
        <div class="card shadow-soft mb-4">
            <div class="card-body d-flex justify-content-between align-items-center p-4">
                <div>
                    <h2 class="h5 mb-1">Selamat datang{{ $user ? ', ' . $user->NAMA : '' }}!</h2>
                    <div class="text-muted">Ini adalah ringkasan singkat aplikasi Anda.</div>
                </div>
                <div class="d-none d-md-block">
                    <img src="{{ asset('assets/images/illustrations/dashboard.svg') }}" alt="" height="72"
                        onerror="this.remove()">
                </div>
            </div>
        </div>

        {{-- KARTU STATISTIK --}}
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card shadow-soft">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon icon-primary"><i class="bi bi-puzzle-fill"></i></div>
                        <div>
                            <div class="text-muted small">Modul Aktif</div>
                            <div class="h5 mb-0">{{ $stats['modul_aktif'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card shadow-soft">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon icon-success"><i class="bi bi-shield-shaded"></i></div>
                        <div>
                            <div class="text-muted small">Hak Akses Aktif</div>
                            <div class="h5 mb-0">{{ $stats['hakakses_aktif'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card shadow-soft">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon icon-warning"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <div class="text-muted small">Pengguna Aktif</div>
                            <div class="h5 mb-0">{{ $stats['pengguna_aktif'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card shadow-soft">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon icon-danger"><i class="bi bi-link"></i></div>
                        <div>
                            <div class="text-muted small">Mapping Aktif</div>
                            <div class="h5 mb-0">{{ $stats['mapping_aktif'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card shadow-soft">
                    <div class="card-header">
                        <h6 class="mb-0">Aktivitas Login (7 Hari Terakhir)</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="userActivityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-soft">
                    <div class="card-header">
                        <h6 class="mb-0">Distribusi Pengguna</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="min-height: 300px; max-height: 340px; margin: auto;">
                            <canvas id="roleDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- SHORTCUT MODUL (hanya jika login) --}}
        <div class="card shadow-soft mt-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0">Shortcut Modul</h6>
                @if (!$user)
                    <span class="small text-muted">Login untuk melihat akses modul Anda.</span>
                @endif
            </div>
            <div class="card-body">
                @if ($user && isset($shortcuts) && count($shortcuts))
                    <div class="row g-3 shortcut">
                        @foreach ($shortcuts as $s)
                            @php
                                $slug = $s['slug'];
                                // default link (dummy)
                                $href = '#';

                                // Cek apakah route ada di map
                                if (isset($routeMap[$slug])) {
                                    try {
                                        if ($slug === 'kelola_aksesmodul') {
                                            // butuh ID → arahkan ke default
                                            $href = $firstHakId
                                                ? route($routeMap[$slug], $firstHakId)
                                                : route('kelola_hakakses');
                                        } else {
                                            $href = route($routeMap[$slug]);
                                        }
                                    } catch (\Exception $e) {
                                        // Jika route() gagal (karena route-nya tidak ada), biarkan href = '#'
                                        $href = '#';
                                    }
                                }
                            @endphp
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <a href="{{ $href }}">
                                    <div class="item d-flex align-items-center justify-content-between">
                                        <div class="me-3">
                                            <div class="fw-semibold">{{ $s['label'] }}</div>
                                            <div class="small text-muted">route: <code
                                                    class="slug">{{ $slug }}</code></div>
                                        </div>
                                        <i class="bi bi-arrow-right-circle fs-5 text-primary"></i>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @elseif($user)
                    <div class="text-muted">Hak akses Anda belum memiliki modul aktif.</div>
                @else
                    <div class="text-muted">Silakan login untuk melihat modul yang tersedia.</div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Menunggu DOM siap
        document.addEventListener('DOMContentLoaded', function() {

            // Helper untuk mengambil variabel CSS
            const getCssVar = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();

            // Data dari PHP (dikonversi ke JSON)
            const userActivityData = @json($userActivity);
            const roleDistributionData = @json($roleDistribution);

            // 1. Grafik Aktivitas Pengguna (Line Chart)
            const ctxLine = document.getElementById('userActivityChart');
            if (ctxLine) {
                new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: userActivityData.labels,
                        datasets: [{
                            label: 'Aktivitas',
                            data: userActivityData.data,
                            fill: true,
                            backgroundColor: 'rgba(89, 101, 232, 0.1)', // var(--primary-soft)
                            borderColor: getCssVar('--primary-hard'), // var(--primary-hard)
                            tension: 0.3,
                            pointBackgroundColor: getCssVar('--primary-hard'),
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                titleColor: '#333',
                                bodyColor: '#666',
                                borderColor: getCssVar('--border'),
                                borderWidth: 1,
                                boxPadding: 8,
                                intersect: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: getCssVar('--border'),
                                    drawBorder: false,
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // 2. Grafik Distribusi Peran (Doughnut Chart)
            const ctxDoughnut = document.getElementById('roleDistributionChart');
            if (ctxDoughnut) {
                new Chart(ctxDoughnut, {
                    type: 'doughnut',
                    data: {
                        labels: roleDistributionData.labels,
                        datasets: [{
                            label: 'Jumlah Pengguna',
                            data: roleDistributionData.data,
                            backgroundColor: roleDistributionData.colors || [
                                getCssVar('--primary-hard'),
                                getCssVar('--success-hard'),
                                getCssVar('--warning-hard'),
                                getCssVar('--danger-hard'),
                                '#6c757d'
                            ],
                            borderColor: '#fff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                titleColor: '#333',
                                bodyColor: '#666',
                                borderColor: getCssVar('--border'),
                                borderWidth: 1,
                                boxPadding: 8,
                            }
                        }
                    }
                });
            }

        }); // Akhir document.addEventListener
    </script>
@endpush
