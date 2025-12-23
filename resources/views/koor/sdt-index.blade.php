@extends('layouts.admin')

@section('title', 'Daftar SDT Modern')
@section('breadcrumb', '')

@section('header_sdt_search')
<meta name="csrf-token" content="{{ csrf_token() }}">

    <form id="header-sdt-search-form" method="GET" action="{{ route('sdt.index') }}">
        <div class="input-group input-group-sm header-search" style="width:clamp(220px, 28vw, 360px);">
            <span class="input-group-text bg-white border-end-0">
                <i class="ti ti-search"></i>
            </span>
            <input id="header-sdt-search-input" type="text" name="q" class="form-control border-start-0"
                placeholder="Cari SDT…" value="{{ request('q') }}" autocomplete="off">
        </div>
    </form>
@endsection

@section('content')
    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endonce

    @push('styles')
        {{-- Select2 --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.6.2/dist/select2-bootstrap-5-theme.min.css">
    @endpush

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

        .card-header h2 {
            margin-bottom: 0
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: .85rem;
            letter-spacing: .5px
        }

        .sdt-card {
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06), 0 2px 6px rgba(0, 0, 0, .04);
            border: 1px solid rgba(0, 0, 0, .03);
            border-radius: .75rem
        }

        .sdt-card:hover {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08), 0 3px 10px rgba(0, 0, 0, .05)
        }

        .stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .stat-chip {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
            flex: 1 1 200px;
            min-width: 220px;
            transition: all 0.2s ease-in-out;
        }

        .table-detail thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 1
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

        .aksi-btns .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
        }

        .aksi-btns .btn-icon i {
            font-size: 1rem;
            line-height: 1;
        }

        @media (prefers-reduced-motion:no-preference) {
            .aksi-btns .btn-icon {
                transition: transform .12s ease, filter .12s ease;
            }

            .aksi-btns .btn-icon:hover {
                transform: translateY(-1px);
                filter: brightness(.98);
            }
        }

        /* ===== Modal Detail Responsive ===== */
        #modalDetail .modal-dialog {
            max-width: min(96vw, 1400px);
            width: auto;
            margin: 1rem auto;
        }

        #modalDetail .modal-content {
            border-radius: 16px;
            overflow: hidden;
            height: auto;
            /* otomatis menyesuaikan isi */
            display: flex;
            flex-direction: column;
        }

        #modalDetail .card {
            height: auto;
            display: flex;
            flex-direction: column;
        }

        #modalDetail .card-body {
            flex: 1 1 auto;
            overflow: visible;
            /* tidak fixed, menyesuaikan isi */
            padding-bottom: 0;
        }

        #modalDetail .table-scroll {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 65vh;
            /* hanya tabel yang scroll */
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        /* Saat zoom out atau in, modal tetap center dan proporsional */
        @media (max-width: 768px) {
            #modalDetail .modal-dialog {
                max-width: 98vw;
                margin: .5rem;
            }

            #modalDetail .table-scroll {
                max-height: 55vh;
            }
        }

        /* Footer tetap nempel di dalam card */
        #modalDetail .card-footer {
            border-top: 1px solid #e5e7eb;
            position: relative;
            bottom: 0;
            background: #fff;
        }

        /* Perbesar readability Select2 */
        .select2-container .select2-selection--single .select2-selection__rendered {
            font-size: 0.95rem;
        }

        .select2-container .select2-results__option {
            font-size: 0.95rem;
        }

        .select2-results__options {
            max-height: 175px !important;
            overflow-y: auto !important;
        }


        .select2-container {
            width: 100% !important;
        }

        /* ===== MODAL TAMBAH PETUGAS MANUAL (Versi Seragam) ===== */
        #modalPetugasManual .modal-dialog {
            max-width: min(95vw, 900px);
            margin: 1rem auto;
        }

        #modalPetugasManual .modal-content {
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(15, 23, 42, 0.2);
            border: none;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: auto;
            /* adaptif tinggi isi */
        }

        /* Header & Title */
        #modalPetugasManual .modal-title {
            font-weight: 600;
            color: #0f172a;
        }

        /* Body */
        #modalPetugasManual .card-body {
            flex: 1 1 auto;
            padding-bottom: 0;
        }

        /* Label dan Field */
        #modalPetugasManual .form-label {
            color: #1e293b;
            font-size: .95rem;
            margin-bottom: .4rem;
        }

        #modalPetugasManual .mb-3 {
            margin-bottom: 1.25rem !important;
        }

        /* Select2 container inside modal */
        #modalPetugasManual .select2-container {
            width: 100% !important;
        }

        #modalPetugasManual .select2-selection {
            border-radius: 10px !important;
            min-height: 44px;
            border: 1px solid #d1d5db !important;
            display: flex;
            align-items: center;
            font-size: .93rem;
            box-shadow: none !important;
        }

        #modalPetugasManual .select2-selection__rendered {
            color: #0f172a !important;
            line-height: 1.4rem;
            padding-left: .75rem !important;
        }

        #modalPetugasManual .select2-selection__arrow {
            height: 100% !important;
            right: .5rem !important;
        }

        #modalPetugasManual .select2-dropdown {
            border-radius: 10px !important;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }

        /* Hover dan fokus pada hasil dropdown Select2 */
        #modalPetugasManual .select2-results__option {
            padding: 8px 12px;
            font-size: .93rem;
            color: #0f172a;
            transition: all 0.15s ease-in-out;
        }

        /* Warna saat di-hover */
        #modalPetugasManual .select2-results__option--highlighted {
            background-color: #2563eb !important;
            color: #fff !important;
            font-weight: 600;
        }

        /* Selected item */
        #modalPetugasManual .select2-results__option[aria-selected="true"] {
            background-color: rgba(37, 99, 235, 0.08) !important;
            color: #1e293b !important;
            font-weight: 600;
        }

        /* Text helper */
        #modalPetugasManual .form-text {
            font-size: .8rem;
            color: #64748b;
            margin-top: .35rem;
            line-height: 1.3;
        }

        /* Tombol */
        #modalPetugasManual .btn-success {
            background-color: #13DEB9;
            border: none;
            padding: .55rem 1.25rem;
            font-weight: 500;
            border-radius: 10px;
            transition: all .2s ease;
        }

        #modalPetugasManual .btn-success:disabled {
            opacity: .6;
            cursor: not-allowed;
            background-color: #9ca3af !important;
        }

        #modalPetugasManual .btn-secondary {
            border-radius: 10px;
            font-weight: 500;
        }

        #modalPetugasManual .btn-close {
            filter: invert(0.6);
        }

        /* Footer */
        #modalPetugasManual .card-footer {
            border-top: 1px solid #e2e8f0;
            background: #fff;
            position: relative;
            bottom: 0;
            right: 0;
        }

        /* Responsif */
        @media (max-width: 576px) {
            #modalPetugasManual .modal-dialog {
                margin: .75rem;
                max-width: 98vw;
            }
        }

        #modalPetugasManual .btn-success {
            transition: all 0.25s ease-in-out;
        }

        #modalEdit .modal-content {
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(15, 23, 42, 0.2);
            border: none;
        }

        #modalEdit .modal-title {
            font-weight: 600;
            color: #0f172a;
        }

        #modalEdit .form-label {
            color: #1e293b;
            font-size: .95rem;
            margin-bottom: .4rem;
        }

        #modalEdit .form-control {
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: .93rem;
            padding: .55rem .75rem;
            box-shadow: none !important;
        }

        #modalEdit .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        #modalEdit .btn-success {
            background-color: #13DEB9;
            border: none;
            padding: .55rem 1.25rem;
            font-weight: 500;
            border-radius: 10px;
        }

        /* Efek highlight untuk field yang sudah diubah */
        #modalEdit .form-control.changed {
            border-color: #13DEB9 !important;
            box-shadow: 0 0 0 0.2rem rgba(19, 222, 185, 0.25);
            transition: all 0.25s ease;
        }

        /* Untuk field yang dikembalikan ke nilai awal */
        #modalEdit .form-control:not(.changed) {
            transition: all 0.25s ease;
        }

        /* Tooltip kecil (opsional) */
        #modalEdit .field-hint {
            font-size: 0.8rem;
            color: #16a34a;
            margin-top: 3px;
            display: none;
        }

        #modalEdit .field-hint.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal.fade .modal-dialog {
            transform: scale(0.98);
            transition: transform .25s ease-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        .inline-editor {
            min-width: 200px;
            border-radius: 6px;
            padding: 4px 6px;
        }

        .inline-btn {
            margin-left: 6px;
        }
    </style>

    <div class="container-lg px-0">
        {{-- breadcrumb --}}
        <div class="page-breadcrumb">
            <div class="crumbs">
                <a href="{{ url('/koor') }}" class="crumb">Koordinator</a>
                <span class="crumb-sep">•</span>
                <span class="crumb active">Daftar SDT</span>
            </div>
        </div>

        {{-- kartu daftar --}}
        <div class="card sdt-card border-0">
            <div class="card-header bg-white p-3 p-md-4 border-bottom-0 d-flex align-items-center justify-content-between">
                <h2 class="h4 mb-0">Daftar SDT</h2>
                <a class="btn btn-primary fw-semibold" href="{{ route('sdt.create') }}">
                    <i class="bi bi-plus-circle me-2"></i>Tambah SDT
                </a>
            </div>

            <div class="card-body p-3 p-md-4">
                @if (session('ok'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('ok') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>NO</th>
                                <th>Nama</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Total Data</th>
                                <th style="width:180px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($list as $r)
                                <tr>
                                    <td>{{ $r->ID }}</td>
                                    <td>{{ $r->NAMA_SDT }}</td>
                                    <td>{{ $r->TGL_MULAI?->format('Y-m-d') }}</td>
                                    <td>{{ $r->TGL_SELESAI?->format('Y-m-d') }}</td>
                                    <td>{{ $r->details_count }}</td>

                                    <td>
                                        <div class="aksi-btns d-flex align-items-center gap-2">
                                            {{-- Tambah Petugas Manual --}}
                                            <button type="button" class="btn btn-success btn-icon btn-add-manual"
                                                data-id="{{ $r->ID }}" data-nama="{{ $r->NAMA_SDT }}"
                                                data-bs-toggle="modal" data-bs-target="#modalPetugasManual"
                                                aria-label="Tambah Petugas Manual" title="Tambah Petugas Manual">
                                                <i class="bi bi-person-plus"></i>
                                            </button>

                                            {{-- (Opsional) Edit Petugas ID_USER — modalnya tetap jika kamu punya --}}
                                            <button type="button" class="btn btn-primary btn-icon btn-edit"
                                                data-id="{{ $r->ID }}" data-nama="{{ $r->NAMA_SDT }}"
                                                data-selected="{{ $r->ID_USER ?? '' }}" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit" aria-label="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            {{-- Detail --}}
                                            <button type="button" class="btn btn-secondary btn-icon btn-detail"
                                                data-url="{{ route('sdt.detail', $r->ID) }}" data-bs-toggle="modal"
                                                data-bs-target="#modalDetail" aria-label="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            {{-- Hapus (soft delete via STATUS=0) --}}
                                            <button type="button" class="btn btn-danger btn-icon btn-delete"
                                                data-id="{{ $r->ID }}" data-nama="{{ $r->NAMA_SDT }}"
                                                data-url="{{ route('sdt.destroy', $r->ID) }}" aria-label="Hapus"
                                                {{ $r->sudah_disampaikan ? 'disabled' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center p-5 text-muted">
                                            <i class="bi bi-inbox fs-2"></i>
                                            <p class="mb-0 mt-2">Belum ada data yang ditambahkan</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @php
                    $from = $list->firstItem();
                    $to = $list->lastItem();
                    $total = $list->total();
                @endphp
                <div class="mt-4 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
                    <div class="text-muted small">
                        @if ($total)
                            Menampilkan <strong>{{ $from }}</strong>–<strong>{{ $to }}</strong> dari
                            <strong>{{ $total }}</strong> data
                        @else
                            Menampilkan 0 data
                        @endif
                    </div>
                    {{ $list->onEachSide(1)->withQueryString()->links('vendor.pagination.modernize') }}
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL DETAIL SDT (versi responsif) ===== -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xxl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="card sdt-card m-0 border-0">
                    <div class="card-header d-flex align-items-center justify-content-between bg-white p-3 border-bottom">
                        <h5 class="fw-semibold mb-0">
                            <i class="bi bi-card-list me-2"></i> Detail SDT
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <div id="detail-loading" class="py-5 text-center d-none">
                            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="small text-muted mt-2">Mengambil data…</div>
                        </div>

                        <div id="detail-error" class="alert alert-danger d-none">Gagal memuat detail. Coba lagi.</div>

                        <div id="detail-content" class="d-none">
                            <div class="d-flex flex-wrap gap-3 mb-3 stat-row">
                                <div class="stat-chip flex-fill flex-md-1">
                                    <div class="text-muted small">Nama SDT</div>
                                    <div class="fw-semibold" id="d-nama">-</div>
                                </div>

                                <div class="stat-chip flex-fill flex-md-1">
                                    <div class="text-muted small">Periode</div>
                                    <div class="fw-semibold">
                                        <span id="d-mulai">-</span> s/d <span id="d-selesai">-</span>
                                    </div>
                                </div>

                                <div class="stat-chip flex-fill flex-md-1">
                                    <div class="text-muted small">Total Item</div>
                                    <div class="fw-semibold"><span id="d-total">0</span> data</div>
                                </div>

                                <div class="stat-chip flex-fill flex-md-1" style="min-width:260px;">
                                    <div class="text-muted small">Total PBB Harus Dibayar</div>
                                    <div class="fw-semibold text-success" id="d-total-pbb">Rp.0</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small mb-1">Petugas (klik untuk filter)</div>
                                <div class="petugas-panel">
                                    <div class="input-group petugas-search mb-2">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" id="d-search" class="form-control"
                                            placeholder="Cari NOP / Tahun / Petugas…">
                                    </div>
                                    <div id="d-petugas" class="petugas-list"></div>
                                </div>
                            </div>

                            <div class="table-responsive table-scroll">
                                <table class="table table-sm table-detail mb-10 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Aksi</th>
                                            <th>NOP</th>
                                            <th>Tahun</th>
                                            <th>Petugas</th>
                                            <th>Alamat OP</th>
                                            <th>Blok/Kav OP</th>
                                            <th>RT</th>
                                            <th>RW</th>
                                            <th>Kel OP</th>
                                            <th>Kec OP</th>
                                            <th>Nama WP</th>
                                            <th>Alamat WP</th>
                                            <th>Blok/Kav WP</th>
                                            <th>RT WP</th>
                                            <th>RW WP</th>
                                            <th>Kel WP</th>
                                            <th>Kota WP</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Terhutang</th>
                                            <th>Pengurangan</th>
                                            <th>PBB Harus Dibayar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="d-rows"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <button id="btn-edit-petugas" type="button" class="btn btn-primary d-none">Edit Petugas</button>

                </div>
            </div>
        </div>
    </div>
    <!-- ===== MODAL TAMBAH PETUGAS MANUAL (Selaras) ===== -->
    <div class="modal fade" id="modalPetugasManual" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="card border-0 rounded-4 m-0">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between p-3">
                        <h5 class="modal-title fw-semibold mb-0">
                            <i class="bi bi-person-plus me-2"></i> Tambah Petugas Manual — <span id="m-nama">-</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <div id="m-error" class="alert alert-danger d-none"></div>
                        <div id="m-ok" class="alert alert-success d-none"></div>

                        <form id="form-manual" method="POST" action="#">
                            @csrf
                            <div class="mb-3">
                                <label for="m-nop" class="form-label fw-semibold">NOP</label>
                                <select id="m-nop" class="form-select" style="width:100%"></select>
                                <div class="form-text">Ketik minimal 4 digit untuk mencari NOP (maks. 5 hasil).</div>
                            </div>

                            <div class="mb-3">
                                <label for="m-tahun" class="form-label fw-semibold">Tahun Pajak</label>
                                <select id="m-tahun" class="form-select" disabled>
                                    <option selected value="">Pilih tahun…</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="m-petugas" class="form-label fw-semibold">Nama Petugas</label>
                                <select id="m-petugas" class="form-select" style="width:100%" disabled></select>
                                <div class="form-text">Ketik beberapa huruf nama; sumber dari Pengguna (role PETUGAS).
                                </div>
                            </div>
                        </form>
                    </div>

                    <div
                        class="card-footer bg-white border-top text-end d-flex flex-wrap justify-content-end gap-2 py-3 px-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" form="form-manual" class="btn btn-success" disabled>
                            <i class="bi bi-check2-circle me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ===== MODAL EDIT SDT ===== -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold" id="modalEditLabel">
                        <i class="bi bi-pencil-square me-2"></i> Edit Data SDT
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <form id="form-edit-sdt" method="POST" action="#">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama SDT</label>
                            <input type="text" class="form-control" id="edit-nama" name="NAMA_SDT">
                            <div class="field-hint">Sudah diubah ✓</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="edit-tgl-mulai" name="TGL_MULAI">
                            <div class="field-hint">Sudah diubah ✓</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="edit-tgl-selesai" name="TGL_SELESAI">
                            <div class="field-hint">Sudah diubah ✓</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditPetugas" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Petugas</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="form-edit-petugas" method="POST">
                    @csrf

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">NOP</label>
                            <input type="text" id="ep-nop" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun Pajak</label>
                            <input type="text" id="ep-tahun" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Petugas</label>
                            <select id="ep-petugas" class="form-control"></select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" id="ep-submit" class="btn btn-primary" disabled>
                            Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    </div>

@endsection

@push('scripts')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

    <script>
        $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

        (function() {
            const BASE = @json(url('/koor/sdt'));

            /* ====== HEADER SEARCH (tetap) ====== */
            (function() {
                const form = document.getElementById('header-sdt-search-form');
                const input = document.getElementById('header-sdt-search-input');
                const clearBtn = document.getElementById('header-search-clear');
                if (!form || !input) return;
                const toggleClear = () => {
                    if (!clearBtn) return;
                    clearBtn.classList.toggle('d-none', !(input.value || '').trim());
                };
                input.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        form.submit();
                    } else if (e.key === 'Escape') {
                        input.value = '';
                        toggleClear();
                    }
                });
                input.addEventListener('input', toggleClear);
                if (clearBtn) clearBtn.addEventListener('click', function() {
                    input.value = '';
                    toggleClear();
                    input.focus({
                        preventScroll: true
                    });
                });
                window.addEventListener('load', toggleClear);
                window.addEventListener('pageshow', toggleClear);
            })();

            /* ====== DETAIL MODAL (tetap) ====== */
            let RAW_ROWS = [],
                MASTER_PETUGAS = [],
                ACTIVE = null,
                QUERY = '';
            const modalDetail = document.getElementById('modalDetail');
            const loader = document.getElementById('detail-loading');
            const errorEl = document.getElementById('detail-error');
            const bodyEl = document.getElementById('detail-content');
            const elNama = document.getElementById('d-nama');
            const elMulai = document.getElementById('d-mulai');
            const elSelesai = document.getElementById('d-selesai');
            const elTotal = document.getElementById('d-total');
            const elPets = document.getElementById('d-petugas');
            const elRows = document.getElementById('d-rows');
            const elSearch = document.getElementById('d-search');

            const norm = s => (s || '').toString().trim();
            const matchesQuery = (row, q) => {
                if (!q) return true;
                const s = q.toString().toLowerCase();

                return (
                    (row.nop && row.nop.toLowerCase().includes(s)) ||
                    (row.tahun && row.tahun.toLowerCase().includes(s)) ||
                    (row.petugas_nama && row.petugas_nama.toLowerCase().includes(s)) ||

                    // 🔍 Tambahan pencarian ALAMAT OP & WP
                    (row.alamat_op && row.alamat_op.toLowerCase().includes(s)) ||
                    (row.alamat_wp && row.alamat_wp.toLowerCase().includes(s))
                );
            };

            function renderRows() {
                elRows.innerHTML = '';
                let rows = RAW_ROWS.filter(r => matchesQuery(r, QUERY));
                if (ACTIVE) {
                    rows = rows.filter(r =>
                        norm(r.petugas_nama).toLowerCase() === norm(ACTIVE).toLowerCase()
                    );
                }


                const frag = document.createDocumentFragment();

                rows.forEach(r => {

                    // ================================
                    // KONDISI TERKUNCI
                    // ================================
                    const isLocked = r.locked === true || r.locked === 1;

                    const btnClass = isLocked ? 'btn-secondary' : 'btn-warning';
                    const disabled = isLocked ? 'disabled' : '';
                    const title = isLocked ?
                        'Terkunci — NOP sudah disampaikan' :
                        'Edit Petugas';

                    const icon = isLocked ?
                        '<i class="bi bi-lock-fill"></i>' :
                        '<i class="bi bi-pencil"></i>';

                    const tr = document.createElement('tr');

                    tr.innerHTML = `
            <td>${r.id ?? '-'}</td>

            <td>
                <button type="button"
                    class="btn btn-sm ${btnClass} btn-edit-row"
                    data-row-id="${r.id}"
data-current="${(r.petugas_nama || '').replace(/"/g,'&quot;')}"
                    ${disabled}
                    title="${title}">
                    ${icon}
                </button>
            </td>

            <td><code>${r.nop ?? '-'}</code></td>
            <td>${r.tahun ?? '-'}</td>
<td data-pengguna-id="${r.pengguna_id ?? ''}">
    ${r.petugas_nama ?? '-'}
</td>

            <td>${r.alamat_op ?? '-'}</td>
            <td>${r.blok_kav_no_op ?? '-'}</td>
            <td>${r.rt_op ?? '-'}</td>
            <td>${r.rw_op ?? '-'}</td>
            <td>${r.kel_op ?? '-'}</td>
            <td>${r.kec_op ?? '-'}</td>

            <td>${r.nama_wp ?? '-'}</td>
            <td>${r.alamat_wp ?? '-'}</td>
            <td>${r.blok_kav_no_wp ?? '-'}</td>
            <td>${r.rt_wp ?? '-'}</td>
            <td>${r.rw_wp ?? '-'}</td>
            <td>${r.kel_wp ?? '-'}</td>
            <td>${r.kota_wp ?? '-'}</td>

            <td>${r.jatuh_tempo ?? '-'}</td>
            <td>${r.terhutang ?? '-'}</td>
            <td>${r.pengurangan ?? '-'}</td>
            <td>${r.pbb_harus_dibayar ?? '-'}</td>
        `;

                    frag.appendChild(tr);
                });

                elRows.appendChild(frag);
                elTotal.textContent = rows.length;

                let totalPBB = 0;
                rows.forEach(r => {
                    const val = Number((r.pbb_harus_dibayar || '0').replace(/[^\d]/g, ''));
                    if (!isNaN(val)) totalPBB += val;
                });
                document.getElementById('d-total-pbb').textContent =
                    'Rp.' + totalPBB.toLocaleString('id-ID');
            }
            // =====================================================
            // TOMBOL EDIT PETUGAS - INLINE EDIT DI MODAL DETAIL
            // =====================================================

            document.addEventListener("click", function(e) {

                if (e.target.closest(".btn-edit-petugas-row")) {

                    const btn = e.target.closest(".btn-edit-petugas-row");
                    const rowId = btn.getAttribute("data-row-id");
                    const petugasAwal = btn.getAttribute("data-petugas");

                    // Simpan ke variabel global (kalau perlu)
                    window.CURRENT_PETUGAS_ROW_ID = rowId;

                    // Tampilkan area edit
                    document.getElementById("view-petugas").classList.add("d-none");
                    document.getElementById("edit-petugas-box").classList.remove("d-none");
                    document.getElementById("edit-petugas-action").classList.remove("d-none");

                    // Inisialisasi select2
                    const el = $("#edit-petugas-select");
                    el.val(null).trigger("change");
                    el.select2({
                        width: "100%",
                        dropdownParent: $("#modalDetailSDT"),
                        ajax: {
                            url: "/koor/pengguna/petugas",
                            dataType: "json",
                            delay: 250,
                            processResults(data) {
                                return {
                                    results: data
                                };
                            }
                        }
                    });

                    // Set nilai petugas awal
                    el.append(new Option(petugasAwal, petugasAwal, true, true)).trigger("change");
                }
            });

            function computeCounts(rows) {
                const map = {};
                rows.forEach(r => {
                    const k = norm(r.petugas_nama);
                    if (!k) return;
                    map[k] = (map[k] || 0) + 1;
                });
                return map;
            }

            function renderPetugasList() {
                elPets.innerHTML = '';
                const filteredRows = RAW_ROWS.filter(r => matchesQuery(r, QUERY));
                const counts = computeCounts(filteredRows);

                const all = document.createElement('button');
                all.type = 'button';
                all.className = 'petugas-btn ' + (ACTIVE ? '' : 'active');
                all.textContent = `Semua (${filteredRows.length})`;
                all.onclick = () => {
                    ACTIVE = null;
                    renderPetugasList();
                    renderRows();
                };
                elPets.appendChild(all);

                MASTER_PETUGAS.forEach(p => {
                    const nm = p.nama;
                    const isActive = norm(ACTIVE).toLowerCase() === nm.toLowerCase();
                    const jml = counts[nm] || 0;

                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'petugas-btn ' + (isActive ? 'active' : '');
                    b.textContent = jml ? `${nm} (${jml})` : nm;

                    b.onclick = () => {
                        ACTIVE = isActive ? null : nm;
                        renderPetugasList();
                        renderRows();
                    };

                    elPets.appendChild(b);
                });
            }

            if (modalDetail) {
                modalDetail.addEventListener('show.bs.modal', function(e) {
                    const url = e.relatedTarget?.getAttribute('data-url');
                    if (!url) return;
                    loader.classList.remove('d-none');
                    errorEl.classList.add('d-none');
                    bodyEl.classList.add('d-none');
                    // btnEdit.classList.add('d-none');
                    // btnEdit.href = '#';
                    RAW_ROWS = [];
                    MASTER_PETUGAS = [];
                    ACTIVE = null;
                    QUERY = '';
                    if (elSearch) elSearch.value = '';

                    fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(async r => {
                            if (!r.ok) throw new Error(await r.text());
                            return r.json();
                        })
                        .then(data => {
                            elNama.textContent = data.nama || '-';
                            elMulai.textContent = data.mulai || '-';
                            elSelesai.textContent = data.selesai || '-';

                            RAW_ROWS = data.rows || [];
                            MASTER_PETUGAS = (data.petugas || [])
                                .filter(p => p && p.nama)
                                .map(p => ({
                                    id: p.id,
                                    nama: p.nama.trim()
                                }))
                                .filter((v, i, a) =>
                                    a.findIndex(x => x.nama.toLowerCase() === v.nama.toLowerCase()) === i
                                )
                                .sort((a, b) => a.nama.localeCompare(b.nama, 'id', {
                                    sensitivity: 'base'
                                }));

                            renderPetugasList();
                            renderRows();
                            // btnEdit.href = url.replace(/\/detail$/, '/edit');
                            // btnEdit.classList.remove('d-none');
                            loader.classList.add('d-none');
                            bodyEl.classList.remove('d-none');
                        })
                        .catch(() => {
                            loader.classList.add('d-none');
                            errorEl.classList.remove('d-none');
                        });
                });
            }

            if (elSearch) {
                let t = null;
                elSearch.addEventListener('input', function() {
                    clearTimeout(t);
                    t = setTimeout(() => {
                        QUERY = this.value || '';
                        renderPetugasList();
                        renderRows();
                    }, 150);
                });
            }

            /* ====== DELETE pakai SweetAlert2 (soft delete) ====== */
            (function() {
                const getCsrf = () =>
                    document.querySelector('meta[name="csrf-token"]')?.content ||
                    document.querySelector('input[name="_token"]')?.value || '';

                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('.btn-delete');
                    if (!btn || btn.disabled) return; // ⬅️ jika disable → stop!

                    e.preventDefault();
                    const url = btn.getAttribute('data-url');
                    const nama = btn.getAttribute('data-nama') || 'SDT ini';
                    const csrf = getCsrf();

                    Swal.fire({
                        title: 'Hapus SDT?',
                        html: `Yakin ingin menghapus <b>${nama}</b>?<br><small>Tindakan ini tidak dapat dibatalkan.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545'
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        const fd = new FormData();
                        fd.append('_token', csrf);
                        fd.append('_method', 'DELETE');

                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: fd
                            })
                            .then(r => {
                                if (!r.ok) throw new Error('HTTP ' + r.status);
                                return r.text();
                            })
                            .then(() => {
                                Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus',
                                        timer: 900,
                                        showConfirmButton: false
                                    })
                                    .then(() => location.reload());
                            })
                            .catch((err) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menghapus',
                                    text: err.message ?? 'Silakan coba lagi.'
                                });
                            });
                    });
                });
            })();

            /* ====== EDIT SDT (NAMA & TANGGAL + Tombol Dinamis) ====== */
            const modalEdit = document.getElementById('modalEdit');
            const formEdit = document.getElementById('form-edit-sdt');
            const inputNama = document.getElementById('edit-nama');
            const inputMulai = document.getElementById('edit-tgl-mulai');
            const inputSelesai = document.getElementById('edit-tgl-selesai');
            const btnSubmit = formEdit?.querySelector('button[type="submit"]');

            let initialValues = {};

            if (modalEdit && formEdit) {
                modalEdit.addEventListener('show.bs.modal', function(e) {
                    const btn = e.relatedTarget;
                    const id = btn?.getAttribute('data-id');
                    const nama = btn?.getAttribute('data-nama') || '-';
                    formEdit.action = `${BASE}/${id}`;
                    console.log('Edit URL:', formEdit.action);

                    // Reset tampilan awal
                    btnSubmit.disabled = true;
                    [inputNama, inputMulai, inputSelesai].forEach(el => {
                        el.classList.remove('changed');
                        const hint = el.closest('.mb-3')?.querySelector('.field-hint');
                        if (hint) hint.classList.remove('active');
                    });

                    // Ambil data detail dari API (agar field terisi otomatis)
                    fetch(`${BASE}/${id}/detail`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(d => {
                            inputNama.value = d.nama || nama;
                            inputMulai.value = d.mulai || '';
                            inputSelesai.value = d.selesai || '';

                            // Simpan nilai awal
                            initialValues = {
                                nama: inputNama.value.trim(),
                                mulai: inputMulai.value,
                                selesai: inputSelesai.value
                            };

                            btnSubmit.disabled = true; // pastikan tetap disable saat awal
                        })
                        .catch(() => console.warn('Gagal load data SDT.'));
                });

                // Fungsi pengecekan perubahan
                function checkChanges() {
                    const namaChanged = inputNama.value.trim() !== (initialValues.nama || '').trim();
                    const mulaiChanged = inputMulai.value !== (initialValues.mulai || '');
                    const selesaiChanged = inputSelesai.value !== (initialValues.selesai || '');
                    const hasChanges = namaChanged || mulaiChanged || selesaiChanged;

                    btnSubmit.disabled = !hasChanges;

                    // Highlight field berubah
                    [{
                            el: inputNama,
                            changed: namaChanged
                        },
                        {
                            el: inputMulai,
                            changed: mulaiChanged
                        },
                        {
                            el: inputSelesai,
                            changed: selesaiChanged
                        },
                    ].forEach(({
                        el,
                        changed
                    }) => {
                        el.classList.toggle('changed', changed);
                        const hint = el.closest('.mb-3')?.querySelector('.field-hint');
                        if (hint) hint.classList.toggle('active', changed);
                    });
                }

                // Pasang listener untuk deteksi perubahan
                [inputNama, inputMulai, inputSelesai].forEach(el => {
                    el.addEventListener('input', checkChanges);
                    el.addEventListener('change', checkChanges);
                });

                // Kirim data via fetch (tidak berubah dari milikmu)
                formEdit.addEventListener('submit', function(ev) {
                    ev.preventDefault();

                    const fd = new FormData(formEdit);

                    // pastikan _method=PATCH ikut terkirim
                    fd.set('_method', 'PATCH');

                    fetch(formEdit.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': fd.get('_token')
                            },
                            body: fd,
                            credentials: 'same-origin' // <-- WAJIB!
                        })
                        .then(r => {
                            if (!r.ok) throw new Error('HTTP ' + r.status);
                            return r.text();
                        })
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data SDT berhasil diperbarui!',
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        })
                        .catch(err => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: err.message || 'Silakan coba lagi.'
                            });
                        });
                });

            }
            // ============================
            // EDIT PETUGAS ONLY
            // ============================

            const modalEP = document.getElementById("modalEditPetugas");
            const formEP = document.getElementById("form-edit-petugas");
            const epNop = document.getElementById("ep-nop");
            const epTahun = document.getElementById("ep-tahun");
            const epPetugas = $("#ep-petugas");
            const epSubmit = document.getElementById("ep-submit");

            let initialPetugas = "";


            /* ====== TAMBAH PETUGAS MANUAL (BARU) ====== */
            const modalManual = document.getElementById('modalPetugasManual');
            const formManual = document.getElementById('form-manual');
            const elNamaManual = document.getElementById('m-nama');
            const elErr = document.getElementById('m-error');
            const elOk = document.getElementById('m-ok');

            const $nop = $('#m-nop'); // Select2 NOP (AJAX)
            const $tahun = $('#m-tahun'); // Dropdown tahun
            const $petugas = $('#m-petugas'); // Select2 petugas (AJAX)

            const showErr = (msg) => {
                if (elErr) {
                    elErr.textContent = msg || 'Terjadi kesalahan.';
                    elErr.classList.remove('d-none');
                }
            };
            const hideErr = () => elErr && elErr.classList.add('d-none');
            const showOk = (msg) => {
                if (elOk) {
                    elOk.textContent = msg || 'Berhasil.';
                    elOk.classList.remove('d-none');
                }
            };
            const hideOk = () => elOk && elOk.classList.add('d-none');

            let SDT_ID = null;
            let apiNOP = null,
                apiTAHUN = null,
                apiEXIST = null;
            const apiPenggunaSearch = @json(route('api.pengguna.search'));

            let nopInitialized = false;
            let petugasInitialized = false;

            function enableSubmit(enable) {
                // Tombol submit di luar form (pakai attribute form="form-manual")
                const btn = document.querySelector('#modalPetugasManual button[type="submit"][form="form-manual"]');
                if (btn) btn.disabled = !enable;
            }


            function resetFormState() {
                hideErr();
                hideOk();
                enableSubmit(false);
                if ($tahun.length) {
                    $tahun.prop('disabled', true).empty().append(new Option('Pilih tahun…', '', true, true));
                }
                if ($petugas.length) {
                    $petugas.val(null).trigger('change');
                    $petugas.prop('disabled', true);
                }
            }

            /* === fungsi baru: cek kondisi semua field === */
            function checkManualFormState() {
                // hideErr();
                const nop = ($nop.val() || '').toString();
                const tahun = ($tahun.val() || '').toString();
                const petugas = ($petugas.val() || '').toString();
                const valid = nop && tahun && petugas && !$tahun.prop('disabled');
                enableSubmit(valid);
            }

            function initNopSelect2() {
                if (nopInitialized || !$nop.length) return;

                $nop.select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Ketik minimal 4 digit NOP…',
                    allowClear: true,
                    minimumInputLength: 4,
                    width: '100%',
                    dropdownParent: $('#modalPetugasManual'),
                    ajax: {
                        url: () => apiNOP,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: (params.term || '').replace(/\D+/g, ''),
                            page: params.page || 1,
                        }),
                        processResults: (data, params) => {
                            params.page = params.page || 1;
                            return {
                                results: data.results || [],
                                pagination: data.pagination || {
                                    more: false
                                }
                            };
                        },
                        cache: true
                    },
                    language: {
                        inputTooShort: () => 'Ketik minimal 4 digit NOP…',
                        searching: () => 'Memuat data...',
                        loadingMore: () => 'Memuat data berikutnya...'
                    }
                });

                $nop.on('select2:select', function(e) {
                    hideErr();
                    hideOk();
                    const nop = e.params.data.id || '';
                    loadTahun(nop);
                });

                $nop.on('select2:clear', function() {
                    resetFormState();
                });

                $nop.on('change', checkManualFormState);

                nopInitialized = true;
            }

            function initPetugasSelect2() {
                if (petugasInitialized || !$petugas.length) return;
                $petugas.select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Ketik nama petugas…',
                    allowClear: true,
                    minimumInputLength: 1,
                    width: '100%',
                    dropdownParent: $('#modalPetugasManual'),
                    ajax: {
                        url: apiPenggunaSearch,
                        dataType: 'json',
                        delay: 150,
                        data: p => ({
                            q: p.term || '',
                            page: p.page || 1
                        }),
                        processResults: d => ({
                            results: (d.items || []),
                            pagination: {
                                more: !!d.hasMore
                            }
                        })
                    }
                });
                $petugas.on('select2:select select2:clear change', checkManualFormState);
                petugasInitialized = true;
            }

            function loadTahun(nop) {
                if (!$tahun.length) return;
                $tahun.prop('disabled', true)
                    .empty()
                    .append(new Option('Memuat tahun…', '', true, true));

                fetch(`${apiTAHUN}?nop=${encodeURIComponent(nop)}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        const years = d.results || [];

                        $tahun.empty();

                        if (years.length === 0) {
                            // Tidak ada tahun tersedia → tampilkan error
                            $tahun.append(new Option('Tidak ada tahun tersedia', '', true, true));
                            $tahun.prop('disabled', true);

                            showErr('Tidak ditemukan Tahun Pajak untuk NOP tersebut.');
                            enableSubmit(false);
                            return;
                        }

                        // Jika ada tahun → tampilkan seperti biasa
                        $tahun.append(new Option('Pilih tahun…', '', true, true));
                        years.forEach(y => {
                            $tahun.append(new Option(y.text || y.id, y.id, false, false));
                        });

                        $tahun.prop('disabled', false);
                        checkManualFormState();
                    })

                    .catch(err => {
                        console.error('Error tahun:', err);
                        $tahun.prop('disabled', true)
                            .empty()
                            .append(new Option('Gagal memuat tahun', '', true, true));
                        showErr('Gagal memuat daftar tahun pajak.');
                        checkManualFormState();
                    });
            }

            function checkExists(nop, tahun) {
                hideErr();

                const btn = document.querySelector('#modalPetugasManual button[type="submit"][form="form-manual"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML =
                        `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memeriksa...`;
                }

                return fetch(`${apiEXIST}?nop=${encodeURIComponent(nop)}&tahun=${encodeURIComponent(tahun)}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.exists) {
                            if (d.has_petugas) {
                                showErr(`Baris sudah ada & sudah punya petugas: ${d.petugas}.`);
                            } else {
                                showErr('Baris dengan NOP + Tahun sudah ada pada SDT ini.');
                            }
                            $petugas.prop('disabled', true);
                        } else {
                            $petugas.prop('disabled', false);
                        }
                    })
                    .catch(() => {
                        showErr('Gagal memeriksa duplikat NOP + Tahun.');
                    })
                    .finally(() => {
                        // Pastikan pengecekan validasi akhir tetap dilakukan setelah fetch
                        if (btn) btn.innerHTML = `<i class="bi bi-check2-circle me-1"></i> Simpan`;
                        setTimeout(checkManualFormState, 200); // kasih jeda agar petugas sudah aktif
                    });
            }



            if (modalManual && formManual) {
                modalManual.addEventListener('show.bs.modal', function(e) {
                    const btn = e.relatedTarget;
                    SDT_ID = btn?.getAttribute('data-id') || null;
                    const nama = btn?.getAttribute('data-nama') || '-';
                    elNamaManual && (elNamaManual.textContent = nama);

                    hideErr();
                    hideOk();
                    enableSubmit(false);

                    // endpoint berdasarkan SDT_ID
                    apiNOP = @json(route('sdt.api.nop', ['id' => '__ID__'])).replace('__ID__', SDT_ID);
                    apiTAHUN = @json(route('sdt.api.tahun', ['id' => '__ID__'])).replace('__ID__', SDT_ID);
                    apiEXIST = @json(route('sdt.exists', ['id' => '__ID__'])).replace('__ID__', SDT_ID);

                    // action form
                    formManual.action = `${BASE}/${SDT_ID}/petugas-manual`;

                    // init Select2 / reset
                    if ($nop.length) {
                        if (!nopInitialized) initNopSelect2();
                        $nop.val(null).trigger('change');
                    }
                    if (!petugasInitialized) initPetugasSelect2();
                    resetFormState();
                });

                $tahun.on('change', async function() {
                    hideErr();
                    hideOk();
                    const nop = ($nop.val() || '').toString();
                    const tahun = (this.value || '').toString();
                    if (!nop || !tahun) {
                        enableSubmit(false);
                        return;
                    }
                    await checkExists(nop, tahun); // sudah otomatis panggil checkManualFormState()
                });


                formManual.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    hideErr();
                    hideOk();
                    enableSubmit(false);

                    const fd = new FormData(formManual);
                    const nop = ($nop.val() || '').toString();
                    const tahun = ($tahun.val() || '').toString();
                    const pet = ($petugas.val() || '').toString();

                    if (!nop || !tahun || !pet) {
                        showErr('Lengkapi NOP, Tahun Pajak, dan Nama Petugas.');
                        enableSubmit(true);
                        return;
                    }

                    fd.set('NOP', nop);
                    fd.set('TAHUN', tahun);
                    fd.set('PENGGUNA_ID', pet); // pet = ID dari select2

                    fetch(formManual.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': fd.get('_token')
                            },
                            body: fd
                        })
                        .then(async r => {
                            const isJSON = r.headers.get('content-type')?.includes('application/json');
                            const payload = isJSON ? await r.json() : {
                                ok: r.ok,
                                msg: await r.text()
                            };
                            if (!r.ok || payload.ok === false) throw new Error(payload.msg ||
                                'Gagal menyimpan.');
                            showOk(payload.msg || 'Berhasil menambahkan petugas.');
                            setTimeout(() => location.reload(), 600);
                        })
                        .catch(err => {
                            showErr(err.message || 'Gagal menambahkan petugas.');
                            enableSubmit(true);
                        });
                });
            }

            /* ====== TOOLTIP (tetap) ====== */
            (function() {
                if (!(window.bootstrap && bootstrap.Tooltip)) return;
                const selector = '.btn-edit, .btn-detail, .btn-delete, .btn-add-manual';
                const forEachBtn = cb => document.querySelectorAll(selector).forEach(cb);
                forEachBtn(el => {
                    if (!el.getAttribute('title') && !el.getAttribute('data-bs-title')) {
                        let t = '';
                        if (el.classList.contains('btn-add-manual')) t = 'Tambah Petugas Manual';
                        else if (el.classList.contains('btn-edit')) t = 'Edit';
                        else if (el.classList.contains('btn-detail')) t = 'Detail';
                        else if (el.classList.contains('btn-delete')) t = 'Hapus';
                        if (t) el.setAttribute('title', t);
                    }
                    new bootstrap.Tooltip(el, {
                        trigger: 'hover',
                        container: 'body',
                        boundary: 'window'
                    });
                    el.addEventListener('click', () => {
                        const inst = bootstrap.Tooltip.getInstance(el);
                        if (inst) inst.hide();
                        el.blur();
                    });
                    el.addEventListener('mouseleave', () => {
                        const inst = bootstrap.Tooltip.getInstance(el);
                        if (inst) inst.hide();
                    });
                });

                function hideAndDisableAllTooltips() {
                    forEachBtn(el => {
                        const inst = bootstrap.Tooltip.getInstance(el);
                        if (inst) {
                            inst.hide();
                            inst.disable();
                        }
                        el.removeAttribute('aria-describedby');
                    });
                    document.querySelectorAll('.tooltip.show, .tooltip.fade').forEach(t => t.remove());
                }

                function enableAllTooltips() {
                    forEachBtn(el => {
                        const inst = bootstrap.Tooltip.getInstance(el);
                        if (inst) inst.enable();
                    });
                }
                document.addEventListener('show.bs.modal', hideAndDisableAllTooltips, true);
                document.addEventListener('hidden.bs.modal', () => {
                    enableAllTooltips();
                    document.querySelectorAll('.tooltip.show, .tooltip.fade').forEach(t => t.remove());
                }, true);
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') hideAndDisableAllTooltips();
                });
            })();

            document.addEventListener("click", async function(e) {
                const btn = e.target.closest(".btn-edit-row");
                if (!btn || btn.disabled) return;

                const rowId = btn.getAttribute("data-row-id");
                const current = btn.getAttribute("data-current") || "-";

                const tr = btn.closest("tr");
                const tdPetugas = tr.children[4]; // kolom PETUGAS

                // Simpan tampilan lama
                const oldHTML = tdPetugas.innerHTML;

                // Ubah jadi editor inline
                tdPetugas.innerHTML = `
        <select id="edit-petugas-${rowId}" class="inline-editor"></select>
        <button type="button" class="btn btn-success btn-sm inline-btn btn-save-row">
            Simpan
        </button>
        <button type="button" class="btn btn-secondary btn-sm inline-btn btn-cancel-row">
            Batal
        </button>
    `;

                /* ============================
                 * INIT SELECT2
                 * ============================ */
                const $select = $(`#edit-petugas-${rowId}`);
                $select.select2({
                    theme: "bootstrap-5",
                    width: "200px",
                    placeholder: "Pilih petugas...",
                    dropdownParent: $("#modalDetail"),
                    ajax: {
                        url: `{{ route('api.pengguna.search') }}`,
                        dataType: "json",
                        delay: 200,
                        data: p => ({
                            q: p.term
                        }),
                        processResults: d => ({
                            results: d.items || []
                        })
                    }
                });

                // Set petugas awal (teks saja, biar aman)
                if (current && current !== "-") {
                    const opt = new Option(current, current, true, true);
                    $select.append(opt).trigger("change");
                }

                /* ============================
                 * BATAL
                 * ============================ */
                tr.querySelector(".btn-cancel-row").addEventListener("click", () => {
                    tdPetugas.innerHTML = oldHTML;
                });

                /* ============================
                 * SIMPAN
                 * ============================ */
                tr.querySelector(".btn-save-row").addEventListener("click", async () => {
                    const val = $select.val(); // ← ID pengguna (INTEGER)

                    if (!val) {
                        alert("Pilih petugas terlebih dahulu");
                        return;
                    }

                    const csrf = document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content");

                    const fd = new FormData();
                    fd.append("petugas", val);

                    let res;
                    try {
                        res = await fetch(`/koor/sdt/row/${rowId}/update-petugas`, {
                            method: "POST",
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                                // "X-CSRF-TOKEN": csrf
                            },
                            credentials: "same-origin", // ⬅️ WAJIB
                            body: fd
                        });
                    } catch (err) {
                        alert("Request gagal (network error)");
                        tdPetugas.innerHTML = oldHTML;
                        return;
                    }

                    if (!res.ok) {
                        alert("Gagal menyimpan (HTTP " + res.status + ")");
                        tdPetugas.innerHTML = oldHTML;
                        return;
                    }

                    const json = await res.json();

                    if (!json.ok) {
                        alert(json.msg || "Gagal menyimpan petugas");
                        tdPetugas.innerHTML = oldHTML;
                        return;
                    }

                    // SUCCESS → tampilkan nama petugas baru
                    tdPetugas.innerHTML =
                        `<span class="fw-semibold text-success">${json.petugas}</span>`;
                });
            });

        })();
    </script>
@endpush
