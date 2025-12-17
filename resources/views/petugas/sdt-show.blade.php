@extends('layouts.admin')

@section('title', 'Petugas / Detail Data')
@section('breadcrumb', 'Petugas / Detail Data')

@section('content')
    <style>
        .detail-grid .label {
            color: #64748b;
            min-width: 180px
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px
        }

        .photo-grid a {
            display: block;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden
        }

        .photo-grid img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block
        }

        .card-clean {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 10px 26px rgba(2, 6, 23, .08)
        }
        
    </style>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0">Detail SDT</h2>
        <div class="d-flex gap-2">
            <a href="{{ $return ?: url()->previous() }}" class="btn btn-light">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- ===== DATA UTAMA ===== --}}
    <div class="card-clean p-3 mb-3">
        <div class="row g-3 detail-grid">
            <div class="col-lg-6">
                <div class="d-flex mb-2">
                    <div class="label">ID</div>
                    <div class="ms-3 fw-semibold">#{{ $row->ID }}</div>
                </div>
                <div class="d-flex mb-2">
                    <div class="label">NOP</div>
                    <div class="ms-3">{{ $row->NOP }}</div>
                </div>
                <div class="d-flex mb-2">
                    <div class="label">Tahun</div>
                    <div class="ms-3">{{ $row->TAHUN }}</div>
                </div>
                <div class="d-flex mb-2">
                    <div class="label">Nama WP</div>
                    <div class="ms-3">{{ $row->NAMA_WP }}</div>
                </div>
                <div class="d-flex mb-2">
                    <div class="label">Alamat OP</div>
                    <div class="ms-3">{{ $row->ALAMAT_OP }}</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex mb-2">
                    <div class="label">Status Penyampaian</div>
                    <div class="ms-3">{{ $row->STATUS_PENYAMPAIAN ?? '-' }}</div>
                </div>
                <div class="d-flex mb-2">
                    <div class="label">Status OP</div>
                    <div class="ms-3">{{ $row->STATUS_OP ?? '-' }}</div>
                </div>
                <div class="d-flex mb-2">
                    <div class="label">Status WP</div>
                    <div class="ms-3">{{ $row->STATUS_WP ?? '-' }}</div>
                </div>
                <div class="d-flex mb-2">
                    <div class="label">Nama Penerima</div>
                    <div class="ms-3">{{ $row->NAMA_PENERIMA ?? '-' }}</div>
                </div>
                <div class="d-flex mb-2">
                    <div class="label">HP Penerima</div>
                    <div class="ms-3">{{ $row->HP_PENERIMA ?? '-' }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex mb-1">
                    <div class="label">Keterangan Petugas</div>
                    <div class="ms-3">{{ $row->KETERANGAN_PETUGAS ?: '—' }}</div>
                </div>
                <div class="small text-muted ms-lg-0 mt-1">
                    Terakhir diubah: {{ optional($row->UPDATED_AT)->format('d-m-Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FOTO EVIDENCE ===== --}}
    <div class="card-clean p-3 mb-4">
        <h5 class="mb-3">Foto Evidence</h5>
        @if (count($photos))
            <div class="photo-grid">
                @foreach ($photos as $src)
                    <a href="{{ $src }}" target="_blank" rel="noopener">
                        <img src="{{ $src }}" alt="Evidence">
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-muted">Tidak ada foto.</div>
        @endif
    </div>

    {{-- ===== DATA KONFIRMASI PETUGAS + PETA ===== --}}
    @if (isset($konfirmasi))
        <div class="card-clean p-3">
            <h5 class="mb-3">Data Konfirmasi Petugas</h5>
            <div class="row gy-2 gx-4">
                <div class="col-md-3">
                    <small class="text-muted d-block">Petugas</small>
                    <span class="fw-semibold text-dark">{{ $konfirmasi->petugas->NAMA ?? '—' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Status OP</small>
                    <span class="fw-semibold">{{ $konfirmasi->STATUS_OP ?? '—' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Status WP</small>
                    <span class="fw-semibold">{{ $konfirmasi->STATUS_WP ?? '—' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">NOP Benar</small>
                    <span class="fw-semibold">{{ $konfirmasi->NOP_BENAR ?? '—' }}</span>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block">Koordinat Lokasi</small>
                    @if (!empty($konfirmasi->KOORDINAT_OP))
                        <a href="https://www.google.com/maps?q={{ $konfirmasi->KOORDINAT_OP }}" target="_blank"
                            class="fw-semibold text-primary">
                            {{ $konfirmasi->KOORDINAT_OP }}
                        </a>

                        {{-- 🗺️ Embed Google Maps --}}
                        @php
                            [$lat, $lng] = explode(',', $konfirmasi->KOORDINAT_OP);
                        @endphp
                        <div class="mt-2" style="border-radius: 10px; overflow: hidden;">
                            <iframe width="100%" height="300" frameborder="0" style="border:0"
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ trim($lat) }},{{ trim($lng) }}&hl=id&z=17&output=embed"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">Tanggal Penyampaian</small>
                    <span class="fw-semibold">
                        {{ $konfirmasi->TGL_PENYAMPAIAN ? date('d M Y H:i', strtotime($konfirmasi->TGL_PENYAMPAIAN)) : '—' }}
                    </span>
                </div>

                <div class="col-md-3">
                    <small class="text-muted d-block">Keterangan Petugas</small>
                    <span class="fw-semibold">{{ $konfirmasi->KETERANGAN_PETUGAS ?? '—' }}</span>
                </div>
            </div>
        </div>
    @endif
@endsection
