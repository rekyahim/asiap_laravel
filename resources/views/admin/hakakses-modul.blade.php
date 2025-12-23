@extends('layouts.admin')

@section('title', 'Hak Akses ↔ Modul')
@section('breadcrumb', '')

@section('content')
    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endonce

    @push('styles')
        <style>
            /* === WARNA AKSI KHUSUS HALAMAN INI === */
            .btn-kelola {
                background: #4f46e5;
            }

            .btn-edit {
                background: #f59e0b;
            }

            .btn-delete {
                background: #ef4444;
            }
        </style>
    @endpush

    <div class="container-fluid">
        {{-- breadcrumb --}}
        <div class="page-breadcrumb">
            <div class="crumbs">
                {{-- <a href="{{ url('/koor') }}" class="crumb">Koordinator</a> --}}
                <span class="crumb active">Kelola Hak Akses Modul</span>
                {{-- <span class="crumb-sep">•</span> --}}

            </div>
        </div>
        <div class="card app-card">

            {{-- HEADER --}}
            <div class="card-header bg-white p-3 p-md-4 d-flex align-items-center justify-content-between">
            <h2 class="h6 mb-0">Tabel Hak Akses & Kelola Modul</h2>

            <button class="btn btn-icon btn-add" data-bs-toggle="modal" data-bs-target="#modalCreateRole"
                title="Tambah Hak Akses">
                <i class="bi bi-plus-lg"></i>
            </button>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Hak Akses</th>
                            <th width="220">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($roles as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r->ID }}</td>
                                <td class="fw-semibold">{{ $r->HAKAKSES }}</td>
                                <td>
                                    <div class="aksi-btns d-flex gap-2">

                                        {{-- KELOLA MODUL --}}
                                        <button class="btn btn-icon btn-kelola" data-bs-toggle="collapse"
                                            data-bs-target="#collapse-{{ $r->ID }}" title="Kelola Modul">
                                            <i class="bi bi-sliders"></i>
                                        </button>

                                        {{-- EDIT --}}
                                        <button class="btn btn-icon btn-edit" data-bs-toggle="modal"
                                            data-bs-target="#modalEditRole" data-id="{{ $r->ID }}"
                                            data-name="{{ $r->HAKAKSES }}" data-status="{{ (int) $r->STATUS }}"
                                            title="Edit Hak Akses">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- DELETE --}}
                                        <form action="{{ url('admin/hak-akses/' . $r->ID) }}" method="POST"
                                            onsubmit="return confirm('Nonaktifkan hak akses ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-delete"
                                                title="Nonaktifkan Hak Akses">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                            {{-- COLLAPSE MODUL --}}
                            <tr>
                                <td colspan="3" class="p-0 border-0">
                                    <div class="collapse" id="collapse-{{ $r->ID }}">
                                        <div class="p-3 border-top">

                                            <form method="POST"
                                                action="{{ route('admin.hakakses.modul.update', ['hak' => $r->ID]) }}">
                                                @csrf
                                                @method('PATCH')

                                                <div class="row">
                                                    @foreach ($moduls as $m)
                                                        <div class="col-md-3 mb-2">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="modul_ids[]" value="{{ $m->ID }}"
                                                                    {{ in_array($m->ID, $r->OWNED_IDS ?? []) ? 'checked' : '' }}>
                                                                <label class="form-check-label">
                                                                    {{ $m->NAMA_MODUL }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="text-end mt-3">
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="bi bi-check-circle me-1"></i>Simpan
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEditRole" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formEditRole" class="modal-content" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Hak Akses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="editRoleName" name="HAKAKSES" class="form-control" required>
                    <input type="hidden" id="editRoleStatusHidden" name="STATUS">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <div class="modal fade" id="modalCreateRole" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('hakakses.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Hak Akses
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label fw-semibold">Nama Hak Akses</label>
                    <input type="text" name="HAKAKSES" class="form-control" placeholder="Contoh: Admin, Operator"
                        required>
                    <input type="hidden" name="STATUS" value="1">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-brand">
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalEdit = document.getElementById('modalEditRole');
                modalEdit.addEventListener('show.bs.modal', e => {
                    const btn = e.relatedTarget;
                    document.getElementById('editRoleName').value = btn.dataset.name;
                    document.getElementById('editRoleStatusHidden').value = btn.dataset.status;
                    document.getElementById('formEditRole').action =
                        "{{ url('admin/hak-akses') }}/" + btn.dataset.id;
                });
            });
        </script>
    @endpush
@endsection
