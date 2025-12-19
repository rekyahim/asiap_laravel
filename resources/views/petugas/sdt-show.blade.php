@extends('layouts.admin')

@section('title', 'Petugas / Detail Data')
@section('breadcrumb', 'Petugas / Detail Data')

@section('content')
<style>
    /* ... (Style tetap sama) ... */
    .card-clean{background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 10px 26px rgba(2,6,23,.08)}

    .map-container {
        width: 100%;
        max-width: 350px;
        height: 180px;
        border-radius: 5px;
        overflow: hidden;
        margin-top: 5px;
        border: 1px solid #ccc;
    }
    .map-container iframe {
        display: block;
    }
    .riwayat-card {
        padding: 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 20px; /* Jarak antar kolom riwayat/card */
        background-color: #f9fafb;
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

{{-- ===== DATA DASAR SDT (FIXED HEADER) ===== --}}
<div class="card-clean p-3 mb-4">
    <h5 class="mb-3">Informasi Utama SDT</h5>
    <table class="table table-bordered table-sm mb-0">
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
            {{-- Foto Evidence diambil dari yang terbaru --}}
            <tr>
                <th>Foto Evidence</th>
                <td colspan="3">
                    @if(count($photos))
                        @foreach($photos as $src)
                            <a href="{{ $src }}" target="_blank">
                                <img src="{{ $src }}" alt="Evidence" style="height:50px; margin-right:5px; object-fit:cover; border-radius:4px;">
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

{{-- ===== RIWAYAT PENGISIAN DATA PETUGAS (Kolom Berurutan) ===== --}}
<div class="mb-4">
    <h5 class="mb-3">Riwayat Pengisian Petugas (Urut Terbaru ke Lama)</h5>

    {{-- FUNGSI UNTUK MERENDER SATU BLOK RIWAYAT --}}
    @php
    $renderRiwayatBlock = function($data, $is_latest) use ($row) {
        // Fallback untuk NAMA_PETUGAS jika tidak ada di objek $data
        $petugasNama = $data->petugas->NAMA ?? '—';
        $statusBadge = $is_latest ? '<span class="badge bg-primary">AKTIF</span>' : '';
        $borderClass = $is_latest ? 'border-primary' : '';
        $cardTitle = $is_latest ? 'Input Terbaru' : 'Input Sebelumnya (Tahun: ' . ($data->TAHUN ?? '—') . ')';

        // Tentukan data yang digunakan
        $statusPenyampaian = $data->STATUS_PENYAMPAIAN ?? $row->STATUS_PENYAMPAIAN ?? '-';
        $statusOP = $data->STATUS_OP ?? '-';
        $statusWP = $data->STATUS_WP ?? '-';
        $namaPenerima = $data->NAMA_PENERIMA ?? '-';
        $hpPenerima = $data->HP_PENERIMA ?? '-';
        $keteranganPetugas = $data->KETERANGAN_PETUGAS ?: '—';
        $tglPenyampaian = $data->TGL_PENYAMPAIAN ? date('d M Y H:i', strtotime($data->TGL_PENYAMPAIAN)) : '—';
        $koordinatOP = $data->KOORDINAT_OP ?? null;
        $nopBenar = $data->NOP_BENAR ?? '—';

        echo "<div class='riwayat-card $borderClass'>";
            echo "<div class='d-flex justify-content-between align-items-start'>";
                echo "<h6 class='mb-2'>**$cardTitle**</h6>";
                echo $statusBadge;
            echo "</div>";

            echo "<table class='table table-sm mb-2'>";
                echo "<tr>";
                    echo "<th style='width: 25%'>Petugas (Yang Input)</th>";
                    echo "<td colspan='3'>";
                        echo "<strong>$petugasNama</strong> <span class='text-muted'>(NOP Benar: $nopBenar)</span>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<th>Status Penyampaian</th>";
                    echo "<td>$statusPenyampaian</td>";
                    echo "<th>Status OP/WP</th>";
                    echo "<td>$statusOP/$statusWP</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<th>Nama / HP Penerima</th>";
                    echo "<td colspan='3'>$namaPenerima / $hpPenerima</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<th>Keterangan Petugas</th>";
                    echo "<td colspan='3'>$keteranganPetugas</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<th>Terakhir Diubah / Tgl Input</th>";
                    echo "<td colspan='3'>$tglPenyampaian</td>";
                echo "</tr>";
            echo "</table>";

            // PETA LOKASI
            if ($koordinatOP) {
                [$lat, $lng] = explode(',', $koordinatOP);
                echo "<div class='map-container'>";
                    echo "<iframe width='100%' height='180' frameborder='0' style='border:0' referrerpolicy='no-referrer-when-downgrade' src='https://www.google.com/maps?q=" . trim($lat) . "," . trim($lng) . "&hl=id&z=17&output=embed' allowfullscreen></iframe>";
                echo "</div>";
                echo "<p class='mt-1 small mb-0'>Koordinat: <a href='https://www.google.com/maps?q=$koordinatOP' target='_blank'>$koordinatOP</a></p>";
            } else {
                echo "<span class='text-muted small'>Tidak ada data koordinat pada input ini.</span>";
            }
        echo "</div>";
    };


    @endphp

    {{-- A. RIWAYAT TERBARU ($konfirmasi) --}}
    @if(isset($konfirmasi))
        {{ $renderRiwayatBlock($konfirmasi, true) }}
    @endif

    {{-- B. RIWAYAT SEBELUMNYA ($lastTwoPetugas) --}}
    @if(isset($lastTwoPetugas) && count($lastTwoPetugas))
        @foreach($lastTwoPetugas as $p)
            {{ $renderRiwayatBlock($p, false) }}
        @endforeach
    @endif

    @if(!isset($konfirmasi) && (!isset($lastTwoPetugas) || count($lastTwoPetugas) == 0))
        <p class="text-muted">Belum ada riwayat pengisian petugas untuk SDT ini.</p>
    @endif
</div>

@endsection
