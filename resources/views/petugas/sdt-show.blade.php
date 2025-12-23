@extends('layouts.admin')

@section('title', 'Petugas / Detail Data')
@section('breadcrumb', 'Petugas / Detail Data')

@section('content')
    <style>
        .card-clean {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 10px 26px rgba(2, 6, 23, .08);
            overflow: hidden;
        }

        /* Container Map */
        .map-container {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            position: relative;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            display: block;
            border: 0;
        }

        .riwayat-card {
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: #f9fafb;
        }

        /* Helper untuk lebar kolom tabel agar proporsional (Desktop) */
        .col-label {
            width: 15%;
            white-space: nowrap;
            background-color: #f8f9fa;
        }

        .col-value {
            width: 35%;
        }

        @media (max-width: 768px) {
            .map-container {
                height: 200px;
            }

            /* Di HP, biarkan lebar otomatis agar tidak sempit */
            .col-label {
                width: auto;
                white-space: normal;
            }

            .col-value {
                width: auto;
            }
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

    {{-- ===== BAGIAN 1: DATA DASAR SDT ===== --}}
    <div class="card-clean p-3 mb-4">
        <h5 class="mb-3">Informasi Utama SDT</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <tbody>
                    <tr>
                        <th class="col-label">ID</th>
                        <td class="col-value">#{{ $row->ID }}</td>
                        <th class="col-label">NOP</th>
                        <td class="col-value">{{ $row->NOP }}</td>
                    </tr>
                    <tr>
                        <th class="col-label">Tahun</th>
                        <td class="col-value">{{ $row->TAHUN }}</td>
                        <th class="col-label">Nama WP</th>
                        <td class="col-value">{{ $row->NAMA_WP }}</td>
                    </tr>
                    <tr>
                        <th>Alamat OP</th>
                        <td colspan="3">{{ $row->ALAMAT_OP }}</td>
                    </tr>

                    {{-- Status Penyampaian --}}
                    <tr>
                        <th>STATUS PENYAMPAIAN</th>
                        <td colspan="3">
                            {{ isset($konfirmasi->STATUS_PENYAMPAIAN) ? ($konfirmasi->STATUS_PENYAMPAIAN == 1 ? 'Tersampaikan' : 'Tidak Tersampaikan') : 'Belum Diproses Petugas' }}
                        </td>
                    </tr>
                    <tr>
                        <th>NOP BENAR</th>
                        <td colspan="3">
                            {{ isset($konfirmasi->NOP_BENAR) ? $konfirmasi->NOP_BENAR : 'Belum Diproses Petugas' }}
                        </td>
                    </tr>
                    {{-- Status OP --}}
                    <tr>
                        <th>STATUS OP</th>
                        <td colspan="3">
                            {{ [
                                1 => 'Belum Diproses Petugas',
                                2 => 'Ditemukan',
                                3 => 'Tidak Ditemukan',
                                4 => 'Sudah Dijual',
                            ][$konfirmasi->STATUS_OP ?? 0] ?? '-' }}
                        </td>
                    </tr>

                    {{-- Status WP --}}
                    <tr>
                        <th>STATUS WP</th>
                        <td colspan="3">
                            {{ [
                                1 => 'Belum Diproses Petugas',
                                2 => 'Ditemukan',
                                3 => 'Tidak Ditemukan',
                                4 => 'Sudah Dijual',
                            ][$konfirmasi->STATUS_WP ?? 0] ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>KETERANGAN PETUGAS</th>
                        <td colspan="3">
                            {{ isset($konfirmasi->KETERANGAN_PETUGAS) ? $konfirmasi->KETERANGAN_PETUGAS : 'Belum Diproses Petugas' }}
                        </td>
                    </tr>
                    <tr>
                        <th>NAMA PENERIMA</th>
                        <td colspan="3">
                            {{ isset($konfirmasi->NAMA_PENERIMA) ? $konfirmasi->NAMA_PENERIMA : 'Belum Diproses Petugas' }}
                        </td>
                    </tr>
                    <tr>
                        <th>NO HP PENERIMA</th>
                        <td colspan="3">
                            {{ isset($konfirmasi->HP_PENERIMA) ? $konfirmasi->HP_PENERIMA : 'Belum Diproses Petugas' }}
                        </td>
                    </tr>
                    <tr>
                        <th>TGL PENYAMPAIAN</th>
                        <td colspan="3">
                            {{ isset($konfirmasi->created_at) ? date('d M Y H:i', strtotime($konfirmasi->created_at)) : 'Belum Diproses Petugas' }}
                        </td>
                    </tr>
                    {{-- Peta Utama --}}
                    <tr>
                        <th>KOORDINAT OP</th>
                        <td colspan="3">
                            @if (!empty($konfirmasi->KOORDINAT_OP))
                                @php
                                    $parts = explode(',', $konfirmasi->KOORDINAT_OP);
                                    $lat = trim($parts[0] ?? '');
                                    $lng = trim($parts[1] ?? '');
                                @endphp

                                @if ($lat && $lng)
                                    <div class="map-container">
                                        <iframe
                                            src="https://maps.google.com/maps?q={{ $lat }},{{ $lng }}&hl=id&z=17&output=embed"
                                            referrerpolicy="no-referrer-when-downgrade" allowfullscreen>
                                        </iframe>
                                    </div>
                                    <p class="mt-1 small mb-0">
                                        <i class="fas fa-map-marker-alt text-danger"></i> Koordinat:
                                        <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}"
                                            target="_blank" class="fw-bold text-primary text-decoration-underline">
                                            {{ $konfirmasi->KOORDINAT_OP }}
                                        </a>
                                    </p>
                                @else
                                    <span class="text-danger small">
                                        <i class="fas fa-exclamation-triangle"></i> Format koordinat invalid.
                                    </span>
                                @endif
                            @else
                                <span class="text-muted small">Tidak ada data koordinat.</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Foto Evidence --}}
                    <tr>
                        <th>Foto Evidence</th>
                        <td colspan="3">
                            @if (isset($photos) && count($photos))
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach ($photos as $index => $src)
                                        <div class="text-center p-2 border rounded bg-light">
                                            {{-- 1. Gambar Preview (Klik untuk Zoom/Tab Baru) --}}
                                            <a href="{{ $src }}" target="_blank" class="d-block mb-2">
                                                <img src="{{ $src }}" alt="Evidence"
                                                    style="height:120px; width: auto; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                                            </a>

                                            {{-- 2. Tombol Download --}}
                                            {{-- Atribut 'download' memaksa browser mengunduh file (jika didukung server) --}}
                                            <a href="{{ $src }}"
                                                download="evidence-{{ $row->ID }}-{{ $index + 1 }}"
                                                class="btn btn-sm btn-primary w-100" target="_blank">
                                                <i class="fas fa-download"></i> Unduh
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">Tidak ada foto.</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== BAGIAN 2: RIWAYAT PENGISIAN (Refactored Loop) ===== --}}
    <div class="mb-4">
        <h5 class="mb-3">Riwayat Pengisian Petugas (Urut Terbaru ke Lama)</h5>

        @php
            // Gabungkan data terbaru ($konfirmasi) dan history ($lastTwoPetugas) jadi satu array
            // Supaya kita bisa meloop dengan rapi menggunakan HTML standar (bukan echo)
            $historyData = [];

            // 1. Masukkan Data Terbaru (Jika ada)
            if (isset($konfirmasi)) {
                // Tambahkan properti custom 'is_latest' on the fly
                $konfirmasi->is_latest = true;
                $historyData[] = $konfirmasi;
            }

            // 2. Masukkan Data Lama (Jika ada)
            if (isset($lastTwoPetugas) && count($lastTwoPetugas)) {
                foreach ($lastTwoPetugas as $p) {
                    $p->is_latest = false;
                    $historyData[] = $p;
                }
            }
        @endphp

        {{-- Loop Blade Standar (Rapi tanpa Echo) --}}
        @forelse($historydt as $data)
            <div class="riwayat-card">
                {{-- <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 fw-bold">
                        {{ $data->is_latest ? 'Input Terbaru' : 'Input Sebelumnya (Tahun: ' . ($data->TAHUN ?? '—') . ')' }}
                    </h6>
                    @if ($data->is_latest)
                        <span class="badge bg-primary">AKTIF (TERBARU)</span>
                    @else
                        <span class="badge bg-secondary">ARSIP LAMA</span>
                    @endif
                </div> --}}

                <div class="table-responsive">
                    <table class="table table-sm mb-2">
                        <tr>
                            <th style="width: 30%">Petugas</th>
                            <td colspan="3">
                                <strong>{{ $data->NAMA ?? '—' }}</strong>
                                <span class="text-muted small"> (NOP Benar: {{ $data->NOP_BENAR ?? '—' }})</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status Penyampaian</th>
                            <td>{{ isset($data->STATUS_PENYAMPAIAN) ? ($data->STATUS_PENYAMPAIAN == 1 ? 'Tersampaikan' : 'Tidak Tersampaikan') : 'Belum Diproses Petugas' }}
                            </td>

                        </tr>
                        <tr>
                            <th>STATUS OP</th>
                            <td colspan="3">
                                {{ [
                                    1 => 'Belum Diproses Petugas',
                                    2 => 'Ditemukan',
                                    3 => 'Tidak Ditemukan',
                                    4 => 'Sudah Dijual',
                                ][$data->STATUS_OP ?? 0] ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>STATUS OP</th>
                            <td colspan="3">
                                {{ [
                                    1 => 'Belum Diproses Petugas',
                                    2 => 'Ditemukan',
                                    3 => 'Tidak Ditemukan',
                                    4 => 'Sudah Dijual',
                                ][$data->STATUS_WP ?? 0] ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Penerima / HP</th>
                            <td colspan="3">{{ $data->NAMA_PENERIMA ?? '-' }} / {{ $data->HP_PENERIMA ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td colspan="3">{{ $data->KETERANGAN_PETUGAS ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Tgl Input</th>
                            <td colspan="3">
                                {{ $data->TGL_PENYAMPAIAN ? date('d M Y H:i', strtotime($data->TGL_PENYAMPAIAN)) : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Foto Evidence</th>
                            <td colspan="3">
                                @if (!empty($data->EVIDENCE))
                                    {{-- Container agar tampilan konsisten --}}
                                    <div class="d-flex">
                                        <div class="text-center p-2 border rounded bg-light" style="width: fit-content;">
                                            {{-- 1. Preview Image --}}
                                            {{-- GANTI url() MENJADI asset() --}}
                                            <a href="{{ asset('storage/' . $data->EVIDENCE) }}" target="_blank"
                                                class="d-block mb-2">
                                                <img src="{{ asset('storage/' . $data->EVIDENCE) }}" alt="Evidence Riwayat"
                                                    style="height:120px; width: auto; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                                            </a>

                                            {{-- 2. Tombol Download --}}
                                            {{-- GANTI url() MENJADI asset() --}}
                                            <a href="{{ asset('storage/' . $data->EVIDENCE) }}"
                                                download="evidence-riwayat-{{ $data->ID ?? rand() }}"
                                                class="btn btn-sm btn-primary w-100" target="_blank">
                                                <i class="fas fa-download"></i> Unduh
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">Tidak ada foto.</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Map Riwayat --}}
                @if (!empty($data->KOORDINAT_OP))
                    @php
                        $hParts = explode(',', $data->KOORDINAT_OP);
                        $hLat = trim($hParts[0] ?? '');
                        $hLng = trim($hParts[1] ?? '');
                    @endphp

                    @if ($hLat && $hLng)
                        <div class="map-container">
                            <iframe
                                src="https://maps.google.com/maps?q={{ $hLat }},{{ $hLng }}&hl=id&z=17&output=embed"
                                referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                        </div>
                        <p class="mt-1 small mb-0">
                            <i class="fas fa-map-marker-alt"></i> Koordinat:
                            <a href="https://www.google.com/maps?q={{ $hLat }},{{ $hLng }}"
                                target="_blank">
                                {{ $data->KOORDINAT_OP }}
                            </a>
                        </p>
                    @else
                        <span class="text-danger small">Format koordinat rusak.</span>
                    @endif
                @else
                    <span class="text-muted small">Tidak ada data koordinat.</span>
                @endif
            </div>
            <hr>
        @empty
            <div class="alert alert-secondary text-center">
                Belum ada riwayat pengisian petugas untuk SDT dengan NOP ini.
            </div>
        @endforelse
    </div>

@endsection
