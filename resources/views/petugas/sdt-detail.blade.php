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
            --accent: #2563eb;
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


@endsection
