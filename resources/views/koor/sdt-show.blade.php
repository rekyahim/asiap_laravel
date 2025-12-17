@extends('layouts.admin')

@section('title', 'Detail Riwayat SDT')
@php($forceMenu = 'riwayat_petugas')
@section('breadcrumb', '')

@push('styles')
    <style>
        /* ========= breadcrumb & card (sama seperti sebelumnya) ========= */
        .page-breadcrumb {
            margin: -.25rem 0 1rem 0
        }

        .crumbs {
            font-size: .9rem
        }

        .crumb {
            color: #6c757d;
            text-decoration: none;
            transition: .15s
        }

        .crumb:hover {
            color: #212529;
            text-decoration: underline
        }

        .crumb.active {
            font-weight: 600;
            color: #212529
        }

        .crumb-sep {
            margin: 0 .35rem;
            color: #adb5bd
        }

        .sdt-card {
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06), 0 2px 6px rgba(0, 0, 0, .04);
            border: 1px solid rgba(0, 0, 0, .03);
            border-radius: .75rem;
        }

        .stat-chip {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
            min-width: 180px;
        }

        /* petugas chips */
        .petugas-panel {
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: .75rem;
            background: #fff;
            padding: .75rem
        }

        .petugas-list {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: .5rem;
            max-height: 140px;
            overflow: auto
        }

        .petugas-chip {
            border: 1px solid rgba(0, 0, 0, .08);
            background: #f8f9fa;
            padding: .35rem .75rem;
            border-radius: 999px;
            font-size: .85rem;
            cursor: pointer
        }

        .petugas-chip.active {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd
        }

        /* table */
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: .85rem;
            letter-spacing: .5px;
            background: #f8f9fa !important;
            position: sticky;
            top: 0;
            z-index: 5
        }

        .table-hover tbody tr:hover {
            background: #f0f4ff
        }

        /* status badges */
        .badge-status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 600
        }

        .badge-ok {
            background: #d3f9d8;
            color: #2b8a3e
        }

        .badge-no {
            background: #ffe3e3;
            color: #c92a2a
        }

        /* offcanvas */
        #offcanvasDetail {
            width: 520px;
            max-width: 92vw;
            border-left: 1px solid #e5e7eb;
            box-shadow: -10px 0 25px rgba(0, 0, 0, .08);
            transition: transform .45s cubic-bezier(.25, .85, .45, 1);
            padding-top: 1rem;
        }

        #offcanvasDetail .offcanvas-body {
            animation: fadeIn .35s ease
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(6px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* floating close */
        .btn-close-floating {
            position: absolute;
            left: -18px;
            top: 14px;
            width: 38px;
            height: 38px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 30;
            cursor: pointer;
        }

        .btn-close-floating:hover {
            transform: scale(1.08)
        }

        /* detail layout inside offcanvas */
        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 8px 0;
            border-bottom: 1px dashed #e5e7eb
        }

        .detail-label {
            font-weight: 600;
            color: #475569;
            width: 48%
        }

        .detail-value {
            width: 52%;
            text-align: right;
            color: #0f172a
        }

        .evidence-box img {
            max-width: 140px;
            border-radius: 10px;
            margin-right: 6px;
            margin-bottom: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .09)
        }
    </style>
@endpush

@section('content')
    <div class="container-lg px-0">

        {{-- Breadcrumb --}}
        <div class="page-breadcrumb mb-3">
            <div class="crumbs">
                <a href="{{ route('riwayat.petugas') }}" class="crumb">Riwayat SDT Petugas</a>
                <span class="crumb-sep">•</span>
                <span class="crumb active">Detail Riwayat SDT</span>
            </div>
        </div>

        {{-- HEADER (stat-chips termasuk Total Item & Total PBB) --}}
        <div class="card sdt-card border-0 mb-4">
            <div class="card-body p-3 p-md-4">
                <h4 class="fw-semibold mb-3">{{ $sdt->NAMA_SDT }}</h4>

                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="stat-chip">
                            <div class="small text-muted">Nama SDT</div>
                            <div class="fw-semibold">{{ $sdt->NAMA_SDT }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-chip">
                            <div class="small text-muted">Periode</div>
                            <div class="fw-semibold">
                                {{ \Carbon\Carbon::parse($sdt->TGL_MULAI)->format('d-m-Y') }}
                                s/d
                                {{ \Carbon\Carbon::parse($sdt->TGL_SELESAI)->format('d-m-Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-chip">
                            <div class="small text-muted">Total Item</div>
                            <div class="fw-semibold">{{ $rows->count() }} data</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-chip">
                            <div class="small text-muted">Total PBB Harus Dibayar</div>
                            <div class="fw-semibold text-success">{{ $totalPbb }}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="card sdt-card border-0 mb-4">
            <div class="card-body p-3 p-md-4">
                <label class="small text-muted mb-1">Pencarian</label>
                <input id="page-search" class="form-control mb-3" placeholder="Cari NOP / Tahun / Petugas / Nama WP…">

                <label class="small text-muted mb-1">Filter Petugas</label>
                <div class="petugas-panel">
                    <div id="page-petugas" class="petugas-list"></div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card sdt-card border-0 mb-4">
            <div class="card-body p-3 p-md-4">
                <h5 class="fw-semibold mb-3">Detail Data SDT</h5>
                <div class="table-responsive" style="max-height:72vh; overflow:auto;">
                    <table class="table table-hover table-striped table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NOP</th>
                                <th>Tahun</th>
                                <th>Petugas</th>
                                <th>Nama WP</th>
                                <th>Alamat WP</th>
                                <th>Status</th>
                                <th style="width:110px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="page-rows"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- OFFCANVAS DETAIL --}}
    <div class="offcanvas offcanvas-end" id="offcanvasDetail" tabindex="-1" aria-labelledby="offcanvasDetailLabel">
        <button class="btn-close-floating" data-bs-dismiss="offcanvas" aria-label="Tutup"><i
                class="bi bi-x-lg"></i></button>

        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-semibold" id="offcanvasDetailLabel">Detail NOP</h5>
        </div>

        <div class="offcanvas-body" id="offcanvas-body">
            <div class="text-center text-muted py-4">Klik tombol Detail untuk melihat informasi lengkap.</div>
        </div>
    </div>
    <!-- MODAL PREVIEW GAMBAR -->
    <div class="modal fade" id="modalPreviewImage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: transparent; border:none; box-shadow:none;">
                <img id="previewImageFull" src="" class="img-fluid rounded"
                    style="max-height:90vh; display:block; margin:auto;">
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        (function() {

            /* RAW data dari controller */
            const RAW = {!! json_encode(
                $rows->map(
                    fn($r) => [
                        'id' => $r->ID,
                        'nop' => $r->NOP,
                        'tahun' => $r->TAHUN,
                        'petugas' => $r->PETUGAS_SDT,
                        'nama_wp' => $r->NAMA_WP,
                        'alamat_wp' => $r->ALAMAT_WP,
                        'status' => $r->status_penyampaian,
                    ],
                ),
            ) !!};

            let QUERY = '',
                FILTER_ACTIVE = null;

            const elRows = document.getElementById('page-rows');
            const elPetugas = document.getElementById('page-petugas');
            const elSearch = document.getElementById('page-search');

            /* helper: normalize status → teks */
            function normalizeStatusMain(v) {
                if (v === null || typeof v === 'undefined') return '-';
                if (typeof v === 'string') {
                    const s = v.trim();
                    if (s === '1' || s === '0')
                        return s === '1' ? 'Tersampaikan' : 'Tidak Tersampaikan';
                    return s || '-';
                }
                if (typeof v === 'number')
                    return v === 1 ? 'Tersampaikan' : 'Tidak Tersampaikan';
                return '-';
            }

            function normalizeStatusOp(v) {
                if (v === null) return '-';
                return (v == 1) ? "Ditemukan" : (v == 2 ? "Tidak ditemukan" : "-");
            }

            function normalizeStatusWp(v) {
                return normalizeStatusOp(v);
            }

            /* render petugas chips */
            function renderPetugas() {
                elPetugas.innerHTML = '';
                const names = [...new Set(RAW.map(r => r.petugas).filter(Boolean))];

                const all = document.createElement('span');
                all.className = 'petugas-chip ' + (!FILTER_ACTIVE ? 'active' : '');
                all.innerText = `Semua (${RAW.length})`;
                all.onclick = () => {
                    FILTER_ACTIVE = null;
                    renderPetugas();
                    renderRows();
                };
                elPetugas.appendChild(all);

                names.forEach(nm => {
                    const count = RAW.filter(r => r.petugas === nm).length;
                    const chip = document.createElement('span');
                    chip.className = 'petugas-chip ' + (FILTER_ACTIVE === nm ? 'active' : '');
                    chip.innerText = `${nm} (${count})`;
                    chip.onclick = () => {
                        FILTER_ACTIVE = nm;
                        renderPetugas();
                        renderRows();
                    };
                    elPetugas.appendChild(chip);
                });
            }

            /* render table rows */
            function renderRows() {
                elRows.innerHTML = '';

                let rows = RAW.filter(r => {
                    if (!QUERY) return true;
                    const s = QUERY.toLowerCase();
                    return r.nop?.toLowerCase().includes(s) ||
                        r.petugas?.toLowerCase().includes(s) ||
                        r.nama_wp?.toLowerCase().includes(s) ||
                        r.tahun?.toString().includes(s);
                });

                if (FILTER_ACTIVE) rows = rows.filter(r => r.petugas === FILTER_ACTIVE);

                rows.forEach(r => {
                    const statusText = normalizeStatusMain(r.status);
                    const statusHtml = statusText === 'Tersampaikan' ?
                        `<span class="badge-status badge-ok">${statusText}</span>` :
                        `<span class="badge-status badge-no">${statusText}</span>`;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${r.id}</td>
                        <td><code>${r.nop ?? '-'}</code></td>
                        <td>${r.tahun ?? '-'}</td>
                        <td>${r.petugas ?? '-'}</td>
                        <td>${r.nama_wp ?? '-'}</td>
                        <td>${r.alamat_wp ?? '-'}</td>
                        <td>${statusHtml}</td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-nop-detail" data-id="${r.id}">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        </td>
                    `;
                    elRows.appendChild(tr);
                });
            }

            /* search */
            elSearch?.addEventListener('input', function() {
                QUERY = this.value.trim();
                renderRows();
            });

            /* CLICK DETAIL → FETCH */
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-nop-detail');
                if (!btn) return;

                const id = btn.dataset.id;
                const body = document.getElementById('offcanvas-body');
                body.innerHTML = '<div class="py-4 text-center text-muted">Memuat data...</div>';

                fetch(`/koor/riwayat/${id}/detail`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(d => {
                        const statusMain = normalizeStatusMain(d.status_penyampaian);
                        const statusOp = normalizeStatusOp(d.status_op);
                        const statusWp = normalizeStatusWp(d.status_wp);

                        // evidence html → jadikan clickable
                        let evidence = (d.evidence_html || '')
                            .replaceAll('<img', '<img class="evidence-thumb" style="cursor:pointer;"');

                        if (!evidence.trim()) {
                            evidence = '<div class="text-muted">Tidak ada evidence</div>';
                        }

                        const L = (label, value) => `
                            <div class="detail-row">
                                <div class="detail-label">${label}</div>
                                <div class="detail-value">${value ?? '-'}</div>
                            </div>
                        `;

                        body.innerHTML = `
                            <h6 class="fw-bold mb-2">Informasi Umum</h6>
                            ${L('NOP', d.nop)} ${L('Tahun', d.tahun)} ${L('Petugas', d.petugas)}

                            <hr>
                            <h6 class="fw-bold mb-2">Objek Pajak</h6>
                            ${L('Alamat OP', d.alamat_op)}
                            ${L('Blok/Kav', d.blok_kav_no_op)}
                            ${L('RT/RW', d.rt_op + '/' + d.rw_op)}
                            ${L('Kelurahan', d.kel_op)}
                            ${L('Kecamatan', d.kec_op)}

                            <hr>
                            <h6 class="fw-bold mb-2">Wajib Pajak</h6>
                            ${L('Nama WP', d.nama_wp)}
                            ${L('Alamat WP', d.alamat_wp)}
                            ${L('RT/RW', d.rt_wp + '/' + d.rw_wp)}
                            ${L('Kelurahan', d.kel_wp)}
                            ${L('Kota', d.kota_wp)}

                            <hr>
                            <h6 class="fw-bold mb-2">Status Penyampaian</h6>
                            ${L('Status', statusMain)}
                            ${L('Status OP', statusOp)}
                            ${L('Status WP', statusWp)}
                            ${L('NOP Benar', d.nop_benar)}
                            ${L('Keterangan Petugas', d.keterangan_petugas)}
                            ${L('Tanggal Penyampaian', d.tgl_penyampaian)}
                            ${L('Nama Penerima', d.nama_penerima)}
                            ${L('HP Penerima', d.hp_penerima)}
                            ${L('Koordinat OP', d.koordinat_op)}

                            <hr>
                            <h6 class="fw-bold mb-2">Evidence</h6>
                            <div class="evidence-box">${evidence}</div>
                        `;
                    })
                    .catch(() => {
                        body.innerHTML =
                            `<div class="text-danger text-center py-3">Gagal memuat data.</div>`;
                    });

                new bootstrap.Offcanvas('#offcanvasDetail').show();
            });

            /* ============================================================
               PREVIEW IMAGE (MODAL)
            ============================================================ */
            document.addEventListener("click", function(e) {
                const img = e.target.closest(".evidence-thumb");
                if (!img) return;

                document.getElementById("previewImageFull").src = img.src;
                new bootstrap.Modal(document.getElementById("modalPreviewImage")).show();
            });

            /* init */
            renderPetugas();
            renderRows();

        })();
    </script>
@endpush
