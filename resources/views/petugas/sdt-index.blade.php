@extends('layouts.admin')

@section('title', 'Petugas / Input Data SDT')
@section('breadcrumb', 'Petugas / Input Data SDT')

@section('content')
    <style>
        /* ========== Design Tokens ========== */
        :root {
            --bg: #f5f7fb;
            --card: #fff;
            --line: #e6e8ec;
            --text: #0f172a;
            --muted: #64748b;
            --accent: #2563eb;
            --ok: #16a34a;
            --ok-2: #34d399;
            --warn: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --shadow: 0 10px 26px rgba(2, 6, 23, .10);
            --px: clamp(12px, 2vw, 20px);
            --py: clamp(10px, 1.4vw, 16px);
            --th: clamp(9px, 1.2vw, 12px);
            --td: clamp(10px, 1.3vw, 14px);
        }

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

        .card-header {
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
            font-size: clamp(1rem, 1.1vw + .85rem, 1.25rem)
        }

        /* BUTTONS */
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
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            transition: .15s;
        }

        .btn-detail:hover {
            opacity: .9;
            transform: translateY(-1px);
            color: white;
        }

        /* TABLE */
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

        .col-no {
            width: 50px;
        }

        .col-nama {
            width: 25%;
        }

        .col-date {
            width: 13%;
        }

        .col-nop {
            width: 10%;
        }

        .col-status {
            width: 12%;
        }

        .col-prog {
            width: 12%;
        }

        .col-aksi {
            width: 15%;
            min-width: 130px;
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

        .tbl tbody td.col-aksi {
            overflow: visible;
            text-overflow: clip;
        }

        .tbl thead th.col-no,
        .tbl thead th.col-nop,
        .tbl tbody td.col-no,
        .tbl tbody td.col-nop {
            text-align: right;
        }

        .tbl thead th.col-date,
        .tbl thead th.col-status,
        .tbl thead th.col-prog,
        .tbl thead th.col-aksi,
        .tbl tbody td.col-date,
        .tbl tbody td.col-status,
        .tbl tbody td.col-prog,
        .tbl tbody td.col-aksi {
            text-align: center;
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

        /* CHIP / BADGE */
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

        .chip[data-type="success"] {
            background: linear-gradient(180deg, #ecfdf5, #dcfce7);
        }

        .chip[data-type="success"] .dot {
            background: var(--ok);
        }

        .chip[data-type="warn"] {
            background: linear-gradient(180deg, #fff7ed, #ffedd5);
        }

        .chip[data-type="warn"] .dot {
            background: var(--warn);
        }

        .chip[data-type="info"] {
            background: linear-gradient(180deg, #eff6ff, #dbeafe);
        }

        .chip[data-type="info"] .dot {
            background: var(--info);
        }

        /* PROGRESS TEXT BADGE */
        .badge-progress {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.75rem;
            color: #fff;
            min-width: 80px;
        }

        .prog-100 {
            background: #10b981;
        }

        /* Hijau */
        .prog-low {
            background: #ef4444;
        }

        /* Merah */

        .prog-detail {
            display: block;
            font-size: 0.65rem;
            color: var(--muted);
            margin-top: 2px;
            font-weight: 600;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .page-sdt {
                margin-top: 0;
            }

            .section {
                padding: 15px;
                border-radius: 12px;
            }

            .card-clean,
            .card-header,
            .table-wrap {
                border: none;
                background: transparent;
                box-shadow: none;
                overflow: visible;
            }

            .card-header {
                padding: 0 0 15px 0;
            }

            .card-body {
                padding: 0 !important;
            }

            .tbl,
            .tbl tbody,
            .tbl tbody tr {
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

            .tbl tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right !important;
                padding: 8px 0;
                border-bottom: 1px dashed #f1f5f9;
                width: 100% !important;
                white-space: normal;
                height: auto;
                overflow: visible;
            }

            .tbl tbody td:last-child {
                border-bottom: none;
                padding-top: 15px;
                justify-content: center;
                margin-top: 5px;
            }

            .tbl tbody td::before {
                content: attr(data-label);
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                color: var(--muted);
                margin-right: 15px;
                text-align: left;
                min-width: 90px;
            }

            .col-nama {
                font-weight: 700;
                color: var(--accent);
                font-size: 1rem !important;
                border-bottom: 2px solid #f1f5f9 !important;
                margin-bottom: 5px;
                padding-bottom: 12px !important;
            }

            .col-nama::before {
                display: none;
            }

            .col-prog {
                display: flex !important;
            }

            .btn-detail {
                width: 100%;
                justify-content: center;
                padding: 10px;
            }
        }
    </style>

    <div class="section page-sdt">
        <div class="card-clean">
            <div class="card-header">
                <h5 class="page-title mb-0">Input Detail SDT</h5>
            </div>

            <div class="card-body" style="padding:20px 26px;">
                <h6 class="text-muted fw-bold mb-3 d-none d-md-block" style="font-size:.9rem;">Daftar SDT</h6>

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
                                <th class="col-date">Tgl Mulai</th>
                                <th class="col-date">Tgl Selesai</th>
                                <th class="col-nop">Jml NOP</th>
                                <th class="col-status">Status</th>
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
                                    $nop = is_numeric($m->JUMLAH_NOP ?? null) ? (int) $m->JUMLAH_NOP : 0;
                                    $processed = is_numeric($m->SUDAH_DIPROSES ?? null) ? (int) $m->SUDAH_DIPROSES : 0;
                                    $prog = isset($m->PROGRESS) ? max(0, min(100, (float) $m->PROGRESS)) : 0;
                                    $progTxt = rtrim(rtrim(number_format($prog, 2, '.', ''), '0'), '.');

                                    // Tentukan warna badge progress
                                    $progClass = $prog >= 100 ? 'prog-100' : 'prog-low';
                                @endphp

                                <tr>
                                    <td class="col-no mono" data-label="No">{{ $loop->iteration }}</td>
                                    <td class="col-nama" data-label="Nama SDT" title="{{ $m->NAMA_SDT }}">
                                        {{ $m->NAMA_SDT }}</td>
                                    <td class="col-date" data-label="Tgl Mulai">
                                        {{ $mulai ? $mulai->translatedFormat('d M Y') : '—' }}</td>
                                    <td class="col-date" data-label="Tgl Selesai">
                                        {{ $selesai ? $selesai->translatedFormat('d M Y') : '—' }}</td>
                                    <td class="col-nop mono" data-label="Jumlah NOP">{{ number_format($nop) }}</td>
                                    <td class="col-status" data-label="Status">
                                        <span class="chip" data-type="{{ $chipType }}"><span
                                                class="dot"></span>{{ $status }}</span>
                                    </td>
                                    <td class="col-prog" data-label="Progress">
                                        <div style="text-align: center;">
                                            <span class="badge-progress {{ $progClass }}">
                                                {{ $progTxt }}%
                                            </span>
                                            <span class="prog-detail">
                                                {{ number_format($processed) }} / {{ number_format($nop) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="col-aksi" data-label="">
                                        <a href="{{ route('petugas.sdt.detail', $m->ID) }}" class="btn btn-detail btn-sm"
                                            aria-label="Detail SDT {{ $m->NAMA_SDT }}">
                                            <i class="bi bi-eye me-1"></i> Buka Detail
                                        </a>
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
