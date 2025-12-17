@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
    @php
        $logColors = [
            'sdt' => 'primary',
            'dt_sdt' => 'info',
            'penyampaian' => 'success',
            'import' => 'warning',
            'pengguna' => 'secondary',
            'hak_akses' => 'dark',
            'hakakses_modul' => 'secondary',
            'modul' => 'info',
        ];

        $logTitles = [
            'sdt' => 'SDT',
            'dt_sdt' => 'Detail SDT',
            'penyampaian' => 'Penyampaian',
            'import' => 'Import',
            'pengguna' => 'Pengguna',
            'hak_akses' => 'Hak Akses',
            'hakakses_modul' => 'Akses Modul',
            'modul' => 'Modul',
        ];
    @endphp

    <div class="card shadow-sm border-0">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-clock-history me-2"></i>Activity Log
                </h5>
            </div>

            {{-- FILTER --}}
            <form class="row g-2 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari aktivitas..."
                        value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="log" class="form-select">
                        <option value="">Semua Modul</option>
                        @foreach ($logNames as $ln)
                            <option value="{{ $ln }}" @selected(request('log') === $ln)>
                                {{ strtoupper($ln) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="event" class="form-select">
                        <option value="">Semua Event</option>
                        <option value="created" @selected(request('event') === 'created')>Create</option>
                        <option value="updated" @selected(request('event') === 'updated')>Update</option>
                        <option value="deleted" @selected(request('event') === 'deleted')>Delete</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="user" class="form-select">
                        <option value="">Semua User</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" @selected(request('user') == $u->id)>
                                {{ $u->NAMA ?? 'User' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>
                </div>
            </form>

            {{-- TIMELINE --}}
            <ul class="timeline">
                @forelse($logs as $log)
                    @php
                        $color = $logColors[$log->log_name] ?? 'secondary';
                        $title = $logTitles[$log->log_name] ?? strtoupper($log->log_name);
                    @endphp

                    <li class="timeline-item">
                        <span class="timeline-point bg-{{ $color }}"></span>

                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-{{ $color }} me-2">
                                        {{ $title }}
                                    </span>

                                    <strong>{{ ucfirst($log->event ?? '-') }}</strong>
                                    <span class="text-muted">
                                        oleh
                                        @if ($log->causer)
                                            <strong>{{ $log->causer->NAMA }}</strong>
                                            @if ($log->causer->NAMA_UNIT || $log->causer->HAKAKSES)
                                                <span class="text-muted">
                                                    —
                                                    {{ $log->causer->NAMA_UNIT ?? '-' }}
                                                    @if ($log->causer->HAKAKSES)
                                                        ({{ $log->causer->HAKAKSES }})
                                                    @endif
                                                </span>
                                            @endif
                                        @else
                                            <em>System</em>
                                        @endif
                                    </span>

                                </div>

                                <small class="text-muted">
                                    {{ optional($log->created_at)->format('d M Y H:i') }}
                                </small>
                            </div>

                            <div class="mt-2">
                                {{ $log->description }}
                            </div>

                            <button class="btn btn-sm btn-outline-secondary mt-2 btn-detail" data-id="{{ $log->id }}">
                                <i class="bi bi-eye me-1"></i>Detail
                            </button>
                        </div>
                    </li>
                @empty
                    <li class="text-center text-muted py-4">
                        Tidak ada aktivitas
                    </li>
                @endforelse
            </ul>

            {{ $logs->links() }}
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="logDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>Detail Aktivitas
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="log-detail-content">
                        Memuat data...
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 2rem;
            list-style: none;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: .7rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-point {
            position: absolute;
            left: .25rem;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            top: .3rem;
        }

        .timeline-content {
            background: var(--bs-body-bg);
            border: 1px solid #e9ecef;
            border-radius: .75rem;
            padding: 1rem;
        }

        /* OPSI D */
        .change-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 14px;
            background: #fafafa;
            margin-bottom: 12px;
        }

        .change-title {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .change-body {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 12px;
        }

        .change-before,
        .change-after {
            background: #fff;
            border-radius: 8px;
            padding: 12px;
        }

        .change-before {
            border-left: 4px solid #dc3545;
        }

        .change-after {
            border-left: 4px solid #198754;
        }

        .change-arrow {
            font-size: 20px;
            font-weight: bold;
            color: #6c757d;
            display: flex;
            align-items: center;
        }
    </style>
@endpush

@push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 2rem;
            list-style: none;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: .7rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-point {
            position: absolute;
            left: .25rem;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            top: .3rem;
        }

        .timeline-content {
            background: var(--bs-body-bg);
            border: 1px solid #e9ecef;
            border-radius: .75rem;
            padding: 1rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        /* ==============================
         * STATUS → TEKS MANUSIA
         * ============================== */
        const STATUS_TRANSLATE = {
            STATUS_PENYAMPAIAN: {
                1: 'Tersampaikan',
                0: 'Belum Tersampaikan'
            },
            STATUS_OP: {
                1: 'Ditemukan',
                2: 'Tidak Ditemukan',
                0: 'Tidak Diketahui'
            },
            STATUS_WP: {
                1: 'Ditemukan',
                2: 'Tidak Ditemukan',
                0: 'Tidak Diketahui'
            }
        };

        /* LABEL RAMAH */
        const FIELD_LABEL = {
            NAMA_SDT: 'Nama SDT',
            TGL_MULAI: 'Tanggal Mulai',
            TGL_SELESAI: 'Tanggal Selesai',
            PETUGAS_SDT: 'Petugas',
            NOP: 'NOP',
            TAHUN: 'Tahun',
            STATUS_PENYAMPAIAN: 'Status Penyampaian',
            STATUS_OP: 'Status Objek Pajak',
            STATUS_WP: 'Status Wajib Pajak',
            KETERANGAN_PETUGAS: 'Keterangan',
            NAMA_PENERIMA: 'Nama Penerima',
            HP_PENERIMA: 'HP Penerima'
        };

        function humanValue(field, value) {
            if (value === null || value === undefined) return '-';

            if (typeof value === 'number' && STATUS_TRANSLATE[field]?.[value] !== undefined) {
                return STATUS_TRANSLATE[field][value];
            }
            if (value === '') return '-';
            return value;
        }

        /* ==============================
         * OPSI D – PERUBAHAN
         * ============================== */
        function renderChanges(oldData = {}, newData = {}) {
            const keys = new Set([...Object.keys(oldData), ...Object.keys(newData)]);
            let html = '';

            keys.forEach(key => {
                const oldVal = humanValue(key, oldData[key]);
                const newVal = humanValue(key, newData[key]);
                if (oldVal === newVal) return;

                html += `
        <div class="mb-3 p-3 border rounded bg-light">
            <div class="fw-semibold mb-2">${FIELD_LABEL[key] ?? key}</div>
            <div class="row">
                <div class="col-md-6 text-danger">
                    <small class="text-muted">Sebelum</small>
                    <div>${oldVal}</div>
                </div>
                <div class="col-md-6 text-success">
                    <small class="text-muted">Sesudah</small>
                    <div>${newVal}</div>
                </div>
            </div>
        </div>`;
            });

            return html || `<div class="text-muted">Tidak ada perubahan detail.</div>`;
        }

        /* ==============================
         * INFO BLOCK (CREATE / IMPORT)
         * ============================== */
        function renderInfo(props) {
            let html = '';
            Object.entries(props).forEach(([group, val]) => {
                if (typeof val !== 'object') return;

                html += `<div class="mb-3">
            <div class="fw-semibold mb-1">${group.toUpperCase()}</div>
            <ul>`;
                Object.entries(val).forEach(([k, v]) => {
                    html += `<li><strong>${FIELD_LABEL[k] ?? k}:</strong> ${humanValue(k,v)}</li>`;
                });
                html += `</ul></div>`;
            });
            return html || `<div class="text-muted">Tidak ada detail tambahan.</div>`;
        }

        /* ==============================
         * MODAL HANDLER
         * ============================== */
        document.addEventListener('click', e => {
            const btn = e.target.closest('.btn-detail');
            if (!btn) return;

            const modal = new bootstrap.Modal('#logDetail');
            const box = document.getElementById('log-detail-content');
            box.innerHTML = 'Memuat detail...';
            modal.show();

            fetch(`/admin/log/${btn.dataset.id}`)
                .then(r => r.json())
                .then(res => {
                    const props = res.properties || {};
                    const oldData = props.old;
                    const newData = props.new;

                    /* ==============================
                     * FORMAT WAKTU RAMAH
                     * ============================== */
                    function formatDate(dateStr) {
                        if (!dateStr) return '-';
                        const d = new Date(dateStr);
                        return d.toLocaleString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }

                    /* ==============================
                     * RINGKASAN (FIX PELAKU & WAKTU)
                     * ============================== */
                    let html = `
    <div class="mb-4">
        <div class="fw-semibold mb-2">Ringkasan</div>
        <table class="table table-sm">
            <tr>
                <th width="35%">Modul</th>
                <td>${res.log_name?.toUpperCase() ?? '-'}</td>
            </tr>
            <tr>
                <th>Aksi</th>
                <td>${res.event ?? '-'}</td>
            </tr>
            <tr>
  <th>Pelaku</th>
  <td>
    ${
      res.causer
        ? `${res.causer.NAMA}
               ${res.causer.NAMA_UNIT ? ' — ' + res.causer.NAMA_UNIT : ''}
               ${res.causer.HAKAKSES ? ' (' + res.causer.HAKAKSES + ')' : ''}`
        : 'System'
    }
  </td>
</tr>

            <tr>
                <th>Waktu</th>
                <td>${formatDate(res.created_at)}</td>
            </tr>
        </table>
    </div>
`;


                    if (oldData || newData) {
                        html += `<div><div class="fw-semibold mb-2">Detail Perubahan</div>
                         ${renderChanges(oldData||{}, newData||{})}</div>`;
                    } else {
                        html += `<div><div class="fw-semibold mb-2">Detail Informasi</div>
                         ${renderInfo(props)}</div>`;
                    }

                    box.innerHTML = html;
                })
                .catch(() => box.innerHTML = '<div class="text-danger">Gagal memuat detail.</div>');
        });
    </script>
@endpush
