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

        /* Responsive Table adjustments */
        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
            padding: 0.75rem;
        }

        /* Mobile Optimization */
        @media (max-width: 768px) {
            .card-header {
                padding: 0.75rem;
            }
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }
            .btn-sm-mobile {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
            /* Mencegah tabel terlalu lebar */
            .table-responsive {
                border-radius: 8px;
                border: 1px solid var(--border);
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
            <div class="card-header">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-6">
                        <h6 class="mb-0 text-center text-md-start">Manajemen Pengguna</h6>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="d-flex gap-2 justify-content-center justify-content-md-end flex-wrap">
                            <form method="GET" action="{{ route('pengguna.index') }}" class="d-flex gap-2 align-items-center">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </form>

                            <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
                                <i class="bi bi-person-plus me-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-2 p-md-3">
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
                        <div class="fw-semibold small">Pengguna dibuat:</div>
                        <div class="small">Username: <span class="mono">{{ $nu['USERNAME'] }}</span></div>
                        <div class="small">Pass: <span class="mono" id="nu-pass">{{ $nu['INITIAL_PASSWORD'] }}</span>
                            <button class="btn btn-sm btn-outline-secondary p-0 px-1 ms-1"
                                onclick="navigator.clipboard.writeText(document.getElementById('nu-pass').textContent)">Copy</button>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-3 py-2">
                        <ul class="mb-0 ps-3 small">
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
                                <th style="width:50px">ID</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>NIP</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th style="min-width:200px">Hak Akses</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $u)
                                <tr>
                                    <td class="text-muted small">{{ $u->ID }}</td>
                                    <td class="fw-semibold">{{ $u->USERNAME }}</td>
                                    <td class="small">{{ $u->NAMA }}</td>
                                    <td class="small">{{ $u->JABATAN ?? '—' }}</td>
                                    <td class="small">{{ $u->NIP ?: '—' }}</td>
                                    <td class="small text-wrap" style="min-width: 150px;">{{ $u->NAMA_UNIT ?: '—' }}</td>

                                    <td>
                                        @if ($u->STATUS == 1)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>

                                    <td>
                                        <form method="POST" action="{{ route('pengguna.hakakses.update', $u->ID) }}"
                                            class="d-flex gap-1 align-items-center">
                                            @csrf @method('PATCH')
                                            <select name="hakakses_id" class="form-select form-select-sm"
                                                style="min-width:130px; font-size: 0.8rem;">
                                                <option value="">— Pilih —</option>
                                                @foreach ($roles as $r)
                                                    <option value="{{ $r->ID }}" @selected((int) $u->HAKAKSES_ID === (int) $r->ID)>
                                                        {{ $r->HAKAKSES }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-primary btn-sm-mobile">Simpan</button>
                                        </form>
                                    </td>

                                    <td>
                                        <div class="aksi-btns d-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-primary btn-icon btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modalEditUser" data-id="{{ $u->ID }}"
                                                data-username="{{ $u->USERNAME }}" data-nama="{{ $u->NAMA }}"
                                                data-jabatan="{{ $u->JABATAN }}" data-nip="{{ $u->NIP }}"
                                                data-kd_unit="{{ $u->KD_UNIT }}" title="Ubah">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <form action="{{ route('pengguna.reset', $u->ID) }}" method="POST"
                                                onsubmit="return confirm('Reset password?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-icon btn-sm" title="Reset">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>

                                            @if ($u->STATUS == 1)
                                                <form action="{{ route('pengguna.destroy', $u->ID) }}" method="POST"
                                                    onsubmit="return confirm('Nonaktifkan?')">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-danger btn-icon btn-sm" title="Nonaktifkan">
                                                        <i class="bi bi-person-x"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('pengguna.activate', $u->ID) }}" method="POST"
                                                    onsubmit="return confirm('Aktifkan?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-icon btn-sm" title="Aktifkan">
                                                        <i class="bi bi-person-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4 small">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($users->hasPages())
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $users->onEachSide(0)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL CREATE (Mobile Optimized) --}}
    <div class="modal fade" id="modalCreateUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('pengguna.store') }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Pengguna</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                            <input name="USERNAME" class="form-control form-control-sm" required placeholder="jdoe">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Nama <span class="text-danger">*</span></label>
                            <input name="NAMA" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Jabatan <span class="text-danger">*</span></label>
                            <select name="JABATAN" class="form-select form-select-sm" required>
                                <option value="">— Pilih —</option>
                                <option value="PNS">PNS</option>
                                <option value="NON PNS">NON PNS</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">NIP</label>
                            <input name="NIP" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Unit <span class="text-danger">*</span></label>
                            <select name="KD_UNIT" class="form-select form-select-sm" required>
                                <option value="">— Pilih Unit —</option>
                                @foreach ($units as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Hak Akses</label>
                            <select name="HAKAKSES_ID" class="form-select form-select-sm">
                                <option value="">— Tidak ada —</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->ID }}">{{ $r->HAKAKSES }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-brand">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT (Mobile Optimized) --}}
    <div class="modal fade" id="modalEditUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" id="editUserForm">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h6 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Pengguna</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Username</label>
                            <input name="USERNAME" id="edit_username" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Nama</label>
                            <input name="NAMA" id="edit_nama" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Jabatan</label>
                            <select name="JABATAN" id="edit_jabatan" class="form-select form-select-sm" required>
                                <option value="PNS">PNS</option>
                                <option value="NON PNS">NON PNS</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">NIP</label>
                            <input name="NIP" id="edit_nip" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Unit</label>
                            <select name="KD_UNIT" id="edit_kdunit" class="form-select form-select-sm">
                                <option value="">— Pilih Unit —</option>
                                @foreach ($units as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-brand">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // MODAL EDIT USER
    // =========================
    const editModal = document.getElementById('modalEditUser');
    editModal?.addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;
        const id  = btn.getAttribute('data-id');

        document.getElementById('edit_username').value = btn.getAttribute('data-username') || '';
        document.getElementById('edit_nama').value     = btn.getAttribute('data-nama') || '';
        document.getElementById('edit_jabatan').value = btn.getAttribute('data-jabatan') || 'PNS';
        document.getElementById('edit_nip').value      = btn.getAttribute('data-nip') || '';
        document.getElementById('edit_kdunit').value   = btn.getAttribute('data-kd_unit') || '';

        document.getElementById('editUserForm').action =
            "{{ url('/admin/pengguna') }}/" + id;
    });


    // =========================
    // TOOLTIP INIT (AMAN)
    // =========================
    const tooltipTriggerList = document.querySelectorAll('[data-bs-title]');
    tooltipTriggerList.forEach(el => {
        new bootstrap.Tooltip(el);
    });

});
</script>

@endsection