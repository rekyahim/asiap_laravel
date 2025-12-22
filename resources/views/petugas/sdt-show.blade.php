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
            /* Mencegah konten keluar dari border radius */
        }

        /* PERBAIKAN RESPONSIF TABEL & MAP */
        .table-fixed-layout {
            table-layout: fixed;
            /* Kunci lebar kolom agar tidak meledak di mobile */
            width: 100%;
        }

        .table-fixed-layout th,
        .table-fixed-layout td {
            word-wrap: break-word;
            /* Paksa text panjang turun ke bawah */
            white-space: normal;
            vertical-align: top;
        }

        /* Container Map yang Responsif */
        .map-container {
            width: 100%;
            max-width: 100%;
            /* Pastikan tidak lebih dari container induk */
            height: 300px;
            /* Tinggi default desktop */
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
            /* Pastikan riwayat card juga menangani overflow */
            overflow-wrap: break-word;
        }

        /* Tweak khusus Mobile (HP) */
        @media (max-width: 768px) {
            .map-container {
                height: 200px;
                /* Peta lebih pendek di HP agar proporsional */
            }

            .table-fixed-layout th {
                width: 35%;
                /* Atur lebar label kolom di HP */
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

    {{-- ===== BAGIAN 1: DATA DASAR SDT (FIXED HEADER) ===== --}}
    <div class="card-clean p-3 mb-4">
        <h5 class="mb-3">Informasi Utama SDT</h5>

        {{-- Gunakan table-responsive untuk safety extra di layar sangat kecil --}}
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0 table-fixed-layout">
                <tbody>
                    {{-- Data Statis --}}
                    <tr>
                        <th>ID</th>
                        <td>#{{ $row->ID }}</td>
                        <th>NOP</th>
                        <td>{{ $row->NOP }}</td>
                    </tr>
                    <tr>
                        <th>Tahun</th>
                        <td>{{ $row->TAHUN }}</td>
                        <th>Nama WP</th>
                        <td>{{ $row->NAMA_WP }}</td>
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
                                @foreach ($photos as $src)
                                    <a href="{{ $src }}" target="_blank">
                                        <img src="{{ $src }}" alt="Evidence"
                                            style="height:100px; margin-right:5px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                                    </a>
                                @endforeach
                            @else
                                <span class="text-muted">Tidak ada foto.</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== BAGIAN 2: RIWAYAT PENGISIAN (History Loop) ===== --}}
    <div class="mb-4">
        <h5 class="mb-3">Riwayat Pengisian Petugas (Urut Terbaru ke Lama)</h5>

        @php
            // Definisi Closure untuk Render Item Riwayat
            $renderRiwayatBlock = function ($data, $is_latest) use ($row) {
                // Siapkan variabel agar tidak error undefined
                $petugasNama = $data->petugas->NAMA ?? '—';
                $statusBadge = $is_latest
                    ? '<span class="badge bg-primary">AKTIF (TERBARU)</span>'
                    : '<span class="badge bg-secondary">ARSIP LAMA</span>';
                $borderClass = $is_latest ? 'border-primary shadow-sm' : '';
                $cardTitle = $is_latest ? 'Input Terbaru' : 'Input Sebelumnya (Tahun: ' . ($data->TAHUN ?? '—') . ')';

                $statusPenyampaian = $data->STATUS_PENYAMPAIAN ?? ($row->STATUS_PENYAMPAIAN ?? '-');
                $statusOP = $data->STATUS_OP ?? '-';
                $statusWP = $data->STATUS_WP ?? '-';
                $namaPenerima = $data->NAMA_PENERIMA ?? '-';
                $hpPenerima = $data->HP_PENERIMA ?? '-';
                $keteranganPetugas = $data->KETERANGAN_PETUGAS ?: '—';
                $tglPenyampaian = $data->TGL_PENYAMPAIAN ? date('d M Y H:i', strtotime($data->TGL_PENYAMPAIAN)) : '—';
                $koordinatOP = $data->KOORDINAT_OP ?? null;
                $nopBenar = $data->NOP_BENAR ?? '—';

                // Render HTML
                echo "<div class='riwayat-card $borderClass'>";
                echo "<div class='d-flex justify-content-between align-items-center mb-2'>";
                echo "<h6 class='mb-0 fw-bold'>$cardTitle</h6>";
                echo $statusBadge;
                echo '</div>';

                // Gunakan table-responsive juga di sini
                echo "<div class='table-responsive'>";
                echo "<table class='table table-sm mb-2 table-fixed-layout'>";

                echo '<tr>';
                echo "<th style='width: 30%'>Petugas</th>";
                echo "<td colspan='3'><strong>$petugasNama</strong> <span class='text-muted small'>(NOP Benar: $nopBenar)</span></td>";
                echo '</tr>';

                echo '<tr>';
                echo '<th>Status Penyampaian</th>';
                echo "<td>$statusPenyampaian</td>";
                echo '<th>Status OP/WP</th>';
                echo "<td>$statusOP / $statusWP</td>";
                echo '</tr>';

                echo '<tr>';
                echo '<th>Penerima / HP</th>';
                echo "<td colspan='3'>$namaPenerima / $hpPenerima</td>";
                echo '</tr>';

                echo '<tr>';
                echo '<th>Keterangan</th>';
                echo "<td colspan='3'>$keteranganPetugas</td>";
                echo '</tr>';

                echo '<tr>';
                echo '<th>Tgl Input</th>';
                echo "<td colspan='3'>$tglPenyampaian</td>";
                echo '</tr>';
                echo '</table>';
                echo '</div>'; // end table-responsive

                // Render Map Riwayat
                if ($koordinatOP) {
                    [$lat, $lng] = explode(',', $koordinatOP);
                    $lat = trim($lat);
                    $lng = trim($lng);

                    if ($lat && $lng) {
                        echo "<div class='map-container'>";
                        echo "<iframe src='https://maps.google.com/maps?q={$lat},{$lng}&hl=id&z=17&output=embed' referrerpolicy='no-referrer-when-downgrade' allowfullscreen></iframe>";
                        echo '</div>';
                        echo "<p class='mt-1 small mb-0'><i class='fas fa-map-marker-alt'></i> Koordinat: <a href='https://www.google.com/maps?q={$lat},{$lng}' target='_blank'>$koordinatOP</a></p>";
                    } else {
                        echo "<span class='text-danger small'>Format koordinat rusak.</span>";
                    }
                } else {
                    echo "<span class='text-muted small'>Tidak ada data koordinat.</span>";
                }

                echo '</div>'; // end riwayat-card
            };
        @endphp

        {{-- LOOPING RIWAYAT --}}

        {{-- 1. Riwayat Terbaru (Data Utama saat ini) --}}
        @if (isset($konfirmasi))
            {{ $renderRiwayatBlock($konfirmasi, true) }}
        @endif

        {{-- 2. Riwayat Sebelumnya --}}
        @if (isset($lastTwoPetugas) && count($lastTwoPetugas))
            @foreach ($lastTwoPetugas as $p)
                {{ $renderRiwayatBlock($p, false) }}
            @endforeach
        @endif

        {{-- 3. State Kosong --}}
        @if (!isset($konfirmasi) && (!isset($lastTwoPetugas) || count($lastTwoPetugas) == 0))
            <div class="alert alert-secondary text-center">
                Belum ada riwayat pengisian petugas untuk SDT ini.
            </div>
        @endif
    </div>

@endsection
