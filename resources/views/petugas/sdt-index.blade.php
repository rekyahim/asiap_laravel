@extends('layouts.admin')

@section('title', 'Petugas / Input Data SDT')
@section('breadcrumb', 'Petugas / Input Data SDT')

@section('content')
    <style>
        /* ========== Design Tokens & Scaling ========== */
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

        /* ========== Layout & Card ========== */
        .page-sdt {
            margin-top: 4px
        }

        .section {
            background: var(--bg);
            border-radius: 22px;
            padding: var(--px)
        }

        .card-clean {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow);
            overflow: hidden
        }

        .card-clean .card-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: var(--py) 26px;
            border-bottom: 1px solid var(--line);
            background:
                radial-gradient(80% 140% at 100% 0%, rgba(37, 99, 235, .05) 0%, #fff 60%),
                linear-gradient(180deg, #fff, #f8fafc);
        }

        .page-title {
            margin: 0;
            color: var(--text);
            font-weight: 800;
            letter-spacing: .2px;
            font-size: clamp(1rem, 1.1vw + .85rem, 1.25rem)
        }

        /* ========== Buttons ========== */
        .btn-detail {
            background: linear-gradient(135deg, var(--ok), var(--ok-2));
            border: 1px solid #10b981;
            color: #fff;
            border-radius: 10px;
            font-weight: 800;
            line-height: 1;
            padding: .38rem .72rem;
            font-size: .85rem;
            box-shadow: 0 8px 18px rgba(16, 185, 129, .18);
            text-decoration: none;
            outline: none !important;
            transition: all .15s ease-in-out;
        }

        .btn-detail:hover {
            opacity: .9;
            transform: translateY(-1px);
            text-decoration: none
        }

        .btn-detail:focus,
        .btn-detail:active,
        .btn-detail:focus-visible {
            outline: none !important;
            box-shadow: none !important;
            text-decoration: none
        }

        /* ========== Table ========== */
        .table-wrap {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #fff;
            overflow: auto
        }

        .tbl {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed
        }

        .col-no {
            width: 64px
        }

        .col-nama {
            width: 28%
        }

        .col-date {
            width: 14%
        }

        .col-nop {
            width: 12%
        }

        .col-status {
            width: 13%
        }

        .col-prog {
            width: 13%
        }

        .col-aksi {
            width: 10%;
            min-width: 120px
        }

        /* beri ruang tombol */

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
            font-size: clamp(.78rem, .9vw, .84rem)
        }

        /* Alignment sinkron */
        .tbl thead th.col-no,
        .tbl tbody td.col-no {
            text-align: right
        }

        .tbl thead th.col-nama,
        .tbl tbody td.col-nama {
            text-align: left
        }

        .tbl thead th.col-date,
        .tbl tbody td.col-date {
            text-align: center
        }

        .tbl thead th.col-nop,
        .tbl tbody td.col-nop {
            text-align: right
        }

        .tbl thead th.col-status,
        .tbl tbody td.col-status {
            text-align: center
        }

        .tbl thead th.col-prog,
        .tbl tbody td.col-prog {
            text-align: center
        }

        .tbl thead th.col-aksi,
        .tbl tbody td.col-aksi {
            text-align: center
        }

        .tbl tbody td {
            padding: var(--td) calc(var(--td) + 2px);
            border-bottom: 1px solid var(--line);
            vertical-align: middle;
            color: #334155;
            font-size: clamp(.82rem, 1vw, .95rem);
            line-height: 1.35;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ==== FIX elipsis "..." di kolom Aksi ==== */
        .tbl thead th.col-aksi,
        .tbl tbody td.col-aksi {
            overflow: visible;
            text-overflow: clip;
            white-space: nowrap;
        }

        .tbl tbody td.col-aksi * {
            overflow: visible !important;
            text-overflow: clip !important;
            white-space: nowrap !important;
        }

        .tbl tbody tr:nth-child(even) {
            background: #fcfdff
        }

        .tbl tbody tr:hover {
            background: #f6f8ff
        }

        .mono {
            font-family: ui-monospace, Menlo, monospace
        }

        /* ========== Status Pill ========== */
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
            white-space: nowrap
        }

        .chip .dot {
            width: .44rem;
            height: .44rem;
            border-radius: 999px;
            background: #9ca3af
        }

        .chip[data-type="success"] {
            background: linear-gradient(180deg, #ecfdf5, #dcfce7)
        }

        .chip[data-type="success"] .dot {
            background: var(--ok)
        }

        .chip[data-type="warn"] {
            background: linear-gradient(180deg, #fff7ed, #ffedd5)
        }

        .chip[data-type="warn"] .dot {
            background: var(--warn)
        }

        .chip[data-type="info"] {
            background: linear-gradient(180deg, #eff6ff, #dbeafe)
        }

        .chip[data-type="info"] .dot {
            background: var(--info)
        }

        /* ========== Progress Mini ========== */
        .progress {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            white-space: nowrap
        }

        .bar {
            display: block;
            width: clamp(90px, 12vw, 140px);
            height: 8px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden
        }

        .bar>i {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent-2))
        }

        .pct {
            font-size: .78rem;
            color: var(--muted)
        }

        /* Responsif */
        @media (max-width:992px) {
            .col-prog {
                display: none
            }

            .tbl thead th.col-prog,
            .tbl tbody td.col-prog {
                display: none
            }
        }

        @media (max-width:768px) {
            .col-date {
                width: 18%
            }

            .col-nop {
                width: 18%
            }

            .col-aksi {
                width: 14%
            }
        }
    </style>

    <div class="section page-sdt">
        <div class="card-clean">

            {{-- Header --}}
            <div class="card-header">
                <h5 class="page-title mb-0">Input Detail SDT</h5>
            </div>

            {{-- Body --}}
            <div class="card-body" style="padding:20px 26px;">
                <h6 class="text-muted fw-bold mb-3" style="font-size:.9rem;">Daftar SDT</h6>

                <div class="table-wrap">
                    <table class="tbl align-middle">
                        <colgroup>
                            <col class="col-no">
                            <col class="col-nama">
                            <col class="col-date">
                            <col class="col-date">
                            <col class="col-nop">
                            <col class="col-status">
                            <col class="col-prog">
                            <col class="col-aksi">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-no">No</th>
                                <th class="col-nama">Nama SDT</th>
                                <th class="col-date">Tanggal Mulai</th>
                                <th class="col-date">Tanggal Selesai</th>
                                <th class="col-nop">Jumlah NOP</th>
                                <th class="col-status">Status SDT</th>
                                <th class="col-prog">Progress</th>
                                <th class="col-aksi">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse(($master ?? []) as $m)
                                @php
                                    $mulai = !empty($m->TGL_MULAI) ? \Carbon\Carbon::parse($m->TGL_MULAI) : null;
                                    $selesai = !empty($m->TGL_SELESAI) ? \Carbon\Carbon::parse($m->TGL_SELESAI) : null;
                                    $now = now();

                                    $statusDb = strtoupper(trim($m->STATUS_SDT ?? ''));
                                    $status =
                                        $statusDb ?:
                                        ($mulai && $selesai && $now->between($mulai, $selesai)
                                            ? 'AKTIF'
                                            : ($selesai && $now->gt($selesai)
                                                ? 'SELESAI'
                                                : 'DRAFT'));

                                    $chipType =
                                        $status === 'AKTIF' ? 'success' : ($status === 'SELESAI' ? 'warn' : 'info');

                                    $nop = is_numeric($m->JUMLAH_NOP ?? null)
                                        ? number_format((int) $m->JUMLAH_NOP)
                                        : '—';
                                    $prog = isset($m->PROGRESS) ? max(0, min(100, (float) $m->PROGRESS)) : null;
                                    $progTxt = isset($prog)
                                        ? rtrim(rtrim(number_format($prog, 2, '.', ''), '0'), '.')
                                        : null;
                                @endphp

                                <tr>
                                    <td class="col-no mono">{{ $loop->iteration }}</td>
                                    <td class="col-nama" title="{{ $m->NAMA_SDT }}">{{ $m->NAMA_SDT }}</td>
                                    <td class="col-date">{{ $mulai ? $mulai->translatedFormat('d M Y') : '—' }}</td>
                                    <td class="col-date">{{ $selesai ? $selesai->translatedFormat('d M Y') : '—' }}</td>
                                    <td class="col-nop mono">{{ $nop }}</td>
                                    <td class="col-status">
                                        <span class="chip" data-type="{{ $chipType }}">
                                            <span class="dot"></span>{{ $status }}
                                        </span>
                                    </td>
                                    <td class="col-prog">
                                        @if (!is_null($prog))
                                            <span class="progress">
                                                <span class="bar"><i style="width:{{ $prog }}%"></i></span>
                                                <span class="pct">{{ $progTxt }}%</span>
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="col-aksi">
                                        <a href="{{ route('petugas.sdt.detail', $m->ID) }}" class="btn btn-detail btn-sm"
                                            aria-label="Detail SDT {{ $m->NAMA_SDT }}">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada master SDT.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
