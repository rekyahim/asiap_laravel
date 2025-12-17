@extends('layouts.admin')

@section('title', 'Pengguna')
@section('breadcrumb', 'Admin / Pengguna')

@section('content')
    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endonce

    <style>
        :root {
            --brand: #5965e8;
            --border: #eef2f7;
            --muted: #6c757d;
        }

        .card {
            border: 0;
            border-radius: 14px;
        }

        .card-header {
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
        }

        .btn-brand {
            background: var(--brand);
            color: #fff;
        }

        .btn-brand:hover {
            background: #4854d1;
            color: #fff;
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 600;
        }

        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .search-input {
            min-width: 260px;
        }

        @media (max-width: 720px) {
            .search-input {
                min-width: 140px;
            }
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
    </style>

    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3">
                    <h6 class="mb-0">Manajemen Pengguna</h6>
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    {{-- Filter status (optional) --}}
                    <form method="GET" action="{{ route('pengguna.index') }}" class="d-flex gap-2 align-items-center">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control form-control-sm search-input" placeholder="Cari username / nama / NIP">
                        <select name="status" class="form-select form-select-sm">
                            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i>
                            Cari</button>
                    </form>

                    <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
                        <i class="bi bi-person-plus me-1"></i> Tambah
                    </button>
                </div>
            </div>

            <div class="card-body p-3">
                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="alert alert-success py-2 mb-3"><i class="bi bi-check2-circle me-1"></i>
                        {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger py-2 mb-3"><i class="bi bi-x-circle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                @if (session('created_user'))
                    @php $nu = session('created_user'); @endphp
                    <div class="alert alert-info mb-3">
                        <div class="fw-semibold">Pengguna dibuat</div>
                        <div>Username: <span class="mono">{{ $nu['USERNAME'] }}</span></div>
                        <div>Password awal: <span class="mono" id="nu-pass">{{ $nu['INITIAL_PASSWORD'] }}</span>
                            <button class="btn btn-sm btn-outline-secondary ms-2"
                                onclick="navigator.clipboard.writeText(document.getElementById('nu-pass').textContent)">Copy</button>
                        </div>
                        <small class="text-muted">Password awal default: <span class="mono">123456</span>. Wajib diganti
                            saat login pertama.</small>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i> Validasi gagal</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:64px">ID</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>NIP</th>
                                <!-- <th>KD_UNIT</th> -->
                                <th>Unit</th>
                                <th>Status</th>
                                <th style="min-width:220px">Hak Akses</th>
                                <th>Password Awal</th>
                                <th style="min-width:220px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $u)
                                <tr>
                                    <td class="text-muted">{{ $u->ID }}</td>
                                    <td class="fw-semibold">{{ $u->USERNAME }}</td>
                                    <td>{{ $u->NAMA }}</td>
                                    <td>{{ $u->JABATAN ?? '—' }}</td>
                                    <td>{{ $u->NIP ?: '—' }}</td>
                                    <!-- <td>{{ $u->KD_UNIT ?: '—' }}</td> -->
                                    <td>{{ $u->NAMA_UNIT ?: '—' }}</td>

                                    {{-- Status badge --}}
                                    <td>
                                        @if ($u->STATUS == 1)
                                            <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>
                                                Aktif</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>
                                                Nonaktif</span>
                                        @endif
                                    </td>

                                    {{-- Hak akses inline form --}}
                                    <td>
                                        <form method="POST" action="{{ route('pengguna.hakakses.update', $u->ID) }}"
                                            class="d-flex gap-2 align-items-center">
                                            @csrf @method('PATCH')
                                            <select name="hakakses_id" class="form-select form-select-sm"
                                                style="min-width:160px">
                                                <option value="">— Tidak ada —</option>
                                                @foreach ($roles as $r)
                                                    <option value="{{ $r->ID }}" @selected((int) $u->HAKAKSES_ID === (int) $r->ID)>
                                                        {{ $r->HAKAKSES }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-primary">Simpan</button>

                                        </form>
                                    </td>

                                    <td class="mono">{{ $u->INITIAL_PASSWORD ?: '—' }}</td>

                                    {{-- Aksi (Edit, Reset, Nonaktifkan) --}}
                                    <td class="align-middle text-center">
                                        <div class="aksi-btns d-flex align-items-center gap-2">

                                            {{-- Ubah --}}
                                            <button type="button" class="btn btn-primary btn-icon" data-bs-toggle="modal"
                                                data-bs-target="#modalEditUser" data-id="{{ $u->ID }}"
                                                data-username="{{ $u->USERNAME }}" data-nama="{{ $u->NAMA }}"
                                                data-jabatan="{{ $u->JABATAN }}" data-nip="{{ $u->NIP }}"
                                                data-kd_unit="{{ $u->KD_UNIT }}" aria-label="Ubah" title="Ubah Pengguna">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>


                                            {{-- Reset Password --}}
                                            <form action="{{ route('pengguna.reset', $u->ID) }}" method="POST"
                                                onsubmit="return confirm('Reset password pengguna ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-icon"
                                                    aria-label="Reset Password" title="Reset Password">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>

                                            {{-- Nonaktif / Aktifkan --}}
                                            @if ($u->STATUS == 1)
                                                {{-- Nonaktifkan --}}
                                                <form action="{{ route('pengguna.destroy', $u->ID) }}" method="POST"
                                                    onsubmit="return confirm('Nonaktifkan pengguna ini?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-danger btn-icon"
                                                        aria-label="Nonaktifkan" title="Nonaktifkan Pengguna">
                                                        <i class="bi bi-person-x"></i>
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Aktifkan --}}
                                                <form action="{{ route('pengguna.activate', $u->ID) }}" method="POST"
                                                    onsubmit="return confirm('Aktifkan pengguna ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-icon"
                                                        aria-label="Aktifkan" title="Aktifkan Pengguna">
                                                        <i class="bi bi-person-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>


                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($users->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $users->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- =========================
       Modal CREATE
     ========================= --}}
    <div class="modal fade" id="modalCreateUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('pengguna.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <input name="USERNAME" class="form-control" required maxlength="100" placeholder="mis. jdoe">
                        <div class="form-text">huruf kecil, angka, titik, minus, atau underscore</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                        <input name="NAMA" class="form-control" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                        <select name="JABATAN" class="form-select" required>
                            <option value="">— Pilih —</option>
                            <option value="PNS">PNS</option>
                            <option value="NON PNS">NON PNS</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIP</label>
                        <input name="NIP" class="form-control" maxlength="50">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                        <select name="KD_UNIT" class="form-select" required>
                            <option value="">— Pilih Unit —</option>
                            @foreach ($units as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hak Akses</label>
                        <select name="HAKAKSES_ID" class="form-select">
                            <option value="">— Tidak ada —</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->ID }}">{{ $r->HAKAKSES }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Password awal otomatis: <span class="mono">123456</span>.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand"><i class="bi bi-check-circle me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================
       Modal EDIT
     ========================= --}}
    <div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form class="modal-content" method="POST" id="editUserForm">
                @csrf @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username</label>
                            <input name="USERNAME" id="edit_username" class="form-control" required maxlength="100">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama</label>
                            <input name="NAMA" id="edit_nama" class="form-control" required maxlength="255">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <select name="JABATAN" id="edit_jabatan" class="form-select" required>
                                <option value="PNS">PNS</option>
                                <option value="NON PNS">NON PNS</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP</label>
                            <input name="NIP" id="edit_nip" class="form-control" maxlength="50">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Unit</label>
                            <select name="KD_UNIT" id="edit_kdunit" class="form-select">
                                <option value="">— Pilih Unit —</option>
                                @foreach ($units as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand"><i class="bi bi-check-circle me-1"></i>Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script: autofill modal edit --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('modalEditUser');

            editModal?.addEventListener('show.bs.modal', event => {
                const btn = event.relatedTarget;
                const id = btn.getAttribute('data-id');

                document.getElementById('edit_username').value = btn.getAttribute('data-username') || '';
                document.getElementById('edit_nama').value = (btn.getAttribute('data-nama') || '');
                document.getElementById('edit_jabatan').value = btn.getAttribute('data-jabatan') || 'PNS';
                document.getElementById('edit_nip').value = btn.getAttribute('data-nip') || '';
                document.getElementById('edit_kdunit').value = btn.getAttribute('data-kd_unit') || '';

                const form = document.getElementById('editUserForm');
                form.action = "{{ url('/admin/pengguna') }}/" + id;
            });
        });
    </script>

    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"], [title]');
        [...tooltipTriggerList].map(t => new bootstrap.Tooltip(t));
    </script>

@endsection
