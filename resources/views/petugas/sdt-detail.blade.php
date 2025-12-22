@extends('layouts.admin')

@section('title', 'Petugas / Detail SDT')
@section('breadcrumb', 'Petugas / Detail SDT')

@section('content')

    <style>
        :root {
            --bg: #f4f6fa;
            --card: #fff;
            --text: #0f172a;
            --muted: #6b7280;
            --line: #e5e7eb;
            --accent: #25@extends('layouts.admin')

@section('title', 'Petugas / Detail SDT')
@section('breadcrumb', 'Petugas / Detail SDT')

@section('content')

@php
    $hasFilter = request()->filled('nop') || request()->filled('tahun') || request()->filled('nama');

    // Hitung Sedang Diproses / Belum Diproses
    $total = $rows->count();
    $sedangDiproses = 0;
    $belumDiproses = 0;

    foreach ($rows as $r) {
        if ($r->latestStatus) {
            // Ada input petugas → sudah diproses
            $sedangDiproses++;
        } else {
            // Belum ada input → belum diproses
            $belumDiproses++;
        }
    }

    // Progress (%)
    $progress = $total ? ($sedangDiproses / $total) * 100 : 0;
    $progressFmt = rtrim(rtrim(number_format($progress,2,'.',''),'0'),'.');

    // DEFINISI FUNGSI RENDER RIWAYAT
    $renderRiwayatBlock = function($data, $is_latest) use ($sdt) {
        $petugasNama = $data->petugas->NAMA ?? '—';
        $statusBadge = $is_latest ? '<span class="badge bg-primary badge-status">AKTIF</span>' : '';
        $borderClass = $is_latest ? 'border-accent' : 'border-light';
        $cardTitle = $is_latest ? 'Input Terbaru' : 'Input Sebelumnya';

        $statusPenyampaian = $data->STATUS_PENYAMPAIAN ?? $sdt->STATUS_PENYAMPAIAN ?? '-';
        $statusOP = $data->STATUS_OP ?? '-';
        $statusWP = $data->STATUS_WP ?? '-';
        $namaPenerima = $data->NAMA_PENERIMA ?? '-';
        $hpPenerima = $data->HP_PENERIMA ?? '-';
        $keteranganPetugas = $data->KETERANGAN_PETUGAS ?: '—';
        $tglPenyampaian = $data->TGL_PENYAMPAIAN ? date('d M Y H:i', strtotime($data->TGL_PENYAMPAIAN)) : '—';
        $koordinatOP = $data->KOORDINAT_OP ?? null;
        $nopBenar = $data->NOP_BENAR ?? '—';

        echo "<style>
            .riwayat-card { padding: 15px; border: 1px solid var(--line); border-radius: 12px; margin-bottom: 20px; background-color: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,.04); }
            .riwayat-card.border-accent { border-color: var(--accent); border-width: 2px; }
            .badge-status { font-size: 0.65rem; padding: 0.3em 0.6em; font-weight: 700; }
            .table-detail-riwayat th { width: 25%; padding: 6px 8px; font-size: .7rem; color: var(--muted); background: #fcfcfc; border-bottom: 1px solid var(--line); text-align: left; }
            .table-detail-riwayat td { padding: 6px 8px; font-size: .8rem; color: var(--text); border-bottom: 1px solid var(--line); text-align: left; font-weight: 500;}
            .map-container { width: 100%; max-width: 350px; height: 180px; border-radius: 8px; overflow: hidden; margin-top: 5px; border: 1px solid #ccc; }
        </style>";

        echo "<div class='riwayat-card $borderClass'>";
            echo "<div class='d-flex justify-content-between align-items-center mb-3'>";
                echo "<h6 class='mb-0' style='font-weight:700; color:var(--text);'>$cardTitle</h6>";
                echo $statusBadge;
            echo "</div>";

            echo "<table class='table table-sm table-borderless table-detail-riwayat mb-3'>";
                echo "<tr><th>Petugas (Input)</th><td colspan='3'><strong>$petugasNama</strong> <span class='text-muted'>(NOP Benar: $nopBenar)</span></td></tr>";
                echo "<tr><th>Status Penyampaian</th><td>$statusPenyampaian</td><th>Status OP/WP</th><td>$statusOP/$statusWP</td></tr>";
                echo "<tr><th>Penerima (Nama/HP)</th><td colspan='3'>$namaPenerima / $hpPenerima</td></tr>";
                echo "<tr><th>Keterangan</th><td colspan='3'>$keteranganPetugas</td></tr>";
                echo "<tr><th>Waktu Input</th><td colspan='3'>$tglPenyampaian</td></tr>";
            echo "</table>";

            if ($koordinatOP) {
                [$lat, $lng] = explode(',', $koordinatOP);
                echo "<p class='mt-2 mb-1 small text-muted font-weight-600'>📍 Lokasi Input Peta:</p>";
                echo "<div class='map-container'>";
                    echo "<iframe width='100%' height='180' frameborder='0' style='border:0' src='https://www.google.com/maps?q=" . trim($lat) . "," . trim($lng) . "&hl=id&z=17&output=embed'></iframe>";
                echo "</div>";
                echo "<p class='mt-1 small mb-0'>Koordinat: <a href='https://www.google.com/maps?q=$koordinatOP' target='_blank' style='color:var(--accent); font-weight:600;'>$koordinatOP</a></p>";
            } else {
                echo "<span class='text-muted small'>Tidak ada data koordinat pada input ini.</span>";
            }
        echo "</div>";
    };
@endphp


<style>
/* --- MODERNISED STYLE CSS --- */
:root {
    --bg: #f5f7fa;
    --card: #ffffff;
    --text: #1f2937;
    --muted: #6b7280;
    --line: #e5e7eb;
    --accent: #3b82f6; /* Blue 500 */
    --accent-2: #2563eb; /* Blue 600 */
    --ok: #10b981; /* Emerald 500 */
    --danger: #ef4444; /* Red 500 */
    --radius: 16px;
    --radius-sm: 8px;
    --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Base Page & Card */
.page-sdt-detail { background: var(--bg); border-radius: 20px; padding: 20px; margin-top: 15px; }
.card-clean {
    background: var(--card);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    border: 1px solid var(--line);
    transition: all 0.3s ease;
}
.card-clean:hover { box-shadow: 0 15px 35px -8px rgba(0, 0, 0, 0.08); }
.card-header { padding: 16px 20px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; }
.page-title { font-weight: 700; font-size: 1.2rem; color: var(--text); }

/* Buttons */
.btn-ghost {
    background: #fff;
    border: 1px solid #d1d5db;
    padding: .4rem .8rem;
    border-radius: var(--radius-sm);
    font-weight: 600;
    color: var(--muted);
    transition: .2s;
    font-size: .8rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-ghost:hover { background: #f9fafb; border-color: #e5e7eb; transform: translateY(-1px); }
.btn-blue {
    background: var(--accent);
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
}
.btn-blue:hover { background: var(--accent-2); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4); transform: translateY(-1px); }
.btn-compact { font-size: .7rem; padding: .3rem .6rem; }

/* KPIs */
.kpis { display: flex; flex-direction: column; gap: 10px; padding: 15px 20px; }
.kpi-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; }
.kpi {
    background: #fcfcfc;
    border-radius: 10px;
    padding: 12px 15px;
    border: 1px solid #f3f4f6;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.kpi .t { color: var(--muted); font-size: .75rem; margin-bottom: 4px; font-weight: 500; }
.kpi .v { color: var(--text); font-weight: 800; font-size: 1rem; }
.kpi .bar { height: 6px; background: #eef2ff; border-radius: 999px; margin-top: 6px; overflow: hidden; }
.kpi .bar > i { height: 100%; background: linear-gradient(90deg, #6366f1, var(--accent)); display: block; }

/* Table */
.table-wrap { border-radius: var(--radius-sm); overflow: hidden; background: #fff; margin-top: 15px; border: 1px solid var(--line); }
table.tbl { width: 100%; border-collapse: collapse; }
thead th { background: #f8fafc; padding: 12px 10px; font-weight: 700; font-size: .7rem; color: var(--muted); border-bottom: 1px solid var(--line); text-align: center; text-transform: uppercase; letter-spacing: 0.5px;}
tbody td { padding: 10px; font-size: .78rem; color: var(--text); text-align: center; border-bottom: 1px solid #f3f4f6; }
tbody tr:hover { background: #f5f9ff; }
.badge-soft {
    background: #eff6ff; /* Blue 50 */
    padding: .2rem .5rem;
    border-radius: 4px;
    font-size: .65rem;
    font-weight: 700;
    color: var(--accent);
    display: inline-block;
}
.icon.ok { color: var(--ok); font-weight: bold; margin-right: 3px; }
.icon.no { color: var(--danger); font-weight: bold; margin-right: 3px; }
.td-actions { display: flex; gap: 4px; justify-content: center; }
</style>

  {{-- HEADER --}}
        <div class="card-header">
            <h5 class="page-title mb-0">Detail SDT — {{ $sdt->NAMA_SDT }}</h5>
            <div class="d-flex gap-2 flex-wrap">
                @if($hasFilter)
                    <a href="{{ route('petugas.sdt.detail',$sdt->ID) }}" class="btn-ghost">
                        <i class="bi bi-x-circle"></i> Reset Filter
                    </a>
                @endif
                <a href="{{ request()->get('back', url()->previous()) }}" class="btn-ghost">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn-blue" data-bs-toggle="modal" data-bs-target="#modalMassKO">
                    <i class="bi bi-geo-alt"></i> Update Massal KO
                </button>
                <button type="button" class="btn-blue" data-bs-toggle="modal" data-bs-target="#modalMassNOP">
                    <i class="bi bi-pencil-square"></i> Update Massal NOP
                </button>
            </div>
        </div>


{{-- ===== KPI ===== --}}
<div class="card-body p-0">
    <div class="kpis">
        <div class="kpi-row">
            <div class="kpi">
                <div class="t">Total NOP</div>
                <div class="v">{{ $total }}</div>
            </div>
            <div class="kpi">
                <div class="t">Sedang Diproses</div>
                <div class="v" style="color:var(--ok);">{{ $sedangDiproses }}</div>
            </div>
            <div class="kpi">
                <div class="t">Belum Diproses</div>
                <div class="v" style="color:var(--danger);">{{ $belumDiproses }}</div>
            </div>
        </div>
        <div class="kpi-row">
            <div class="kpi" style="grid-column: span 2 / auto;">
                <div class="t">Progress Penyampaian</div>
                <div class="v">{{ $progressFmt ? $progressFmt.' %' : '-' }}</div>
                <div class="bar"><i style="width:{{ $progress ?? 0 }}%"></i></div>
            </div>
            <div class="kpi">
                <div class="t">Total Biaya</div>
                <div class="v">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Table & Rows ===== --}}
<div class="table-wrap">
    <table class="tbl">
        <thead>
            <tr>
                <th>No</th>
                <th>NOP</th>
                <th>Tahun</th>
                <th>Nama WP</th>
                <th>Status Penyampaian</th>
                <th>Status OP</th>
                <th>Status WP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $r)
                @php
                    $status = $r->latestStatus?->STATUS_PENYAMPAIAN ?? $r->STATUS_PENYAMPAIAN;
                    $okPeny = in_array(strtoupper($status ?? ''), ['YA','Y','1','TERSAMPAIKAN']);
                    $lastUpdate = $r->latestStatus->updated_at ?? $r->updated_at;


                    $statusOP = $r->latestStatus?->STATUS_OP;


                    if($statusOP == 1){
                        $statusOP = 'Belum Diproses Petugas';
                    }else if($statusOP == 2){
                        $statusOP = 'Ditemukan';
                    }else if ($statusOP == 3){
                       $statusOP = 'Tidak Ditemukan';
                    }else if($statusOP == '4'){
                        $statusOP = 'Sudah Dijual';
                    }
                    // $statusOP = match ((int)($statusOP ?? 0)) {
                    //     1 => 'Belum Diproses Petugas',
                    //     2 => 'Tersampaikan',
                    //     3 => 'Tidak Tersampaikan',
                    //     default => '--',
                    // };

                    $statusWP = $r->latestStatus?->STATUS_WP;


                   if($statusWP == 1){
                        $statusWP = 'Belum Diproses Petugas';
                    }else if($statusWP == 2){
                        $statusWP = 'Ditemukan';
                    }else if ($statusWP == 3){
                       $statusWP = 'Tidak Ditemukan';
                    }else if($statusWP == '4'){
                        $statusWP = 'Luar Kota';
                    }

                    // $statusWP = match ((int)($statusWP ?? 0)) {
                    //     1 => 'Belum Diproses Petugas',
                    //     2 => 'Tersampaikan',
                    //     3 => 'Tidak Tersampaikan',
                    //     default => '--',
                    // };
                @endphp
                <tr>
                    <td>{{ $rows->firstItem() + $i }}</td>
                    <td>{{ $r->NOP }}</td>
                    <td>{{ $r->TAHUN }}</td>
                    <td style="text-align:left;">{{ $r->NAMA_WP ?: '—' }}</td>

                    <td>
                        @if($okPeny)
                            <span class="icon ok"><i class="bi bi-check-circle-fill"></i></span>
                            <span class="badge-soft" style="background-color:#d1fae5; color:var(--ok);">Tersampaikan</span>
                        @else
                            <span class="icon no"><i class="bi bi-x-circle-fill"></i></span>
                            <span class="badge-soft" style="background-color:#fee2e2; color:var(--danger);">Tidak Tersampaikan</span>
                        @endif
                    </td>

                    <td><span class="badge-soft">{{ $statusOP }}</span></td>
                    <td><span class="badge-soft">{{ $statusWP }}</span></td>

                    <td class="td-actions">
                        <a href="{{ route('petugas.sdt.show',['id'=>$r->ID,'return'=>request()->fullUrl()]) }}" class="btn-blue btn-compact">
                            <i class="bi bi-eye"></i> View
                        </a>

                        @if(isset($r->expired))
                            @if($r->expired == '1')
                            <span class="btn-ghost btn-compact" style="opacity:.4;cursor:not-allowed;">
                                <i class="bi bi-lock"></i> Expired
                            </span>
                            @else
                            <a href="{{ route('petugas.sdt.edit',['id'=>$r->ID,'back'=>request()->fullUrl()]) }}" class="btn-blue btn-compact">
                                <i class="bi bi-pencil"></i> Update
                            </a>
                            @endif

                        @else

                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data NOP yang ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3 px-3">
    {{ $rows->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@include('petugas.partials.mass-ko')
@include('petugas.partials.mass-nop')
@include('petugas.partials.modal-camera')

@endsection
63eb;
            --accent-2: #1d4ed8;
            --ok: #16a34a;
            --danger: #ef4444;
            --radius: 18px;
            --radius-sm: 10px;
            --shadow: 0 8px 24px rgba(0, 0, 0, .06);
        }

        /* CONTAINER */
        .page-sdt-detail {
            background: var(--bg);
            border-radius: 20px;
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
            padding: 14px 20px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text);
        }

        /* BUTTONS */
        .btn-ghost {
            background: #fff;
            border: 1px solid var(--line);
            padding: .35rem .6rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            color: var(--muted);
            transition: .2s;
            font-size: .85rem;
        }

        .btn-ghost:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .btn-blue {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border: none;
            color: #fff;
            border-radius: var(--radius-sm);
            padding: .4rem .7rem;
            font-weight: 700;
            font-size: .85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: .2s;
        }

        .btn-blue:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, .35);
        }

        .btn-compact {
            font-size: .75rem;
            padding: .25rem .45rem;
        }

        /* KPI */
        .kpis {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin: 12px 0;
        }

        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 8px;
        }

        .kpi {
            background: #fff;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: .75rem;
            border: 1px solid var(--line);
            transition: .2s;
        }

        .kpi:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
        }

        .kpi .t {
            color: var(--muted);
            font-size: .7rem;
            margin-bottom: 3px;
        }

        .kpi .v {
            color: var(--text);
            font-weight: 700;
            font-size: .85rem;
        }

        .kpi .bar {
            height: 5px;
            background: #eef2ff;
            border-radius: 999px;
            margin-top: 4px;
            overflow: hidden;
        }

        .kpi .bar>i {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
            display: block;
            transition: width .4s ease;
        }

        /* TABLE */
        .table-wrap {
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: #fff;
            margin-top: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .03);
        }

        table.tbl {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #f8fafc;
            padding: 10px;
            font-weight: 700;
            font-size: .75rem;
            color: var(--text);
            border-bottom: 1px solid var(--line);
            text-align: center;
        }

        tbody td {
            padding: 8px;
            font-size: .72rem;
            color: #334155;
            text-align: center;
            border-bottom: 1px solid var(--line);
        }

        tbody tr:nth-child(even) {
            background: #fafbff;
        }

        tbody tr:hover {
            background: #e4f0ff;
        }

        .badge-soft {
            background: #eef2ff;
            padding: .2rem .4rem;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 700;
            color: #1e3a8a;
        }

        .icon.ok {
            color: var(--ok);
            font-weight: bold;
        }

        .icon.no {
            color: var(--danger);
            font-weight: bold;
        }

        .td-actions {
            display: flex;
            gap: 6px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>

    @php
        $hasFilter = request()->filled('nop') || request()->filled('tahun') || request()->filled('nama');
        $progress = $summary['progress'] ?? null;
        $progressFmt = $progress ? rtrim(rtrim(number_format($progress, 2, '.', ''), '0'), '.') : null;
    @endphp

    <div class="page-sdt-detail">
        <div class="card-clean">

            {{-- HEADER --}}
            <div class="card-header">
                <h5 class="page-title mb-0">Detail SDT — {{ $sdt->NAMA_SDT }}</h5>
                <div class="d-flex gap-2 flex-wrap">
                    @if ($hasFilter)
                        <a href="{{ route('petugas.sdt.detail', $sdt->ID) }}" class="btn-ghost btn-sm">Reset Filter</a>
                    @endif
                    <a href="{{ request()->get('back', url()->previous()) }}" class="btn-ghost btn-sm">← Kembali</a>
                    <button type="button" class="btn-blue btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalMassKO">Update Massal KO</button>
                    <button type="button" class="btn-blue btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalMassNOP">Update Massal NOP</button>
                </div>
            </div>

            {{-- KPI --}}
            <div class="card-body">
                <div class="kpis">
                    <div class="kpi-row">
                        <div class="kpi">
                            <div class="t">Total NOP</div>
                            <div class="v">{{ $summary['total'] }}</div>
                        </div>
                        <div class="kpi">
                            <div class="t">Tersampaikan</div>
                            <div class="v">{{ $summary['tersampaikan'] }}</div>
                        </div>
                        <div class="kpi">
                            <div class="t">Belum Tersampaikan</div>
                            <div class="v">{{ $summary['belum'] }}</div>
                        </div>
                    </div>
                    <div class="kpi-row">
                        <div class="kpi">
                            <div class="t">Progress</div>
                            <div class="v">{{ $progressFmt ? $progressFmt . ' %' : '-' }}</div>
                            <div class="bar"><i style="width:{{ $progress ?? 0 }}%"></i></div>
                        </div>
                        <div class="kpi">
                            <div class="t">Total Biaya</div>
                            <div class="v">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NOP</th>
                                <th>Tahun</th>
                                <th>Nama WP</th>
                                <th>Status Penyampaian</th>
                                <th>Status OP</th>
                                <th>Status WP</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $i => $r)
                                @php $okPeny = in_array(strtoupper($r->STATUS_PENYAMPAIAN ?? ''), ['YA','Y','1','TERSAMPAIKAN']); @endphp
                                <tr>
                                    <td>{{ $rows->firstItem() + $i }}</td>
                                    <td>{{ $r->NOP }}</td>
                                    <td>{{ $r->TAHUN }}</td>
                                    <td>{{ $r->NAMA_WP ?: '—' }}</td>
                                    <td>
                                        @if ($okPeny)
                                            <span class="icon ok">✓</span> <span class="badge-soft">Tersampaikan</span>
                                        @else
                                            <span class="icon no">✕</span> <span class="badge-soft">Belum</span>
                                        @endif
                                    </td>
                                    <td><span class="badge-soft">{{ $r->STATUS_OP ?: '—' }}</span></td>
                                    <td><span class="badge-soft">{{ $r->STATUS_WP ?: '—' }}</span></td>

                                    <td class="td-actions">
                                        <a href="{{ route('petugas.sdt.show', ['id' => $r->ID, 'return' => request()->fullUrl()]) }}"
                                            class="btn-blue btn-compact"><i class="bi bi-eye"></i> View</a>
                                        <a href="{{ route('petugas.sdt.edit', ['id' => $r->ID, 'back' => request()->fullUrl()]) }}"
                                            class="btn-blue btn-compact"><i class="bi bi-pencil"></i> Update</a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $rows->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
    <!-- Select2 CSS -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



    @include('petugas.partials.mass-ko')
    @include('petugas.partials.mass-nop')
    @include('petugas.partials.modal-camera')


@endsection
