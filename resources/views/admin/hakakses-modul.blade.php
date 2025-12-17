{{-- resources/views/admin/hakakses-modul.blade.php --}}
@extends('layouts.admin')

@section('title', 'Hak Akses ↔ Modul')
@section('breadcrumb', 'Admin / Hak Akses Modul')

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
                --aksi-offset: 140px;
            }

            .shadow-soft {
                box-shadow: 0 .5rem 1.25rem rgba(18, 26, 41, .08) !important;
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

            .pill {
                border-radius: 999px;
            }

            .table thead th {
                position: sticky;
                top: 0;
                background: #fff;
                z-index: 5;
                box-shadow: inset 0 -1px 0 var(--border);
                text-transform: uppercase;
                letter-spacing: .5px;
                font-size: .8rem;
                color: #5d6d8a;
            }

            .table-hover tbody tr:hover {
                background: #f9fbff;
            }

            .collapse-row td {
                background: #fff;
                padding: 0 !important;
                border-bottom: 1px solid var(--border);
            }

            .module-form-container {
                padding: 1.25rem 1.5rem;
            }

            .switch-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .5rem 1rem;
            }

            @media (min-width:768px) {
                .switch-grid {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
            }

            @media (min-width:992px) {
                .switch-grid {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }
            }

            th.aksi-head {
                padding-left: var(--aksi-offset);
            }

            .aksi-cell {
                display: inline-flex;
                flex-wrap: wrap;
                align-items: center;
                gap: .5rem;
            }

            .btn-kelola {
                width: var(--aksi-offset);
                justify-content: center;
            }
        </style>
    @endpush

    @php
        $total = $roles->count();
    @endphp

    <div class="container-fluid">
        <div class="card shadow-soft">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h6 mb-0">Tabel Hak Akses & Kelola Modul</h2>
                    <span class="badge rounded-pill text-bg-light">{{ $total }}</span>
                </div>
            </div>

            <div class="card-body pt-3 pb-0">
                @if (session('success'))
                    <div class="alert alert-success py-2"><i class="bi bi-check2-circle me-1"></i>{{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger py-2"><i class="bi bi-x-circle me-1"></i>{{ session('error') }}</div>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:72px">ID</th>
                            <th>Hak Akses</th>
                            <th class="text-start aksi-head" style="width:400px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r->ID }}</td>
                                <td class="fw-semibold">{{ $r->HAKAKSES }}</td>
                                <td class="text-start">
                                    <div class="aksi-cell">
                                        {{-- Kelola Modul --}}
                                        <button class="btn btn-brand btn-sm btn-kelola" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $r->ID }}">
                                            <i class="bi bi-sliders me-1"></i>Kelola Modul
                                        </button>

                                        {{-- Edit --}}
                                        <button class="btn btn-ghost btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalEditRole" data-id="{{ $r->ID }}"
                                            data-name="{{ $r->HAKAKSES }}" data-status="{{ (int) $r->STATUS }}">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </button>

                                        {{-- Nonaktifkan / Hapus --}}
                                        <form class="d-inline" action="{{ url('admin/hak-akses/' . $r->ID) }}" method="POST"
                                            onsubmit="return confirm('Nonaktifkan hak akses ini? Data tetap tersimpan (STATUS=0).')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-sm text-danger">
                                                <i class="bi bi-trash3 me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- COLLAPSE: Form mapping modul per role --}}
                            <tr class="collapse-row">
                                <td colspan="4">
                                    <div class="collapse" id="collapse-{{ $r->ID }}">
                                        <div class="module-form-container">
                                            <form method="POST"
                                                action="{{ route('admin.hakakses.modul.update', ['hak' => $r->ID]) }}">
                                                @csrf @method('PATCH')


                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <h6 class="mb-0">Modul diizinkan untuk:
                                                        <span class="text-primary">{{ $r->HAKAKSES }}</span>
                                                    </h6>
                                                    <div class="small text-muted">Centang modul yang boleh diakses.</div>
                                                </div>

                                                @php
                                                    $owned = $r->OWNED_IDS ?? [];
                                                @endphp


                                                <div class="switch-grid mb-3">
                                                    @foreach ($moduls as $m)
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch"
                                                                id="m-{{ $r->ID }}-{{ $m->ID }}"
                                                                name="modul_ids[]" value="{{ $m->ID }}"
                                                                {{ in_array($m->ID, $owned) ? 'checked' : '' }}>

                                                            <label class="form-check-label"
                                                                for="m-{{ $r->ID }}-{{ $m->ID }}">
                                                                {{ $m->NAMA_MODUL }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-ghost pill"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-{{ $r->ID }}">
                                                        <i class="bi bi-x-circle me-1"></i>Tutup
                                                    </button>
                                                    <button class="btn btn-brand pill">
                                                        <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
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
        </div>
    </div>

    {{-- Modal Edit Hak Akses --}}
    <div class="modal fade" id="modalEditRole" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formEditRole" class="modal-content" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Hak Akses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Hak Akses</label>
                        <input id="editRoleName" name="HAKAKSES" class="form-control" required maxlength="100">
                    </div>
                    <input type="hidden" id="editRoleStatusHidden" name="STATUS" value="1">
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
            const modalEdit = document.getElementById('modalEditRole');
            modalEdit.addEventListener('show.bs.modal', (ev) => {
                const btn = ev.relatedTarget;
                const id = btn.getAttribute('data-id');
                const nm = btn.getAttribute('data-name') || '';
                const st = btn.getAttribute('data-status') === '1';

                const form = document.getElementById('formEditRole');
                form.action = "{{ url('admin/hak-akses') }}/" + id;

                document.getElementById('editRoleName').value = nm;
                document.getElementById('editRoleStatusHidden').value = st ? '1' : '0';
            });
        </script>
    @endpush
@endsection
