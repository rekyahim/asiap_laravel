@extends('layouts.admin')

@section('title', 'Daftar SDT Modern')
@php($forceMenu = 'riwayat_petugas')

@section('breadcrumb', '')

@section('content')
    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endonce

    @push('styles')
        <style>
            .page-breadcrumb {
                margin: -.25rem 0 1rem 0
            }

            .crumbs {
                font-size: .9rem
            }

            .crumb {
                color: #6c757d;
                text-decoration: none;
                transition: color .15s ease
            }

            .crumb:hover {
                color: #212529;
                text-decoration: underline
            }

            .crumb.active {
                font-weight: 700;
                color: #212529;
                pointer-events: none;
                text-decoration: none
            }

            .crumb-sep {
                margin: 0 .35rem;
                color: #adb5bd
            }

            .petugas-panel {
                border: 1px solid rgba(0, 0, 0, .06);
                border-radius: .75rem;
                background: #fff;
                box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
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

            .petugas-btn {
                border: 1px solid rgba(0, 0, 0, .08);
                background: #f8f9fa;
                color: #495057;
                padding: .35rem .6rem;
                border-radius: 999px;
                font-size: .85rem;
                line-height: 1;
                cursor: pointer;
                user-select: none
            }

            .petugas-btn:hover {
                filter: brightness(.96)
            }

            .petugas-btn.active {
                background: #0d6efd;
                color: #fff;
                border-color: #0d6efd
            }

            .card-header h2 {
                margin-bottom: 0
            }

            .stat-chip {
                border: 1px solid rgba(0, 0, 0, .06);
                border-radius: .65rem;
                padding: .75rem 1rem;
                background: #fff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, .04)
            }

            .table thead th {
                font-weight: 600;
                text-transform: uppercase;
                font-size: .85rem;
                letter-spacing: .5px
            }

            .aksi-btns .btn-icon {
                width: 36px;
                height: 36px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: .5rem
            }

            .aksi-btns .btn-icon i {
                font-size: 1rem
            }

            @media (prefers-reduced-motion:no-preference) {
                .aksi-btns .btn-icon {
                    transition: transform .12s ease, filter .12s ease
                }

                .aksi-btns .btn-icon:hover {
                    transform: translateY(-1px);
                    filter: brightness(.98)
                }
            }
        </style>
    @endpush

    <div class="row">
        {{-- breadcrumbs (pakai gaya yang sekarang) --}}
        <div class="page-breadcrumb">
            <div class="crumbs">
                <span class="crumb active">Riwayat SDT Petugas</span>
            </div>
        </div>

        <div class="row g-1">

            {{-- Filter Tahun --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-3">Filter Tahun SDT</h5>

                        <form method="get" action="{{ route('riwayat.petugas') }}">
                            <div class="mb-3">
                                <label for="year" class="form-label">Tahun</label>
                                <select id="year" name="year" class="form-select" onchange="this.form.submit()">
                                    <option value="">— semua tahun —</option>
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <noscript><button class="btn btn-primary">Tampilkan</button></noscript>
                        </form>

                        <div class="mt-3 text-muted small">
                            Pilih tahun untuk melihat daftar SDT yang dimulai pada tahun tersebut.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat SDT --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title fw-semibold mb-0">Riwayat SDT</h5>
                            @if ($year)
                                <span class="badge bg-primary-subtle text-primary-emphasis">Tahun:
                                    {{ $year }}</span>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:80px">NO</th>
                                        <th>Nama SDT</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th>Total Data</th> <!-- Tambah kolom -->
                                        <th style="width:120px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $r)
                                        <tr>
                                            <td>{{ $r->ID }}</td>
                                            <td>{{ $r->NAMA_SDT }}</td>
                                            <td>{{ $r->TGL_MULAI instanceof \Carbon\Carbon ? $r->TGL_MULAI->format('Y-m-d') : $r->TGL_MULAI }}
                                            </td>
                                            <td>{{ $r->TGL_SELESAI instanceof \Carbon\Carbon ? $r->TGL_SELESAI->format('Y-m-d') : $r->TGL_SELESAI }}
                                            </td>
                                            <td>{{ $r->details_count ?? 0 }}</td> <!-- Jumlah record -->
                                            <td>
                                                <div class="aksi-btns d-flex align-items-center gap-2">
                                                    <a href="{{ route('sdt.show', $r->ID) }}"
                                                        class="btn btn-secondary btn-icon" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <a href="{{ route('sdt.export', $r->ID) }}"
                                                        class="btn btn-success btn-icon" title="Export SDT">
                                                        <i class="bi bi-file-earmark-excel"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada SDT.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $rows->withQueryString()->links() }}
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- MODAL DETAIL PER BARIS -->
        <div class="modal fade" id="modalRowDetail" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Detail Item SDT</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="row-detail-body" class="p-2">
                            <!-- JS akan mengisi detail di sini -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script>
            (function() {

                /* =======================================================
                   1) DETAIL PER BARIS (tetap dipakai)
                ======================================================== */
                document.addEventListener("click", function(e) {
                    if (!e.target.classList.contains("btn-detail-row")) return;

                    const id = e.target.dataset.id;
                    const url = `/koor/riwayat/${id}/detail`;

                    const body = document.getElementById("row-detail-body");
                    body.innerHTML = `<div class="text-center py-3">Loading...</div>`;

                    fetch(url)
                        .then(r => r.json())
                        .then(data => {
                            if (data.error) {
                                body.innerHTML =
                                    `<div class="text-danger text-center py-3">${data.message}</div>`;
                                return;
                            }

                            body.innerHTML = `
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary">Informasi SDT</h6>
                        <div class="row">
                            <div class="col-md-6"><strong>Nama SDT:</strong> ${data.nama_sdt}</div>
                            <div class="col-md-6"><strong>Petugas:</strong> ${data.petugas}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-primary">Objek Pajak (OP)</h6>
                        <div class="row">
                            <div class="col-md-6"><strong>NOP:</strong> ${data.nop}</div>
                            <div class="col-md-6"><strong>Tahun:</strong> ${data.tahun}</div>
                            <div class="col-md-12"><strong>Alamat OP:</strong> ${data.alamat_op}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-primary">Wajib Pajak (WP)</h6>
                        <div class="row">
                            <div class="col-md-6"><strong>Nama WP:</strong> ${data.nama_wp}</div>
                            <div class="col-md-6"><strong>Kota WP:</strong> ${data.kota_wp}</div>
                            <div class="col-md-12"><strong>Alamat WP:</strong> ${data.alamat_wp}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-primary">Status Penyampaian</h6>
                        <div class="row">
                            <div class="col-md-6"><strong>Status Penyampaian:</strong> ${data.status_penyampaian}</div>
                            <div class="col-md-6"><strong>Status OP:</strong> ${data.status_op}</div>
                            <div class="col-md-6"><strong>Status WP:</strong> ${data.status_wp}</div>
                            <div class="col-md-12"><strong>Keterangan Petugas:</strong> ${data.keterangan_petugas}</div>
                            <div class="col-md-6"><strong>Tanggal Penyampaian:</strong> ${data.tgl_penyampaian}</div>
                            <div class="col-md-6"><strong>Nama Penerima:</strong> ${data.nama_penerima}</div>
                            <div class="col-md-6"><strong>HP Penerima:</strong> ${data.hp_penerima}</div>
                            <div class="col-md-12"><strong>Koordinat OP:</strong> ${data.koordinat_op}</div>
                            <div class="col-md-12 mt-2">
                                <strong>Evidence:</strong><br>${data.evidence_html}
                            </div>
                        </div>
                    </div>
                `;

                            const modal = new bootstrap.Modal(document.getElementById("modalRowDetail"));
                            modal.show();
                        })
                        .catch(err => {
                            body.innerHTML =
                                `<div class="text-danger text-center py-3">Terjadi error: ${err}</div>`;
                        });
                });

            })();
        </script>
    @endpush
