@extends('layouts.admin')

@section('title', 'Petugas / Detail SDT')
@section('breadcrumb', 'Petugas / Detail SDT')

@section('content')

    @php
        $hasFilter = request()->filled('nop') || request()->filled('tahun') || request()->filled('nama');
        $progress = $summary['progress'] ?? 0;
        $progressFmt = rtrim(rtrim(number_format($progress, 2, '.', ''), '0'), '.');
    @endphp

    <style>
        /* =====================================================================
           1. DESIGN TOKENS & SCALING
           ===================================================================== */
        :root {
            --bg: #f5f7fb;
            --card: #fff;
            --line: #e6e8ec;
            --text: #0f172a;
            --muted: #64748b;
            --accent: #2563eb;
            --accent-2: #1d4ed8;
            --ok: #16a34a;
            --ok-2: #34d399;
            --warn: #f59e0b;
            --info: #06b6d4;
            --r-lg: 18px;
            --shadow: 0 10px 26px rgba(2, 6, 23, .10);
            --px: clamp(12px, 2vw, 20px);
            --py: clamp(10px, 1.4vw, 16px);
            --th: clamp(9px, 1.2vw, 12px);
            --td: clamp(10px, 1.3vw, 14px);
        }

        /* =====================================================================
           2. LAYOUT & CARD STYLES
           ===================================================================== */
        .page-sdt-detail {
            margin-top: 4px;
            background: var(--bg);
            border-radius: 22px;
            padding: var(--px);
        }

        .card-clean {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-clean .card-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: var(--py) 26px;
            border-bottom: 1px solid var(--line);
            background: radial-gradient(80% 140% at 100% 0%, rgba(37, 99, 235, .05) 0%, #fff 60%), linear-gradient(180deg, #fff, #f8fafc);
        }

        .page-title {
            margin: 0;
            color: var(--text);
            font-weight: 800;
            letter-spacing: .2px;
            font-size: clamp(1rem, 1.1vw + .85rem, 1.25rem);
        }

        /* =====================================================================
           3. BUTTONS
           ===================================================================== */
        .btn-ghost {
            background: #fff;
            border: 1px solid var(--line);
            padding: .4rem .8rem;
            border-radius: 8px;
            font-weight: 600;
            color: var(--muted);
            transition: .2s;
            font-size: .8rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-ghost:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: var(--text);
        }

        .btn-blue {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: .4rem .8rem;
            font-weight: 600;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: .2s;
            box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
        }

        .btn-blue:hover,
        .btn-blue:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .btn-disabled {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
            border-radius: 8px;
            padding: .4rem .8rem;
            font-weight: 600;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn-compact {
            font-size: .7rem;
            padding: .25rem .5rem;
        }

        /* =====================================================================
           4. KPI DASHBOARD GRID
           ===================================================================== */
        .kpis {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
        }

        .kpi {
            background: #fff;
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid var(--line);
            transition: .2s;
        }

        .kpi:hover {
            border-color: var(--accent);
        }

        .kpi .t {
            color: var(--muted);
            font-size: .75rem;
            margin-bottom: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kpi .v {
            color: var(--text);
            font-weight: 800;
            font-size: 1.1rem;
        }

        .kpi .bar {
            height: 6px;
            background: #eff6ff;
            border-radius: 999px;
            margin-top: 8px;
            overflow: hidden;
        }

        .kpi .bar>i {
            height: 100%;
            background: var(--accent);
            display: block;
            border-radius: 999px;
        }

        /* =====================================================================
           5. TABLE STYLES (DEFAULT / DESKTOP)
           ===================================================================== */
        .table-wrap {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            overflow: auto;
            width: 100%;
        }

        .tbl {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        /* --- Setting Lebar Kolom Desktop --- */
        .col-no {
            width: 50px;
        }

        .col-nop {
            width: 220px;
        }

        .col-nama {
            width: 200px;
        }

        .col-date {
            width: 100px;
        }

        .col-status {
            width: 100px;
        }

        .col-aksi {
            width: 110px;
        }

        .tbl thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: linear-gradient(180deg, #f8fafc, #eef2ff);
            color: var(--text);
            border-bottom: 1px solid var(--line);
            font-weight: 800;
            letter-spacing: .2px;
            text-transform: uppercase;
            padding: var(--th) calc(var(--th) + 2px);
            font-size: clamp(.78rem, .9vw, .84rem);
            text-align: left;
        }

        .tbl thead th.col-no,
        .tbl thead th.col-nop {
            text-align: right;
        }

        .tbl thead th.col-date,
        .tbl thead th.col-status,
        .tbl thead th.col-prog,
        .tbl thead th.col-aksi {
            text-align: center;
        }

        .tbl tbody td {
            padding: var(--td) calc(var(--td) + 2px);
            border-bottom: 1px solid var(--line);
            color: #334155;
            font-size: clamp(.82rem, 1vw, .95rem);
            line-height: 1.4;
            white-space: normal;
            vertical-align: top;
            word-wrap: break-word;
        }

        .tbl tbody td.col-aksi {
            vertical-align: middle;
        }

        .tbl tbody td.col-no,
        .tbl tbody td.col-nop {
            text-align: right;
        }

        .tbl tbody td.col-date,
        .tbl tbody td.col-status,
        .tbl tbody td.col-prog,
        .tbl tbody td.col-aksi {
            text-align: center;
        }

        /* NOP Desktop: Satu Baris (Nowrap) */
        .tbl tbody td.col-nop {
            white-space: nowrap !important;
            font-family: 'Consolas', 'Monaco', monospace;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .tbl tbody tr:nth-child(even) {
            background: #fcfdff;
        }

        .tbl tbody tr:hover {
            background: #f6f8ff;
        }

        .mono {
            font-family: ui-monospace, Menlo, monospace;
        }

        /* =====================================================================
           6. COMPONENTS (CHIPS & BADGES)
           ===================================================================== */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .3rem .62rem;
            border-radius: 999px;
            border: 1px solid var(--line);
            font-size: .78rem;
            font-weight: 700;
            background: #f3f4f6;
            color: #334155;
            white-space: nowrap;
        }

        .chip .dot {
            width: .44rem;
            height: .44rem;
            border-radius: 999px;
            background: #9ca3af;
        }

        .badge-soft {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            display: inline-block;
            white-space: normal;
            text-align: center;
            line-height: 1.3;
        }

        .bg-soft-blue {
            background: #eff6ff;
            color: var(--accent);
        }

        .bg-soft-green {
            background: #f0fdf4;
            color: var(--ok);
        }

        .bg-soft-red {
            background: #fef2f2;
            color: #b91c1c;
        }

        .bg-soft-gray {
            background: #f3f4f6;
            color: var(--muted);
        }

        .td-actions {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        /* =====================================================================
           7. MOBILE RESPONSIVE FIXES (CARD VIEW)
           ===================================================================== */
        @media (max-width: 768px) {
            .page-sdt-detail {
                margin-top: 0;
                padding: 15px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .card-header .d-flex {
                width: 100%;
                display: grid !important;
                grid-template-columns: 1fr 1fr;
            }

            .btn-ghost,
            .btn-blue,
            .btn-disabled {
                justify-content: center;
                width: 100%;
            }

            /* Reset Table untuk Mobile */
            .table-wrap {
                border: none;
                background: transparent;
                overflow: visible;
            }

            .tbl,
            .tbl thead,
            .tbl tbody,
            .tbl tbody tr,
            .tbl tbody td {
                display: block;
            }

            .tbl thead {
                display: none;
            }

            .tbl tbody tr {
                margin-bottom: 15px;
                background: #fff;
                border: 1px solid var(--line);
                border-radius: 16px;
                padding: 16px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            }

            /* --- Layout Baris Tabel di Mobile --- */
            .tbl tbody td {
                display: flex;
                justify-content: space-between;
                /* Label Kiri, Isi Kanan */
                align-items: flex-start;
                /* Rata atas biar rapi */
                text-align: right !important;
                padding: 8px 0;
                border-bottom: 1px dashed #f1f5f9;
                width: 100% !important;
                box-sizing: border-box;
                height: auto;
                white-space: normal;
                /* Global wrap enabled */
                gap: 15px;
                /* Jarak antara Label dan Isi */
            }

            .tbl tbody td:last-child {
                border-bottom: none;
                padding-top: 15px;
                justify-content: center;
                margin-top: 5px;
            }

            /* Label (Kolom Kiri) */
            .tbl tbody td::before {
                content: attr(data-label);
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                color: var(--muted);
                text-align: left;
                min-width: 90px;
                flex-shrink: 0;
                margin-top: 3px;
            }

            /* --- FIX KHUSUS NOP DI MOBILE (FINAL) ---
               1. overflow-wrap: anywhere; (Pecah string panjang tanpa spasi seperti NOP)
               2. Hapus max-width agar tidak dipaksa sempit
               3. Gunakan font clamp agar responsif
            */
            .tbl tbody td[data-label="NOP"] {
                white-space: normal !important;
                overflow-wrap: anywhere;
                /* KUNCI PERBAIKAN */
                word-break: break-word;
                /* Fallback */
                font-family: 'Consolas', 'Monaco', monospace;
                font-size: clamp(0.75rem, 4vw, 0.9rem) !important;
                letter-spacing: -0.3px;
                text-align: right;
                width: 100% !important;
                /* Gunakan lebar penuh */
                max-width: 100% !important;
                /* Reset batasan lama */
            }

            /* --- BADGE STATUS --- */
            .tbl tbody td .badge-soft {
                white-space: normal !important;
                text-align: right;
                display: inline-block;
                max-width: 100%;
                line-height: 1.4;
            }

            /* --- NAMA WP --- */
            .col-nama {
                font-weight: 700;
                color: var(--accent);
                font-size: 1rem !important;
                border-bottom: 2px solid #f1f5f9 !important;
                margin-bottom: 5px;
                padding-bottom: 12px !important;
                display: block !important;
                text-align: left !important;
            }

            .col-nama::before {
                display: none;
            }

            /* --- PAGINATION --- */
            div.dataTables_wrapper div.dataTables_length,
            div.dataTables_wrapper div.dataTables_filter,
            div.dataTables_wrapper div.dataTables_info,
            div.dataTables_wrapper div.dataTables_paginate {
                text-align: center;
                justify-content: center;
            }

            div.dataTables_wrapper div.dataTables_info {
                white-space: normal;
                margin-bottom: 12px;
                font-size: 0.85rem;
                color: var(--muted);
            }

            div.dataTables_wrapper div.dataTables_paginate {
                margin-top: 5px !important;
                display: flex;
                justify-content: center !important;
            }

            div.dataTables_wrapper div.dataTables_paginate ul.pagination {
                flex-wrap: wrap !important;
                justify-content: center !important;
                margin: 0;
                gap: 5px;
            }

            div.dataTables_wrapper div.dataTables_paginate ul.pagination li.page-item .page-link {
                border-radius: 6px;
                padding: 0.4rem 0.75rem;
                font-size: 0.85rem;
                margin: 0 1px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0;
            margin-left: 2px;
        }

        div.dataTables_wrapper div.dataTables_filter {
            text-align: right;
            margin-bottom: 10px;
        }

        .dataTables_length select {
            border-radius: 8px;
            border: 1px solid var(--line);
        }

        .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid var(--line);
            padding: 5px 10px;
        }
    </style>
    <div class="page-sdt-detail">
        <div class="card-clean">

            {{-- HEADER --}}
            <div class="card-header">
                <div>
                    <h5 class="page-title">Detail SDT</h5>
                    <small class="text-muted">{{ $sdt->NAMA_SDT }}</small>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    @if ($hasFilter)
                        <a href="{{ route('petugas.sdt.detail', $sdt->ID) }}" class="btn-ghost">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                    <a href="{{ request()->get('back', url()->previous()) }}" class="btn-ghost">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>

                    <button type="button" class="btn-blue" data-bs-toggle="modal" data-bs-target="#modalMassKO">
                        <i class="bi bi-geo-alt"></i> Massal KO
                    </button>
                    <button type="button" class="btn-blue" data-bs-toggle="modal" data-bs-target="#modalMassNOP">
                        <i class="bi bi-pencil-square"></i> Massal NOP
                    </button>
                </div>
            </div>

            <div class="card-body p-4">

                {{-- Pesan Sukses --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Pesan Error --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                {{-- KPI Section --}}
                <div class="kpis">
                    <div class="kpi-row">
                        <div class="kpi">
                            <div class="t">Total SDT</div>
                            <div class="v">{{ number_format($summary['total']) }}</div>
                        </div>
                        <div class="kpi">
                            <div class="t">Sudah Diproses</div>
                            <div class="v" style="color:var(--ok)">{{ number_format($summary['tersampaikan']) }}</div>
                        </div>
                        <div class="kpi">
                            <div class="t">Belum</div>
                            <div class="v" style="color:var(--danger)">{{ number_format($summary['belum']) }}</div>
                        </div>
                    </div>

                    <div class="kpi-row">
                        <div class="kpi" style="grid-column: span 2 / auto;">
                            <div class="t">Progress</div>
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="v">{{ $progressFmt }}%</div>
                            </div>
                            <div class="bar"><i style="width:{{ $progress }}%"></i></div>
                        </div>
                        <div class="kpi">
                            <div class="t">Potensi Pajak</div>
                            <div class="v text-primary">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                {{-- SEARCH FILTER --}}
                <div class="card-clean mb-4">
                    <form method="GET" action="{{ route('petugas.sdt.detail', $sdt->ID) }}">
                        <div class="row g-3 p-3 align-items-end">

                            {{-- Satu input untuk NOP / Nama WP --}}
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Cari NOP / Nama WP</label>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                    placeholder="Masukkan NOP atau Nama WP">
                            </div>

                            {{-- Tombol Cari & Reset --}}
                            <div class="col-md-4 d-flex gap-2">
                                <button type="submit" class="btn-blue w-auto">
                                    <i class="bi bi-search"></i> Cari
                                </button>

                                @if (request()->filled('search'))
                                    <a href="{{ route('petugas.sdt.detail', $sdt->ID) }}" class="btn-ghost w-auto">
                                        <i class="bi bi-x-circle"></i> Reset
                                    </a>
                                @endif
                            </div>

                        </div>
                    </form>
                </div>




                {{-- Table Section --}}
                <div class="table-wrap p-3">
                    <table id="tableSdt" class="tbl" style="width:100%">
                        <thead>
                            <tr>
                                <th class="col-no">No</th>
                                <th width="200px">NOP</th>
                                <th>Tahun</th>
                                <th width="110px">Nama WP</th>
                                <th width="120px">Penyampaian</th>
                                <th>Status OP</th>
                                <th>Status WP</th>
                                <th class="text-end" width="80px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody> {{-- BIARKAN KOSONG --}}
                    </table>
                </div>



            </div>
        </div>
    </div>

    {{-- Include Modals --}}
    @include('petugas.partials.mass-ko')
    @include('petugas.partials.mass-nop')
    @include('petugas.partials.modal-camera')

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Script tambahan
        });
    </script>
@endpush
@push('scripts')
    {{-- Pastikan urutan load: jQuery -> DataTables JS -> DataTables BS5 JS --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#tableSdt').DataTable({
                processing: true,
                serverSide: true, // INI KUNCINYA AGAR RINGAN
                ajax: {
                    url: "{{ route('petugas.sdt.detail', $sdt->ID) }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                pageLength: 5,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'NOP',
                        name: 'NOP',
                        className: 'mono fw-bold text-end'
                    },
                    {
                        data: 'TAHUN',
                        name: 'TAHUN',
                        className: 'text-center'
                    },
                    {
                        data: 'NAMA_WP',
                        name: 'NAMA_WP',
                        defaultContent: '—'
                    },
                    {
                        data: 'status_penyampaian',
                        name: 'latestStatus.STATUS_PENYAMPAIAN'
                    }, // Mengarah ke relasi
                    {
                        data: 'status_op_html',
                        name: 'latestStatus.STATUS_OP'
                    },
                    {
                        data: 'status_wp_html',
                        name: 'latestStatus.STATUS_WP'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-end'
                    }
                ],
                // Konfigurasi Bahasa Indonesia
                language: {
                    search: "Cari NOP/Nama:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "&rarr;",
                        previous: "&larr;"
                    }
                },
                // Layout Filter
                dom: '<"d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2"lf>rt<"d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2"ip>',

                // ============================================================
                // MAGIS AGAR TAMPILAN MOBILE CARD TETAP JALAN
                // ============================================================
                createdRow: function(row, data, dataIndex) {
                    // Kita inject attr data-label agar CSS Mobile Card membacanya
                    $('td', row).eq(0).attr('data-label', 'No');
                    $('td', row).eq(1).attr('data-label', 'NOP');
                    $('td', row).eq(2).attr('data-label', 'Tahun');
                    $('td', row).eq(3).attr('data-label', 'Nama WP').addClass(
                        'col-nama'); // Class khusus nama
                    $('td', row).eq(4).attr('data-label', 'Penyampaian');
                    $('td', row).eq(5).attr('data-label', 'Status OP');
                    $('td', row).eq(6).attr('data-label', 'Status WP');
                    // Kolom aksi tidak butuh label
                }
            });
        });
    </script>
@endpush
