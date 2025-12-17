{{-- resources/views/admin/hakakses.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Hak Akses')
@section('breadcrumb', 'Admin / Hak Akses')

@section('content')
    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endonce

    @push('styles')
        <style>
            :root {
                --brand: #5965e8;
                --brand-2: #7380ff;
                --ink: #131a29;
                --border: #eef2f7;
            }

            .shadow-soft {
                box-shadow: 0 .5rem 1.25rem rgba(18, 26, 41, .08) !important;
            }

            .btn-brand {
                background: var(--brand);
                border-color: var(--brand);
                color: #fff;
            }

            .btn-brand:hover {
                background: var(--brand-2);
                border-color: var(--brand-2);
                color: #fff;
            }

            .btn-ghost {
                background: #fff;
                border: 1px solid rgba(18, 26, 41, .12);
            }

            .btn-ghost:hover {
                background: #f8f9fe;
            }

            .btn-sm i {
                vertical-align: -1px;
            }

            .card {
                border: 0;
                border-radius: 18px;
                background: #fff;
            }

            .card-header {
                border-bottom: 1px solid var(--border);
                background: #fff;
            }

            .table thead th {
                position: sticky;
                top: 0;
                background: #fff;
                z-index: 5;
                box-shadow: inset 0 -1px 0 var(--border);
                font-weight: 600;
                color: #5d6d8a;
                font-size: .8rem;
                text-transform: uppercase;
                letter-spacing: .5px;
            }

            .table tbody tr {
                border-bottom: 1px solid var(--border);
            }

            .table-hover tbody tr:hover {
                background: #f9fbff;
            }

            th.col-aksi {
                text-align: left;
                width: 360px;
            }

            .aksi-cell {
                display: inline-flex;
                flex-wrap: wrap;
                align-items: center;
                gap: .5rem;
            }
        </style>
    @endpush

    @php
        $tz = 'Asia/Jakarta';
        $total = method_exists($items, 'total') ? $items->total() : $items->count();
    @endphp

    <div class="container-fluid">
        <div class="card shadow-soft">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h6 mb-0">Manajemen Hak Akses</h2>
                    <span class="badge rounded-pill text-bg-light">{{ $total }}</span>
                </div>
                <button class="btn btn-brand rounded-1 px-3 btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Hak Akses
                </button>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success py-2"><i class="bi bi-check2-circle me-1"></i>{{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center mb-2 fw-semibold">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                            <span>Validasi gagal! Mohon periksa kembali isian Anda.</span>
                        </div>
                        <ul class="mb-0 ps-4">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                    <h5 class="mb-2 mb-md-0 fw-semibold">Daftar Hak Akses</h5>
                </div>


                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:72px;">ID</th>
                                <th>Hak Akses</th>
                                <th style="width:200px;">Tgl Post</th>
                                <th class="col-aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $row)
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $row->ID }}</td>
                                    <td class="fw-semibold">{{ $row->HAKAKSES }}</td>
                                    <td class="small text-muted">
                                        @php $val = $row->TGLPOST; @endphp
                                        @if ($val)
                                            {{ (is_numeric($val) ? \Carbon\Carbon::createFromTimestamp((int) $val) : \Carbon\Carbon::parse($val))->setTimezone($tz)->format('Y-m-d H:i') }}
                                            WIB
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="col-aksi">
                                        <div class="aksi-cell">
                                            {{-- Edit (modal, tidak pindah halaman) --}}
                                            <button class="btn btn-ghost btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit" data-id="{{ $row->ID }}"
                                                data-name="{{ $row->HAKAKSES }}">
                                                <i class="bi bi-pencil-square me-1"></i>Edit
                                            </button>

                                            {{-- Hapus = set STATUS=0 (soft) via toggle --}}
                                            <form action="{{ route('hakakses.toggle', $row->ID) }}" method="POST"
                                                onsubmit="return confirm('Hapus (sembunyikan) hak akses ini dari tampilan? Data TIDAK dihapus dari database.')">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-ghost btn-sm text-danger">
                                                    <i class="bi bi-trash3 me-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($items, 'hasPages') && $items->hasPages())
                    <div class="mt-3 d-flex justify-content-center">{{ $items->onEachSide(1)->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Modal CREATE – field name huruf besar ===== --}}
    <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('hakakses.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Hak Akses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Hak Akses</label>
                        <input name="HAKAKSES" class="form-control" placeholder="mis. Admin SDT" required maxlength="100">
                    </div>
                    <input type="hidden" name="STATUS" value="1">
                    {{-- jika ingin kirim TGLPOST manual, tambahkan input name="TGLPOST" --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-brand rounded-1 px-4">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== Modal EDIT – kirim HAKAKSES (STATUS disimpan apa adanya) ===== --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formEdit" class="modal-content" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Hak Akses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Hak Akses</label>
                        <input id="editName" name="HAKAKSES" class="form-control" required maxlength="100">
                    </div>
                    {{-- STATUS disembunyikan agar tidak tampil di UI, value diisi sama seperti kondisi saat ini --}}
                    <input type="hidden" id="editStatusHidden" name="STATUS" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-brand rounded-1 px-4">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Modal EDIT: set action + isi field
            const modalEdit = document.getElementById('modalEdit');
            modalEdit.addEventListener('show.bs.modal', (ev) => {
                const btn = ev.relatedTarget;
                const id = btn.getAttribute('data-id');
                const nm = btn.getAttribute('data-name') || '';

                const form = document.getElementById('formEdit');
                form.action = "{{ url('admin/hak-akses') }}/" + id;

                document.getElementById('editName').value = nm;

                // Kalau kamu mau mempertahankan STATUS saat edit,
                // ambil badge di baris yang sama (opsional). Basic-nya tetap 1 (aktif).
                const tr = btn.closest('tr');
                const isActive = true; // default
                document.getElementById('editStatusHidden').value = isActive ? '1' : '0';
            });
        </script>
    @endpush
@endsection
