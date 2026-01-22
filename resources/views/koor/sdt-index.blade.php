@extends('layouts.admin')

@section('title', 'Daftar SDT Modern')

@section('content')

    @push('styles')
        {{-- DataTables CSS --}}
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

        {{-- Select2 --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.6.2/dist/select2-bootstrap-5-theme.min.css">

        <style>
            /* === CUSTOM STYLES === */
            .card-header h2 {
                margin-bottom: 0
            }

            /* DataTables Customization */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 1rem;
            }

            table.dataTable td {
                vertical-align: middle;
            }

            .dataTables_processing {
                background: rgba(255, 255, 255, 0.95);
                z-index: 100;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                font-weight: 600;
                color: #5D87FF;
            }

            /* Stat Chips & Modal Styles */
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

            /* Modal Responsive Styles */
            #modalDetail .modal-dialog {
                max-width: min(96vw, 1400px);
                width: auto;
                margin: 1rem auto;
            }

            #modalDetail .modal-content {
                border-radius: 16px;
                overflow: hidden;
                height: auto;
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
                padding-bottom: 0;
            }

            #modalDetail .table-scroll {
                overflow-x: auto;
                overflow-y: auto;
                max-height: 65vh;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
            }

            @media (max-width: 768px) {
                #modalDetail .modal-dialog {
                    max-width: 98vw;
                    margin: .5rem;
                }

                #modalDetail .table-scroll {
                    max-height: 55vh;
                }
            }

            #modalDetail .card-footer {
                border-top: 1px solid #e5e7eb;
                position: relative;
                bottom: 0;
                background: #fff;
            }

            /* Select2 Overrides */
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

            /* Modal Manual Styles */
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
            }

            #modalPetugasManual .modal-title {
                font-weight: 600;
                color: #0f172a;
            }

            #modalPetugasManual .card-body {
                flex: 1 1 auto;
                padding-bottom: 0;
            }

            #modalPetugasManual .form-label {
                color: #1e293b;
                font-size: .95rem;
                margin-bottom: .4rem;
            }

            #modalPetugasManual .mb-3 {
                margin-bottom: 1.25rem !important;
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

            #modalPetugasManual .card-footer {
                border-top: 1px solid #e2e8f0;
                background: #fff;
                position: relative;
                bottom: 0;
                right: 0;
            }

            /* Modal Edit Styles */
            #modalEdit .modal-content {
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(15, 23, 42, 0.2);
                border: none;
            }

            #modalEdit .modal-title {
                font-weight: 600;
                color: #0f172a;
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

            #modalEdit .form-control.changed {
                border-color: #13DEB9 !important;
                box-shadow: 0 0 0 0.2rem rgba(19, 222, 185, 0.25);
                transition: all 0.25s ease;
            }

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

            .inline-editor {
                min-width: 200px;
                border-radius: 6px;
                padding: 4px 6px;
            }

            .inline-btn {
                margin-left: 6px;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }
        </style>
    @endpush

    <div class="container-lg px-0">
        {{-- Breadcrumb --}}
        <div class="page-breadcrumb">
            <div class="crumbs">
                <span class="crumb active">Daftar SDT</span>
            </div>
        </div>

        {{-- Kartu Daftar --}}
        <div class="card app-card border-0">
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
                    {{-- TABEL DATATABLES SERVER-SIDE --}}
                    <table id="table-sdt" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">NO</th>
                                <th>Nama</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Total Data</th>
                                <th style="width:180px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan dimuat otomatis oleh Yajra DataTables via AJAX POST --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODALS SECTION --}}
    {{-- ========================================= --}}

    {{-- 1. MODAL DETAIL SDT --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xxl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="card app-card m-0 border-0">
                    <div class="card-header d-flex align-items-center justify-content-between bg-white p-3 border-bottom">
                        <h5 class="fw-semibold mb-0">
                            <i class="bi bi-card-list me-2"></i> Detail SDT
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <div id="detail-loading" class="py-5 text-center d-none">
                            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
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
                </div>
            </div>
        </div>
    </div>

    {{-- 2. MODAL TAMBAH PETUGAS MANUAL --}}
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

    {{-- 3. MODAL EDIT SDT --}}
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

@endsection

@push('scripts')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        (function() {
            const BASE = '/koor/sdt';

            /* =========================================
             * 1. INISIALISASI DATATABLES (METHOD: POST)
             * ========================================= */
            const table = $('#table-sdt').DataTable({
                processing: true,
                serverSide: true,
                // Menggunakan AJAX Object untuk Method POST
                ajax: {
                    url: "{{ route('sdt.list-data') }}", // <--- UBAH JADI INI
                    method: 'POST',
                    // Sertakan CSRF di headers untuk keamanan
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'NAMA_SDT',
                        name: 'NAMA_SDT'
                    },
                    {
                        data: 'TGL_MULAI',
                        name: 'TGL_MULAI'
                    },
                    {
                        data: 'TGL_SELESAI',
                        name: 'TGL_SELESAI'
                    },
                    {
                        data: 'details_count',
                        name: 'details_count',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "«",
                        last: "»",
                        next: "›",
                        previous: "‹"
                    }
                },
                drawCallback: function() {
                    initTooltips();
                }
            });

            /* Helper: Tooltips */
            function initTooltips() {
                if (!(window.bootstrap && bootstrap.Tooltip)) return;
                const els = document.querySelectorAll('[title], [data-bs-title]');
                els.forEach(el => {
                    if (!bootstrap.Tooltip.getInstance(el)) new bootstrap.Tooltip(el);
                });
            }

            /* =========================================
             * 2. LOGIKA HAPUS (DELETE)
             * ========================================= */
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-delete');
                if (!btn || btn.disabled) return;

                e.preventDefault();
                const url = btn.getAttribute('data-url');
                const nama = btn.getAttribute('data-nama') || 'SDT ini';
                const csrf = $('meta[name="csrf-token"]').attr('content');

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
                                'Accept': 'application/json' // Tambahkan ini agar Laravel yakin merespon JSON
                            },
                            body: fd
                        })
                        .then(async r => {
                            // 1. Parse respons JSON terlebih dahulu (baik sukses maupun error)
                            const data = await r.json().catch(() => null);

                            // 2. Cek jika status HTTP bukan 200-299 (misal: 422)
                            if (!r.ok) {
                                // Ambil pesan error dari backend ('msg'), atau gunakan fallback
                                const errorMsg = (data && data.msg) ? data.msg :
                                    'Terjadi kesalahan (' + r.status + ')';
                                throw new Error(errorMsg);
                            }

                            return data;
                        })
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                timer: 900,
                                showConfirmButton: false
                            });
                            table.ajax.reload(); // Reload DataTables
                        })
                        .catch((err) => {
                            // 3. Tampilkan pesan error yang sudah kita tangkap di atas
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                // Gunakan err.message karena JavaScript Error object menyimpan pesan di properti .message
                                text: err.message ?? 'Silakan coba lagi.'
                            });
                        });
                });
            });

            /* =========================================
             * 3. LOGIKA MODAL DETAIL (EXISTING LOGIC)
             * ========================================= */
            let RAW_ROWS = [],
                MASTER_PETUGAS = [],
                ACTIVE = null,
                QUERY = '';
            const modalDetail = document.getElementById('modalDetail');
            const loader = document.getElementById('detail-loading');
            const errorEl = document.getElementById('detail-error');
            const bodyEl = document.getElementById('detail-content');
            const elSearch = document.getElementById('d-search');

            // Element Referensi
            const els = {
                nama: document.getElementById('d-nama'),
                mulai: document.getElementById('d-mulai'),
                selesai: document.getElementById('d-selesai'),
                total: document.getElementById('d-total'),
                pets: document.getElementById('d-petugas'),
                rows: document.getElementById('d-rows')
            };

            const norm = s => (s || '').toString().trim();
            const matchesQuery = (row, q) => {
                if (!q) return true;
                const s = q.toString().toLowerCase();
                return (
                    (row.nop && row.nop.toLowerCase().includes(s)) ||
                    (row.tahun && row.tahun.toLowerCase().includes(s)) ||
                    (row.petugas_nama && row.petugas_nama.toLowerCase().includes(s)) ||
                    (row.alamat_op && row.alamat_op.toLowerCase().includes(s)) ||
                    (row.alamat_wp && row.alamat_wp.toLowerCase().includes(s))
                );
            };

            function renderRows() {
                els.rows.innerHTML = '';
                let rows = RAW_ROWS.filter(r => matchesQuery(r, QUERY));
                if (ACTIVE) {
                    rows = rows.filter(r => norm(r.petugas_nama).toLowerCase() === norm(ACTIVE).toLowerCase());
                }

                const frag = document.createDocumentFragment();
                rows.forEach(r => {
                    const isLocked = r.locked === true || r.locked === 1;
                    const btnClass = isLocked ? 'btn-secondary' : 'btn-warning';
                    const disabled = isLocked ? 'disabled' : '';
                    const tr = document.createElement('tr');

                    tr.innerHTML = `
                        <td>${r.id ?? '-'}</td>
                        <td>
                            <button type="button" class="btn btn-sm ${btnClass} btn-edit-row"
                                data-row-id="${r.id}"
                                data-current="${(r.petugas_nama || '').replace(/"/g,'&quot;')}"
                                ${disabled} title="${isLocked ? 'Terkunci' : 'Edit Petugas'}">
                                ${isLocked ? '<i class="bi bi-lock-fill"></i>' : '<i class="bi bi-pencil"></i>'}
                            </button>
                        </td>
                        <td><code>${r.nop ?? '-'}</code></td>
                        <td>${r.tahun ?? '-'}</td>
                        <td>${r.petugas_nama ?? '-'}</td>
                        <td>${r.alamat_op ?? '-'}</td><td>${r.blok_kav_no_op ?? '-'}</td><td>${r.rt_op ?? '-'}</td>
                        <td>${r.rw_op ?? '-'}</td><td>${r.kel_op ?? '-'}</td><td>${r.kec_op ?? '-'}</td>
                        <td>${r.nama_wp ?? '-'}</td><td>${r.alamat_wp ?? '-'}</td><td>${r.blok_kav_no_wp ?? '-'}</td>
                        <td>${r.rt_wp ?? '-'}</td><td>${r.rw_wp ?? '-'}</td><td>${r.kel_wp ?? '-'}</td>
                        <td>${r.kota_wp ?? '-'}</td><td>${r.jatuh_tempo ?? '-'}</td><td>${r.terhutang ?? '-'}</td>
                        <td>${r.pengurangan ?? '-'}</td><td>${r.pbb_harus_dibayar ?? '-'}</td>
                    `;
                    frag.appendChild(tr);
                });
                els.rows.appendChild(frag);
                els.total.textContent = rows.length;

                let totalPBB = 0;
                rows.forEach(r => {
                    const val = Number((r.pbb_harus_dibayar || '0').replace(/[^\d]/g, ''));
                    if (!isNaN(val)) totalPBB += val;
                });
                document.getElementById('d-total-pbb').textContent = 'Rp.' + totalPBB.toLocaleString('id-ID');
            }

            function renderPetugasList() {
                els.pets.innerHTML = '';
                const rows = RAW_ROWS.filter(r => matchesQuery(r, QUERY));
                // Hitung jumlah
                const map = {};
                rows.forEach(r => {
                    const k = norm(r.petugas_nama);
                    if (k) map[k] = (map[k] || 0) + 1;
                });

                const all = document.createElement('button');
                all.className = 'petugas-btn ' + (ACTIVE ? '' : 'active');
                all.textContent = `Semua (${rows.length})`;
                all.onclick = () => {
                    ACTIVE = null;
                    renderPetugasList();
                    renderRows();
                };
                els.pets.appendChild(all);

                MASTER_PETUGAS.forEach(p => {
                    const nm = p.nama;
                    const isActive = norm(ACTIVE).toLowerCase() === nm.toLowerCase();
                    const jml = map[nm] || 0;
                    const b = document.createElement('button');
                    b.className = 'petugas-btn ' + (isActive ? 'active' : '');
                    b.textContent = jml ? `${nm} (${jml})` : nm;
                    b.onclick = () => {
                        ACTIVE = isActive ? null : nm;
                        renderPetugasList();
                        renderRows();
                    };
                    els.pets.appendChild(b);
                });
            }

            if (modalDetail) {
                modalDetail.addEventListener('show.bs.modal', function(e) {
                    const url = e.relatedTarget?.getAttribute('data-url');
                    if (!url) return;
                    loader.classList.remove('d-none');
                    errorEl.classList.add('d-none');
                    bodyEl.classList.add('d-none');
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
                        .then(r => r.ok ? r.json() : Promise.reject())
                        .then(data => {
                            els.nama.textContent = data.nama || '-';
                            els.mulai.textContent = data.mulai || '-';
                            els.selesai.textContent = data.selesai || '-';
                            RAW_ROWS = data.rows || [];
                            MASTER_PETUGAS = (data.petugas || [])
                                .map(p => ({
                                    id: p.id,
                                    nama: p.nama.trim()
                                }))
                                .filter((v, i, a) => a.findIndex(x => x.nama.toLowerCase() === v.nama
                                    .toLowerCase()) === i)
                                .sort((a, b) => a.nama.localeCompare(b.nama));
                            renderPetugasList();
                            renderRows();
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

            // Inline Edit Petugas di Detail
            document.addEventListener("click", function(e) {
                const btn = e.target.closest(".btn-edit-row");
                if (!btn || btn.disabled) return;
                const rowId = btn.getAttribute("data-row-id");
                const current = btn.getAttribute("data-current") || "-";
                const tr = btn.closest("tr");
                const tdPetugas = tr.children[4];
                const oldHTML = tdPetugas.innerHTML;

                tdPetugas.innerHTML = `
                    <select id="edit-petugas-${rowId}" class="inline-editor"></select>
                    <button type="button" class="btn btn-success btn-sm inline-btn btn-save-row">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm inline-btn btn-cancel-row">X</button>
                `;

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
                if (current && current !== "-") $select.append(new Option(current, current, true, true))
                    .trigger("change");

                tr.querySelector(".btn-cancel-row").addEventListener("click", () => {
                    tdPetugas.innerHTML = oldHTML;
                });
                tr.querySelector(".btn-save-row").addEventListener("click", () => {
                    const val = $select.val();
                    const token = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: `${BASE}/row/${rowId}/update-petugas`,
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        data: {
                            petugas: val
                        },
                        success: (json) => {
                            if (json.ok) tdPetugas.innerHTML = json.petugas;
                            else alert(json.msg);
                        },
                        error: () => alert("Gagal update petugas")
                    });
                });
            });


            /* =========================================
             * 4. LOGIKA EDIT SDT (NAMA/TGL)
             * ========================================= */
            const modalEdit = document.getElementById('modalEdit');
            const formEdit = document.getElementById('form-edit-sdt');
            if (modalEdit && formEdit) {
                modalEdit.addEventListener('show.bs.modal', function(e) {
                    const btn = e.relatedTarget;
                    const id = btn?.getAttribute('data-id');
                    formEdit.action = `${BASE}/${id}`;
                    // Reset
                    const btnSubmit = formEdit.querySelector('button[type="submit"]');
                    btnSubmit.disabled = true;
                    // Load Detail
                    fetch(`${BASE}/${id}/detail`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(d => {
                            document.getElementById('edit-nama').value = d.nama;
                            document.getElementById('edit-tgl-mulai').value = d.mulai;
                            document.getElementById('edit-tgl-selesai').value = d.selesai;
                            // Enable detection logic (simplified)
                            ['edit-nama', 'edit-tgl-mulai', 'edit-tgl-selesai'].forEach(id => {
                                document.getElementById(id).oninput = () => btnSubmit.disabled =
                                    false;
                            });
                        });
                });
                formEdit.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    const fd = new FormData(formEdit);
                    fd.set('_method', 'PATCH');
                    fetch(formEdit.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': fd.get('_token')
                            },
                            body: fd
                        })
                        .then(r => r.ok ? r.text() : Promise.reject(r))
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                timer: 1000,
                                showConfirmButton: false
                            });
                            table.ajax.reload(); // Reload Table
                            bootstrap.Modal.getInstance(modalEdit).hide();
                        })
                        .catch(() => Swal.fire('Error', 'Gagal menyimpan perubahan', 'error'));
                });
            }


            /* =========================================
             * 5. LOGIKA TAMBAH PETUGAS MANUAL
             * ========================================= */
            const modalManual = document.getElementById('modalPetugasManual');
            const formManual = document.getElementById('form-manual');
            const $nop = $('#m-nop'),
                $tahun = $('#m-tahun'),
                $petugas = $('#m-petugas');
            let SDT_ID = null,
                apiNOP = null,
                apiTAHUN = null,
                apiEXIST = null;

            if (modalManual) {
                modalManual.addEventListener('show.bs.modal', function(e) {
                    SDT_ID = e.relatedTarget?.getAttribute('data-id');
                    document.getElementById('m-nama').textContent = e.relatedTarget?.getAttribute(
                        'data-nama') || '-';
                    formManual.action = `${BASE}/${SDT_ID}/petugas-manual`;
                    apiNOP = `{{ route('sdt.api.nop', ['id' => '__ID__']) }}`.replace('__ID__', SDT_ID);
                    apiTAHUN = `{{ route('sdt.api.tahun', ['id' => '__ID__']) }}`.replace('__ID__', SDT_ID);
                    apiEXIST = `{{ route('sdt.exists', ['id' => '__ID__']) }}`.replace('__ID__', SDT_ID);

                    // Init Select2 NOP
                    $nop.empty().select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#modalPetugasManual'),
                        ajax: {
                            url: () => apiNOP,
                            dataType: 'json',
                            delay: 250,
                            data: p => ({
                                q: (p.term || '').replace(/\D+/g, ''),
                                page: p.page || 1
                            }),
                            processResults: (d, p) => ({
                                results: d.results,
                                pagination: d.pagination
                            })
                        }
                    });
                    // Init Select2 Petugas
                    $petugas.empty().prop('disabled', true).select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#modalPetugasManual'),
                        ajax: {
                            url: `{{ route('api.pengguna.search') }}`,
                            dataType: 'json',
                            delay: 150,
                            data: p => ({
                                q: p.term,
                                page: p.page || 1
                            }),
                            processResults: d => ({
                                results: d.items,
                                pagination: {
                                    more: !!d.hasMore
                                }
                            })
                        }
                    });
                    $tahun.prop('disabled', true).empty();
                    document.getElementById('m-error').classList.add('d-none');
                    document.querySelector('#modalPetugasManual button[type="submit"]').disabled = true;
                });

                // Logic NOP Change -> Load Tahun
                $nop.on('select2:select', function(e) {
                    const nop = e.params.data.id;
                    $tahun.prop('disabled', true).empty().append(new Option('Loading...', ''));
                    fetch(`${apiTAHUN}?nop=${nop}`).then(r => r.json()).then(d => {
                        $tahun.empty().append(new Option('Pilih tahun...', ''));
                        if (d.results.length) {
                            d.results.forEach(y => $tahun.append(new Option(y.text, y.id)));
                            $tahun.prop('disabled', false);
                        } else {
                            $tahun.append(new Option('Tidak ada tahun', ''));
                        }
                    });
                });

                // Logic Tahun Change -> Check Exist
                $tahun.on('change', function() {
                    const nop = $nop.val(),
                        thn = this.value;
                    if (!nop || !thn) return;
                    fetch(`${apiEXIST}?nop=${nop}&tahun=${thn}`).then(r => r.json()).then(d => {
                        if (d.exists) {
                            $('#m-error').text('Data sudah ada!').removeClass('d-none');
                            $petugas.prop('disabled', true);
                        } else {
                            $('#m-error').addClass('d-none');
                            $petugas.prop('disabled', false);
                        }
                    });
                });

                $petugas.on('change', function() {
                    const btn = document.querySelector('#modalPetugasManual button[type="submit"]');
                    btn.disabled = !this.value;
                });

                formManual.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const fd = new FormData(formManual);
                    fd.set('NOP', $nop.val());
                    fd.set('TAHUN', $tahun.val());
                    fd.set('PENGGUNA_ID', $petugas.val());

                    fetch(formManual.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: fd
                    }).then(r => r.json()).then(d => {
                        if (d.ok) {
                            Swal.fire('Berhasil', d.msg, 'success');
                            bootstrap.Modal.getInstance(modalManual).hide();
                            table.ajax.reload();
                        } else throw new Error(d.msg);
                    }).catch(e => {
                        $('#m-error').text(e.message).removeClass('d-none');
                    });
                });
            }

        })();
    </script>
@endpush
