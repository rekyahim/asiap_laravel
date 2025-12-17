@extends('layouts.admin')

@section('title', 'Daftar SDT Modern')
@section('breadcrumb', 'Koordinator / Daftar SDT')

@section('content')
    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endonce

    <style>
        .card-header h2 {
            margin-bottom: 0;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: .85rem;
            letter-spacing: .5px;
        }

        .sdt-card {
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06), 0 2px 6px rgba(0, 0, 0, .04);
            border: 1px solid rgba(0, 0, 0, .03);
            border-radius: .75rem;
        }

        .sdt-card:hover {
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08), 0 3px 10px rgba(0, 0, 0, .05);
        }

        /* Modal detail */
        .stat-chip {
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: .65rem;
            padding: .75rem 1rem;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        }

        .table-detail thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 1;
        }
    </style>

    <div class="container-lg px-0">
        <div class="card sdt-card border-0">
            <div class="card-header bg-white p-3 p-md-4 border-bottom-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-0">Daftar SDT</h2>
                    <a class="btn btn-primary fw-semibold" href="{{ route('sdt.create') }}">
                        <i class="bi bi-plus-circle me-2"></i>Tambah SDT
                    </a>
                </div>
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
                                <th>Petugas</th>
                                <th style="width:290px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($list as $r)
                                <tr>
                                    <td>{{ $r->ID }}</td>
                                    <td>{{ $r->NAMA_SDT }}</td>
                                    <td>{{ $r->TGL_MULAI?->format('Y-m-d') }}</td>
                                    <td>{{ $r->TGL_SELESAI?->format('Y-m-d') }}</td>
                                    <td>
                                        @php $names = $r->petugas_names ?? collect(); @endphp
                                        @if ($names->isNotEmpty())
                                            @foreach ($names->take(3) as $nm)
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">{{ $nm }}</span>
                                            @endforeach
                                            @if ($names->count() > 3)
                                                <span
                                                    class="badge bg-light text-muted rounded-pill">+{{ $names->count() - 3 }}
                                                    lainnya</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            {{-- EDIT PETUGAS (Modal) --}}
                                            <button type="button" class="btn btn-outline-primary btn-edit-petugas"
                                                data-edit-url="{{ route('sdt.updatePetugas', $r->ID) }}"
                                                data-current="{{ $r->ID_USER ?? '' }}" data-nama="{{ $r->NAMA_SDT }}"
                                                data-bs-toggle="modal" data-bs-target="#modalEditPetugas">
                                                <i class="bi bi-pencil-square me-1"></i> Edit Petugas
                                            </button>

                                            {{-- DETAIL (Modal) --}}
                                            <button type="button" class="btn btn-outline-secondary btn-detail"
                                                data-url="{{ route('sdt.detail', $r->ID) }}" data-bs-toggle="modal"
                                                data-bs-target="#modalDetail">
                                                <i class="bi bi-eye me-1"></i> Detail
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
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

    {{-- MODAL DETAIL (sudah ada) --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-card-list me-2"></i> Detail SDT
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="detail-loading" class="py-5 text-center d-none">
                        <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                        <div class="small text-muted mt-2">Mengambil data…</div>
                    </div>

                    <div id="detail-error" class="alert alert-danger d-none">
                        Gagal memuat detail. Coba lagi.
                    </div>

                    <div id="detail-content" class="d-none">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="stat-chip">
                                    <div class="text-muted small">Nama SDT</div>
                                    <div class="fw-semibold" id="d-nama">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-chip">
                                    <div class="text-muted small">Periode</div>
                                    <div class="fw-semibold"><span id="d-mulai">-</span> s/d <span id="d-selesai">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-chip">
                                    <div class="text-muted small">Total Item</div>
                                    <div class="fw-semibold"><span id="d-total">0</span> data</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small mb-1">Petugas</div>
                            <div id="d-petugas" class="d-flex flex-wrap gap-2"></div>
                        </div>

                        <div class="table-responsive" style="max-height: 420px;">
                            <table class="table table-sm table-detail mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:90px">ID</th>
                                        <th style="width:200px">NOP</th>
                                        <th style="width:120px">Tahun</th>
                                        <th>Petugas (dt_sdt)</th>
                                    </tr>
                                </thead>
                                <tbody id="d-rows"><!-- diisi via JS --></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- tombol ini sekarang opsional, karena kita punya modal Edit khusus --}}
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT PETUGAS (PARTIAL BARU) --}}
    @include('koor.partials.sdt-editpetugas-modal')
@endsection

@push('scripts')
    <script>
        (function() {
            // DETAIL
            const modal = document.getElementById('modalDetail');
            const loader = document.getElementById('detail-loading');
            const errorEl = document.getElementById('detail-error');
            const bodyEl = document.getElementById('detail-content');

            const elNama = document.getElementById('d-nama');
            const elMulai = document.getElementById('d-mulai');
            const elSelesai = document.getElementById('d-selesai');
            const elTotal = document.getElementById('d-total');
            const elPets = document.getElementById('d-petugas');
            const elRows = document.getElementById('d-rows');

            modal.addEventListener('show.bs.modal', function(e) {
                const url = e.relatedTarget?.getAttribute('data-url');
                if (!url) return;

                loader.classList.remove('d-none');
                errorEl.classList.add('d-none');
                bodyEl.classList.add('d-none');
                elRows.innerHTML = '';
                elPets.innerHTML = '';

                fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(async r => {
                        if (!r.ok) {
                            throw new Error(await r.text() || ('HTTP ' + r.status));
                        }
                        return r.json();
                    })
                    .then(data => {
                        elNama.textContent = data.nama || '-';
                        elMulai.textContent = data.mulai || '-';
                        elSelesai.textContent = data.selesai || '-';
                        elTotal.textContent = (data.rows || []).length;

                        (data.petugas || []).forEach(nm => {
                            const span = document.createElement('span');
                            span.className =
                                'badge bg-secondary-subtle text-secondary-emphasis rounded-pill';
                            span.textContent = nm;
                            elPets.appendChild(span);
                        });
                        if (!data.petugas || !data.petugas.length) {
                            elPets.innerHTML = '<span class="text-muted">-</span>';
                        }

                        const frag = document.createDocumentFragment();
                        (data.rows || []).forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML =
                                `<td>${row.id ?? '-'}</td>
             <td><code>${row.nop ?? '-'}</code></td>
             <td>${row.tahun ?? '-'}</td>
             <td>${row.petugas ?? '-'}</td>`;
                            frag.appendChild(tr);
                        });
                        elRows.appendChild(frag);

                        loader.classList.add('d-none');
                        bodyEl.classList.remove('d-none');
                    })
                    .catch(err => {
                        loader.classList.add('d-none');
                        errorEl.classList.remove('d-none');
                        console.error('DETAIL ERROR:', err);
                    });
            });
        })();
    </script>
@endpush
