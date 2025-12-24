@extends('layouts.admin')

@section('title', 'Petugas / Edit SDT')
@section('breadcrumb', 'Petugas / Edit SDT')

@php
    $paramBack = request('back');
    $decodedBack = $paramBack ? urldecode($paramBack) : null;
    $goBack =
        $decodedBack && str_starts_with($decodedBack, url('/'))
            ? $decodedBack
            : route('petugas.sdt.detail', $row->ID_SDT);

    // Helper untuk cek status awal
    $currentStatus = strtoupper($status->STATUS_PENYAMPAIAN ?? '');
    $isTersampaikan = $currentStatus == 'TERSAMPAIKAN' || ($status->STATUS_PENYAMPAIAN ?? '') == '1';
@endphp

@section('content')

    <style>
        /* ======= STYLING CARD & BUTTON ======= */
        .edit-card {
            background: linear-gradient(145deg, #003b8e, #005ed9);
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.22);
            color: #fff;
        }

        .edit-section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .white-card {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 20px 25px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            color: #222;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .btn-glass-blue {
            background: rgba(79, 147, 255, 0.2);
            backdrop-filter: blur(10px);
            color: #1c66ff;
            font-weight: 600;
            border: 1px solid rgba(79, 147, 255, 0.5);
            border-radius: 12px;
            padding: 0.45rem 1rem;
            box-shadow: 0 4px 20px rgba(31, 96, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-glass-green {
            background: rgba(0, 208, 132, 0.2);
            backdrop-filter: blur(10px);
            color: #009e5f;
            font-weight: 600;
            border: 1px solid rgba(0, 208, 132, 0.5);
            border-radius: 12px;
            padding: 0.45rem 1.2rem;
            box-shadow: 0 4px 20px rgba(0, 208, 132, 0.3);
            transition: all 0.3s ease;
        }

        .btn-retake {
            background: rgba(240, 173, 78, 0.2);
            backdrop-filter: blur(10px);
            color: #e6951c;
            font-weight: 600;
            border: 1px solid rgba(240, 173, 78, 0.5);
            border-radius: 12px;
            padding: 0.45rem 1rem;
            box-shadow: 0 4px 20px rgba(240, 173, 78, 0.3);
            transition: all 0.3s ease;
        }

        .thumb {
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        /* Container Penerima Transition */
        #containerPenerima {
            transition: all 0.4s ease-in-out;
        }

        @media(max-width:576px) {
            .edit-card {
                padding: 16px;
                border-radius: 12px;
            }

            .white-card {
                padding: 14px;
                border-radius: 10px;
            }

            .btn-glass-blue,
            .btn-glass-green {
                width: 100%;
                display: block;
                margin-bottom: 8px;
            }
        }

        /* CAMERA STYLES (Sama seperti sebelumnya) */
        .cam-container {
            background: #000;
            overflow: hidden;
            position: relative;
        }

        .cam-topbar {
            position: absolute;
            width: 100%;
            top: 0;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.05);
            z-index: 10;
        }

        .cam-title {
            color: #fff;
            font-size: 18px;
            font-weight: 600;
        }

        .cam-btn-close {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            border-radius: 50%;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cam-view {
            width: 100%;
            height: 100vh;
            position: relative;
            background: #000;
        }

        #camVideo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #camCanvas {
            display: none;
        }

        .cam-bottom {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px 30px 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            backdrop-filter: blur(12px);
            background: rgba(0, 0, 0, 0.25);
        }

        .cam-btn-round-small {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.35);
            color: #fff;
            font-size: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .cam-shutter {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: white;
            border: 5px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
            cursor: pointer;
        }
    </style>

    <div class="section">
        <div class="edit-card mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="edit-section-title mb-0 text-white">Edit Detail SDT</h5>
                    <div style="opacity:.95;font-size:13px;margin-top:6px;">
                        <strong>Master SDT :</strong>
                        <span style="font-weight:700;color:#ffd;">{{ $row->NAMA_SDT }}</span>
                    </div>
                </div>
                <div>
                    <a href="{{ $goBack }}" class="btn btn-light btn-sm" style="border-radius:10px;font-weight:600;">⟵
                        Kembali</a>
                </div>
            </div>
            <div class="mt-3" style="font-size:13px;opacity:.92;">
                <div><strong>NOP:</strong> {{ $row->NOP }}</div>
                <div><strong>Tahun:</strong> {{ $row->TAHUN }}</div>
                <div><strong>Alamat OP:</strong> {{ $row->ALAMAT_OP.' , '.$row->BLOK_KAV_NO_OP.' RT '.$row->RT_OP.' RW '.$row->RW_OP.' , '.$row->KEL_OP.' , '.$row->KEC_OP }}</div>
                <div><strong>Nama WP:</strong> {{ $row->NAMA_WP.' , '.$row->ALAMAT_WP.' , '.$row->BLOK_KAV_NO_.' RT '.$row->RT_WP.' RW '.$row->RW_WP.' , '.$row->KEL_WP.' , '.$row->KOTA_WP}}</div>
            </div>
        </div>

        <form id="frmEdit" method="POST"
            action="{{ route('petugas.sdt.update', $row->ID) }}?back={{ urlencode($paramBack ?? $goBack) }}">
            @csrf
            <input type="hidden" name="FOTO_BASE64" id="FOTO_BASE64">
            <input type="hidden" name="KOORDINAT_OP" id="KOORDINAT_OP">
            <input type="hidden" name="back" value="{{ $paramBack ?? urlencode($goBack) }}">

            <div class="white-card glass-card mb-3" id="editContainer">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Status Penyampaian</label>
                        <select name="STATUS_PENYAMPAIAN" id="STATUS" class="form-select" required>
                            <option value="">— Pilih —</option>
                            <option value="TERSAMPAIKAN" {{ $isTersampaikan ? 'selected' : '' }}>Tersampaikan</option>
                            <option value="TIDAK TERSAMPAIKAN"
                                {{ strtoupper($status->STATUS_PENYAMPAIAN ?? '') == 'TIDAK TERSAMPAIKAN' || ($status->STATUS_PENYAMPAIAN ?? '') == '0' ? 'selected' : '' }}>
                                Tidak Tersampaikan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Apakah NOP benar?</label>
                        <select name="NOP_BENAR" id="NOP_BENAR" class="form-select" required>
                            <option value="">— Pilih —</option>
                            <option value="YA" {{ strtoupper($status->NOP_BENAR ?? '') == 'YA' ? 'selected' : '' }}>YA
                            </option>
                            <option value="TIDAK" {{ strtoupper($status->NOP_BENAR ?? '') == 'TIDAK' ? 'selected' : '' }}>
                                TIDAK</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Koordinat</label>
                        <div class="input-group">
                            <span class="input-group-text">📍</span>
                            <input type="text" id="coordDisplay" class="form-control" placeholder="Mencari GPS..."
                                disabled value="{{ $status->KOORDINAT_OP ?? '' }}">
                        </div>
                    </div>
                </div>

                {{-- WRAPPER PENERIMA --}}
                <div id="containerPenerima" style="{{ $isTersampaikan ? '' : 'display:none;' }}">
                    <hr class="my-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" name="NAMA_PENERIMA" id="NAMA_PENERIMA" class="form-control"
                                placeholder="Masukkan nama penerima..."
                                value="{{ old('NAMA_PENERIMA', $status->NAMA_PENERIMA ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor HP Penerima</label>
                            <input type="text" name="HP_PENERIMA" id="HP_PENERIMA" class="form-control"
                                placeholder="08xxxxxxxxxx" value="{{ old('HP_PENERIMA', $status->HP_PENERIMA ?? '') }}">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Status OP</label>
                        <select name="STATUS_OP" class="form-select" required>
                            <option value="">-- Pilih Status OP --</option>
                            <option value="Belum Diproses Petugas"
                                {{ ($status->STATUS_OP ?? '') == 'Belum Diproses Petugas' || ($status->STATUS_OP ?? '') == '1' ? 'selected' : '' }}>
                                Belum Diproses Petugas</option>
                            <option value="Ditemukan"
                                {{ ($status->STATUS_OP ?? '') == 'Ditemukan' || ($status->STATUS_OP ?? '') == '2' ? 'selected' : '' }}>
                                Ditemukan</option>
                            <option value="Tidak Ditemukan"
                                {{ ($status->STATUS_OP ?? '') == 'Tidak Ditemukan' || ($status->STATUS_OP ?? '') == '3' ? 'selected' : '' }}>
                                Tidak Ditemukan</option>
                            <option value="Sudah Dijual"
                                {{ ($status->STATUS_OP ?? '') == 'Sudah Dijual' || ($status->STATUS_OP ?? '') == '4' ? 'selected' : '' }}>
                                Sudah Dijual</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status WP</label>
                        <select name="STATUS_WP" class="form-select" required>
                            <option value="">-- Pilih Status WP -- </option>
                            <option value="Belum Diproses Petugas"
                                {{ ($status->STATUS_WP ?? '') == 'Belum Diproses Petugas' || ($status->STATUS_WP ?? '') == '1' ? 'selected' : '' }}>
                                Belum Diproses Petugas</option>
                            <option value="Ditemukan"
                                {{ ($status->STATUS_WP ?? '') == 'Ditemukan' || ($status->STATUS_WP ?? '') == '2' ? 'selected' : '' }}>
                                Ditemukan</option>
                            <option value="Tidak Ditemukan"
                                {{ ($status->STATUS_WP ?? '') == 'Tidak Ditemukan' || ($status->STATUS_WP ?? '') == '3' ? 'selected' : '' }}>
                                Tidak Ditemukan</option>
                            <option value="Luar Kota"
                                {{ ($status->STATUS_WP ?? '') == 'Luar Kota' || ($status->STATUS_WP ?? '') == '4' ? 'selected' : '' }}>
                                Luar Kota</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="KETERANGAN" class="form-control" rows="3" placeholder="Masukkan keterangan tambahan...">{{ old('KETERANGAN_PETUGAS', $status->KETERANGAN_PETUGAS ?? '') }}</textarea>
                </div>

                <div class="mt-4">
                    <div class="btn-wrapper-bottom">
                        <button type="button" id="btnOpenCam" class="btn-glass-blue" data-bs-toggle="modal"
                            data-bs-target="#modalCamera" {{ $expired ? 'disabled' : '' }}>📷 Kamera</button>
                        @if (!$expired)
                            <button type="submit" class="btn-glass-green" id="btnSubmit">💾 Simpan</button>
                        @else
                            <span class="text-muted ms-2">Update tidak tersedia (lebih dari 6 jam)</span>
                        @endif
                    </div>
                    <button type="button" id="btnRetake" class="btn-glass-blue btn-retake mt-3"
                        style="display:none;">🔄 Ulangi Foto</button>
                    <img id="thumbPrev" class="thumb mt-3" style="display:none; max-width:180px;">
                    <div id="expiredMsg" class="mt-3 text-danger"
                        style="font-weight:600; display: {{ $expired ? 'block' : 'none' }};">⚠️ Update sudah tidak
                        tersedia (lebih dari 6 jam)</div>
                </div>
            </div>
        </form>

        {{-- MODAL KAMERA --}}
        <div class="modal fade" id="modalCamera" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content cam-container">
                    <div class="cam-topbar">
                        <span class="cam-title">Kamera</span>
                        <button type="button" id="btnCloseCam" class="cam-btn-close">✕</button>
                    </div>
                    <div class="cam-view">
                        <video id="camVideo" autoplay playsinline></video>
                        <canvas id="camCanvas"></canvas>
                    </div>
                    <div class="cam-bottom">
                        <button id="btnFlip" class="cam-btn-round-small">🔄</button>
                        <button id="btnShot" class="cam-shutter"></button>
                        <button id="btnRetakeModal" class="cam-btn-round-small" style="display:none;">↺</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const expired = {{ $expired ? 'true' : 'false' }};
            const petugas = "{{ auth()->user()->name }}";
            const nomorSDT = "{{ $row->ID_SDT }}";

            // Referensi Elemen Form
            const form = document.getElementById('frmEdit');
            const statusPenyampaian = document.getElementById('STATUS');
            const containerPenerima = document.getElementById('containerPenerima');
            const inputNama = document.getElementById('NAMA_PENERIMA');
            const inputHp = document.getElementById('HP_PENERIMA');

            const foto64 = document.getElementById('FOTO_BASE64');
            const koordInput = document.getElementById('KOORDINAT_OP');
            const coordDisplay = document.getElementById('coordDisplay');

            // ================= 1. LOGIKA SHOW/HIDE PENERIMA =================
            function handlePenerimaToggle() {
                if (statusPenyampaian.value === 'TERSAMPAIKAN') {
                    $(containerPenerima).slideDown(); // Pakai jQuery untuk animasi halus
                    inputNama.setAttribute('required', 'required');
                    inputHp.setAttribute('required', 'required');
                } else {
                    $(containerPenerima).slideUp();
                    inputNama.removeAttribute('required');
                    inputHp.removeAttribute('required');
                    // Reset value agar tidak ada data sampah terkirim
                    inputNama.value = '';
                    inputHp.value = '';
                }
            }

            statusPenyampaian.addEventListener('change', handlePenerimaToggle);

            // ================= 2. LOGIKA GPS =================
            function requestLocation() {
                if (!navigator.geolocation) return;
                navigator.geolocation.getCurrentPosition(successLocation, function(err) {
                    navigator.geolocation.getCurrentPosition(successLocation, null, {
                        enableHighAccuracy: false,
                        timeout: 10000
                    });
                }, {
                    enableHighAccuracy: true,
                    timeout: 15000
                });
            }

            function successLocation(pos) {
                const val = `${pos.coords.latitude},${pos.coords.longitude}`;
                koordInput.value = val;
                coordDisplay.value = val;
            }

            if (!expired && !koordInput.value) requestLocation();

            // ================= 3. LOGIKA KAMERA =================
            const video = document.getElementById('camVideo');
            const canvas = document.getElementById('camCanvas');
            const prev = document.getElementById('thumbPrev');
            const btnShot = document.getElementById('btnShot');
            const btnFlip = document.getElementById('btnFlip');
            const btnRetakeMain = document.getElementById('btnRetake');
            let stream = null;
            let currentFacingMode = 'environment';

            async function startCamera() {
                if (expired) return;
                if (stream) stream.getTracks().forEach(t => t.stop());
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: currentFacingMode
                        }
                    });
                    video.srcObject = stream;
                } catch (err) {
                    if (currentFacingMode === 'environment') {
                        currentFacingMode = 'user';
                        startCamera();
                    }
                }
            }

            document.getElementById('modalCamera').addEventListener('shown.bs.modal', startCamera);
            document.getElementById('modalCamera').addEventListener('hidden.bs.modal', () => {
                if (stream) stream.getTracks().forEach(t => t.stop());
            });

            btnShot.addEventListener('click', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext("2d");
                if (currentFacingMode === 'user') {
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                }
                ctx.drawImage(video, 0, 0);

                // Add Watermark
                const time = new Date().toLocaleString("id-ID");
                ctx.font = "bold 22px Arial";
                ctx.fillStyle = "white";
                ctx.shadowColor = "black";
                ctx.shadowBlur = 4;


                const dataURL = canvas.toDataURL("image/jpeg", 0.8);
                foto64.value = dataURL;
                prev.src = dataURL;
                prev.style.display = "block";
                btnRetakeMain.style.display = "block";
                bootstrap.Modal.getInstance(document.getElementById('modalCamera')).hide();
            });

            btnRetakeMain.addEventListener('click', () => {
                new bootstrap.Modal(document.getElementById('modalCamera')).show();
            });

            // ================= 4. VALIDASI SUBMIT =================
            form.addEventListener('submit', (e) => {
                if (statusPenyampaian.value === 'TERSAMPAIKAN' && !foto64.value) {
                    e.preventDefault();
                    alert("WAJIB ambil foto bukti lokasi untuk status 'Tersampaikan'!");
                }
            });
        });
    </script>
@endsection
