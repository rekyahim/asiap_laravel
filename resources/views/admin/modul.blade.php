@extends('layouts.admin')

@section('title', 'Manajemen Modul')
@section('breadcrumb', '')

@section('content')
    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endonce

    @push('styles')
        <style>
            :root {
                --brand: #5965e8;
                --brand-2: #7380ff;
                --border: #eef2f7;
            }

            /* CARD */
            .card {
                border: 0;
                border-radius: 18px;
                background: #fff;
            }

            .shadow-soft {
                box-shadow: 0 .5rem 1.25rem rgba(18, 26, 41, .08) !important;
            }

            .card-header {
                border-bottom: 1px solid var(--border);
                background: #fff;
            }

            /* TABLE */
            .table thead th {
                text-transform: uppercase;
                font-size: .8rem;
                color: #5d6d8a;
            }

            .table-hover tbody tr:hover {
                background: #f9fbff;
            }

            /* BUTTON */
            .btn-brand {
                background: var(--brand);
                border-color: var(--brand);
                color: #fff;
            }

            .btn-brand:hover {
                background: var(--brand-2);
            }

            /* === AKSI FIX === */
            .aksi-cell {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: .5rem;
            }

            .btn-aksi {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                border: none;
            }

            /* warna */
            .btn-aksi.filter {
                background: #4f46e5;
            }

            .btn-aksi.filter:hover {
                background: #4338ca;
            }

            .btn-aksi.edit {
                background: #f59e0b;
            }

            .btn-aksi.edit:hover {
                background: #d97706;
            }

            .btn-aksi.delete {
                background: #ef4444;
            }

            .btn-aksi.delete:hover {
                background: #dc2626;
            }
        </style>
    @endpush

    @php
        $tz = 'Asia/Jakarta';
    @endphp

    <div class="container-fluid">
        {{-- breadcrumb --}}
        <div class="page-breadcrumb">
            <div class="crumbs">
                {{-- <a href="{{ url('/koor') }}" class="crumb">Koordinator</a>
                <span class="crumb-sep">•</span> --}}
                <span class="crumb active">Kelola Modul</span>
            </div>
        </div>
        <div class="card shadow-soft">

            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="mb-0">Manajemen Modul</h6>
                    <span class="badge bg-light text-dark">{{ $moduls->count() }}</span>
                </div>
                <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Modul
                </button>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:70px">ID</th>
                                <th>Nama Modul</th>
                                <th>Lokasi</th>
                                <th>Tgl Post</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($moduls as $m)
                                <tr>
                                    <td class="text-muted">{{ $m->id }}</td>
                                    <td class="fw-semibold">{{ $m->nama_modul }}</td>
                                    <td><code>{{ $m->lokasi_modul }}</code></td>
                                    <td class="text-muted">
                                        {{ $m->tglpost?->setTimezone($tz)->format('Y-m-d H:i') }} WIB
                                    </td>
                                    <td>
                                        <div class="aksi-cell">



                                            {{-- EDIT --}}
                                            <button type="button" class="btn btn-aksi edit" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit" data-id="{{ $m->id }}"
                                                data-nama="{{ $m->nama_modul }}" data-lokasi="{{ $m->lokasi_modul }}"
                                                data-tgl="{{ $m->tglpost?->setTimezone($tz)->format('Y-m-d\TH:i') }}"
                                                data-bs-toggle="tooltip" title="Edit Modul">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            {{-- DELETE --}}
                                            <form method="POST" action="{{ route('modul.destroy', $m->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-aksi delete" data-bs-toggle="tooltip"
                                                    title="Nonaktifkan Modul"
                                                    onclick="return confirm('Nonaktifkan modul ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada data modul
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('modul.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Modul</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Modul</label>
                        <input name="nama_modul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi Modul</label>
                        <input name="lokasi_modul" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-brand">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formEditModul" class="modal-content" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Modul</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Modul</label>
                        <input id="editNama" name="nama_modul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi Modul</label>
                        <input id="editLokasi" name="lokasi_modul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Post</label>
                        <input id="editTgl" type="datetime-local" name="tglpost" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-brand">Simpan</button>
                </div>
            </form>
        </div>
    </div>

<<<<<<< HEAD
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // TOOLTIP
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    new bootstrap.Tooltip(el);
                });

                // MODAL EDIT
                const modal = document.getElementById('modalEdit');
                modal.addEventListener('show.bs.modal', e => {
                    const b = e.relatedTarget;
                    document.getElementById('editNama').value = b.dataset.nama;
                    document.getElementById('editLokasi').value = b.dataset.lokasi;
                    document.getElementById('editTgl').value = b.dataset.tgl;
                    document.getElementById('formEditModul').action =
                        "{{ url('admin/modul') }}/" + b.dataset.id;
                });

            });
        </script>
    @endpush
=======
@push('scripts')

{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // =====================
    // TOOLTIP
    // =====================
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // =====================
    // MODAL EDIT
    // =====================
    const modal = document.getElementById('modalEdit');
    if (modal) {
        modal.addEventListener('show.bs.modal', e => {
            const b = e.relatedTarget;
            document.getElementById('editNama').value   = b.dataset.nama ?? '';
            document.getElementById('editLokasi').value = b.dataset.lokasi ?? '';
            document.getElementById('editTgl').value    = b.dataset.tgl ?? '';
            document.getElementById('formEditModul').action =
                "{{ url('admin/modul') }}/" + b.dataset.id;
        });
    }

    // =====================
    // SWEETALERT (FLASH)
    // =====================
    @if(session('swal'))
        Swal.fire({
            icon: "{{ session('swal.icon') }}",
            title: "{{ session('swal.title') }}",
            position: "{{ session('swal.position') ?? 'center' }}",
            showConfirmButton: false,
            timer: {{ session('swal.timer') ?? 1500 }},
        });
    @endif

});
</script>

@endpush
>>>>>>> edf07a0e45aae85e5e57261fc3c911949ed4dc91
@endsection
