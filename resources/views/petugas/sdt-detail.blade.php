@extends('layouts.admin')

@section('title', 'Petugas / Detail SDT')
@section('breadcrumb', 'Petugas / Detail SDT')

@push('styles')
    <style>
        :root {
            --bg: #f5f7fa;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --line: #e5e7eb;
            --accent: #3b82f6;
            --accent-2: #1d4ed8;
            --ok: #16a34a;
            --danger: #ef4444;
            --radius: 16px;
            --radius-sm: 8px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        /* CONTAINER */
        .page-sdt-detail {
            background: var(--bg);
            border-radius: var(--radius);
            padding: 24px;
            margin-top: 15px;
        }

        /* CARD CLEAN */
        .card-clean {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--line);
        }

        /* HEADER */
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }

        .page-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text);
            margin: 0;
        }

        /* BUTTONS */
        .btn-ghost {
            background: #fff;
            border: 1px solid var(--line);
            padding: .4rem .8rem;
            border-radius: var(--radius-sm);
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
            border-radius: var(--radius-sm);
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

        .btn-compact {
            font-size: .7rem;
            padding: .25rem .5rem;
        }

        /* KPI GRID */
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

        /* TABLE BASE STYLES */
        .table-wrap {
            border-radius: var(--radius-sm);
            background: #fff;
            margin-top: 0;
            border: 1px solid var(--line);
            width: 100%;
        }

        table.tbl {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #f8fafc;
            padding: 12px 16px;
            font-weight: 700;
            font-size: .75rem;
            color: var(--muted);
            text-transform: uppercase;
            border-bottom: 1px solid var(--line);
            text-align: left;
        }

        tbody td {
            padding: 12px 16px;
            font-size: .8rem;
            color: var(--text);
            text-align: left;
            border-bottom: 1px solid var(--line);
            vertical-align: middle;
        }

        .badge-soft {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: 0.3px;
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
            color: var(--danger);
        }

        .bg-soft-gray {
            background: #f3f4f6;
            color: var(--muted);
        }

        .td-actions {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }

        /* =========================================
                                                                                                                                               MOBILE CARD VIEW TRANSFORMATION (MAGIC)
                                                                                                                                               ========================================= */
        @media (max-width: 768px) {
            .page-sdt-detail {
                padding: 15px;
                margin-top: 10px;
            }

            /* Header Stack */
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
            .btn-blue {
                justify-content: center;
                width: 100%;
            }

            /* HIDE Table Header */
            .tbl thead {
                display: none;
            }

            /* Table Row jadi Card */
            .tbl tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid var(--line);
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
                padding: 10px;
            }

            /* Table Cell jadi Baris dalam Card */
            .tbl tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 10px 5px;
                border-bottom: 1px dashed #eee;
                font-size: 0.85rem;
            }

            .tbl tbody td:last-child {
                border-bottom: none;
                padding-top: 15px;
                justify-content: center;
                /* Tombol aksi di tengah */
            }

            /* CSS Trick: Ambil Judul dari atribut data-label */
            .tbl tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                color: var(--muted);
                text-transform: uppercase;
                font-size: 0.7rem;
                text-align: left;
                margin-right: 15px;
            }

            /* Styling khusus kolom tertentu di mobile */
            .td-nop {
                font-weight: bold;
                color: var(--accent);
                font-family: monospace;
                font-size: 1rem !important;
            }

            .table-wrap {
                border: none;
                background: transparent;
            }
        }
    </style>
@endpush

@section('content')

    @php
        $hasFilter = request()->filled('nop') || request()->filled('tahun') || request()->filled('nama');
        $progress = $summary['progress'] ?? 0;
        $progressFmt = rtrim(rtrim(number_format($progress, 2, '.', ''), '0'), '.');
    @endphp

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

                {{-- KPI Section --}}
                <div class="kpis">
                    <div class="kpi-row">
                        <div class="kpi">
                            <div class="t">Total NOP</div>
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
                            <div class="t">Potensi Biaya</div>
                            <div class="v text-primary">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Table Section --}}
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align:center;">No</th>
                                <th>NOP</th>
                                <th style="text-align:center;">Tahun</th>
                                <th>Nama WP</th>
                                <th>Status Penyampaian</th>
                                <th>Status OP</th>
                                <th>Status WP</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $i => $r)
                                @php
                                    $status = $r->latestStatus->STATUS_PENYAMPAIAN ?? null;

                                    if ($status == '1') {
                                        $badgePeny =
                                            '<span class="badge-soft bg-soft-green"><i class="bi bi-check-circle me-1"></i> TERSAMPAIKAN</span>';
                                    } elseif ($status == '0' || $status === 0) {
                                        $badgePeny =
                                            '<span class="badge-soft bg-soft-red"><i class="bi bi-x-circle me-1"></i> TIDAK TERSAMPAIKAN</span>';
                                    } else {
                                        $badgePeny = '<span class="badge-soft bg-soft-gray">BELUM DIPROSES</span>';
                                    }

                                    $statusOP = $r->latestStatus->STATUS_OP ?? null;
                                    $statusWP = $r->latestStatus->STATUS_WP ?? null;

                                    if ($statusWP == 1 || $statusWP === '1') {
                                        $statusWP = 'Belum Diproses Petugas';
                                    } elseif ($statusWP == 2 || $statusWP === '2') {
                                        $statusWP = 'Ditemukan';
                                    } elseif ($statusWP == 3 || $statusWP === '3') {
                                        $statusWP = 'Tidak Ditemukan';
                                    } elseif ($statusWP == 4 || $statusWP === '4') {
                                        $statusWP = 'Luar Kota';
                                    } else {
                                        $statusWP = '-';
                                    }

                                    if ($statusOP == 1 || $statusOP === '1') {
                                        $statusOP = 'Belum Diproses Petugas';
                                    } elseif ($statusOP == 2 || $statusOP === '2') {
                                        $statusOP = 'Ditemukan';
                                    } elseif ($statusOP == 3 || $statusOP === '3') {
                                        $statusOP = 'Tidak Ditemukan';
                                    } elseif ($statusOP == 4 || $statusOP === '4') {
                                        $statusOP = 'Luar Kota';
                                    } else {
                                        $statusOP = '-';
                                    }
                                @endphp
                                <tr>
                                    {{-- PERHATIKAN: data-label="..." ditambahkan di sini --}}
                                    <td data-label="No" style="text-align:center;">{{ $rows->firstItem() + $i }}</td>

                                    <td data-label="NOP" class="td-nop" style="font-family:monospace; font-weight:600;">
                                        {{ $r->NOP }}
                                    </td>

                                    <td data-label="Tahun" style="text-align:center;">{{ $r->TAHUN }}</td>

                                    <td data-label="Nama WP">
                                        {{ Str::limit($r->NAMA_WP ?: '—', 20) }}
                                    </td>

                                    <td data-label="Penyampaian">{!! $badgePeny !!}</td>

                                    <td data-label="Status OP">
                                        <span class="badge-soft bg-soft-gray">{{ $statusOP }}</span>
                                    </td>

                                    <td data-label="Status WP">
                                        <span class="badge-soft bg-soft-gray">{{ $statusWP }}</span>
                                    </td>

                                    {{-- Kolom Aksi tidak butuh label di mobile, kita styling beda --}}
                                    <td data-label="">
                                        <div class="td-actions">
                                            <a href="{{ route('petugas.sdt.show', ['id' => $r->ID, 'return' => request()->fullUrl()]) }}"
                                                class="btn-ghost btn-compact" title="Lihat Detail">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <a href="{{ route('petugas.sdt.edit', ['id' => $r->ID, 'back' => request()->fullUrl()]) }}"
                                                class="btn-blue btn-compact" title="Update Data">
                                                <i class="bi bi-pencil"></i> Update
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-inbox fs-1 text-muted opacity-50 mb-2"></i>
                                            <span class="text-muted fw-bold">Tidak ada data ditemukan</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $rows->withQueryString()->links('pagination::bootstrap-5') }}
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
