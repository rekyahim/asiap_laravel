{{-- resources/views/admin/hakakses.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Hak Akses')
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

            /* ===== CARD & TABLE ===== */
            .card {
                border: 0;
                border-radius: 18px
            }

            .card-header {
                border-bottom: 1px solid var(--border)
            }

            .table thead th {
                position: sticky;
                top: 0;
                background: #fff;
                z-index: 5;
                font-size: .8rem;
                text-transform: uppercase;
                color: #5d6d8a
            }

            .table-hover tbody tr:hover {
                background: #f9fbff
            }

            /* ===== AKSI ===== */
            th.col-aksi {
                width: 260px
            }

            .aksi-cell {
                display: inline-flex;
                gap: .45rem;
                align-items: center;
            }

            /* ===== ACTION BUTTON STYLE (SESUAI GAMBAR) ===== */
            .btn-aksi {
                width: 36px;
                height: 36px;
                padding: 0;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: none;
                color: #fff;
            }

            .btn-aksi i {
                font-size: 1.05rem
            }

            /* Edit - Biru */
            .btn-aksi.edit {
                background: #5B7CFA
            }

            .btn-aksi.edit:hover {
                background: #4a6cf7
            }

            /* Reset / View - Oranye */
            .btn-aksi.view {
                background: #FFB020
            }

            .btn-aksi.view:hover {
                background: #f5a000
            }

            /* Hapus - Merah */
            .btn-aksi.delete {
                background: #FF8A65
            }

            .btn-aksi.delete:hover {
                background: #ff7043
            }
        </style>
    @endpush

    @php
        $tz = 'Asia/Jakarta';
        $total = method_exists($items, 'total') ? $items->total() : $items->count();
    @endphp

    <div class="container-fluid">
        {{-- breadcrumb --}}
        <div class="page-breadcrumb">
            <div class="crumbs">
                {{-- <a href="{{ url('/koor') }}" class="crumb">Koordinator</a>
                <span class="crumb-sep">•</span> --}}
                <span class="crumb active">Kelola Hak Akses</span>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Manajemen Hak Akses</h6>
                    <span class="badge bg-light text-dark">{{ $total }}</span>
                </div>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
                    <i class="bi bi-plus-circle me-1"></i>Tambah
                </button>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hak Akses</th>
                                <th>Tgl Post</th>
                                <th class="col-aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $row)
                                <tr>
                                    <td>{{ $row->ID }}</td>
                                    <td>{{ $row->HAKAKSES }}</td>
                                    <td>
                                        @if ($row->TGLPOST)
                                            {{ \Carbon\Carbon::parse($row->TGLPOST)->setTimezone($tz)->format('Y-m-d H:i') }}
                                            WIB
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="col-aksi">
                                        <div class="aksi-cell">

                                            {{-- EDIT --}}
                                            <button class="btn-aksi edit" data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                data-id="{{ $row->ID }}" data-name="{{ $row->HAKAKSES }}"
                                                data-bs-title="Edit" data-bs-placement="top">
                                                <i class="bi bi-pencil"></i>
                                            </button>




                                            {{-- HAPUS --}}
                                            <form action="{{ route('hakakses.toggle', $row->ID) }}" method="POST"
                                                data-bs-title="Nonaktifkan" data-bs-placement="top"
                                                onsubmit="return confirm('Nonaktifkan hak akses ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn-aksi delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($items, 'hasPages') && $items->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== MODAL CREATE ===== --}}
    <div class="modal fade" id="modalCreate" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('hakakses.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Hak Akses</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input name="HAKAKSES" class="form-control" required>
                    <input type="hidden" name="STATUS" value="1">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL EDIT ===== --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formEdit" class="modal-content" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Hak Akses</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input id="editName" name="HAKAKSES" class="form-control" required>
                    <input type="hidden" name="STATUS" value="1">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // MODAL EDIT
                const modalEdit = document.getElementById('modalEdit');
                modalEdit.addEventListener('show.bs.modal', e => {
                    const btn = e.relatedTarget;
                    document.getElementById('editName').value = btn.getAttribute('data-name');
                    document.getElementById('formEdit').action =
                        "{{ url('admin/hak-akses') }}/" + btn.getAttribute('data-id');
                });

                // TOOLTIP
                document.querySelectorAll('[data-bs-title]').forEach(el => {
                    new bootstrap.Tooltip(el);
                });

                // SWEETALERT
                @if (session('swal'))
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

@endsection
