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

        /* Glass Buttons */
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

        .btn-glass-blue:hover {
            background: rgba(79, 147, 255, 0.35);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(31, 96, 255, 0.5);
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

        .btn-glass-green:hover {
            background: rgba(0, 208, 132, 0.35);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 208, 132, 0.5);
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

        .btn-retake:hover {
            background: rgba(240, 173, 78, 0.35);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(240, 173, 78, 0.5);
        }

        .thumb {
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .thumb:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
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
            .btn-retake {
                width: 100%;
                display: block;
                margin-bottom: 8px;
            }
        }

        /* ===== CAMERA MODAL STYLES ===== */
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
            font-size: 16px;
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
                <div><strong>Alamat OP:</strong> {{ $row->ALAMAT_OP }}</div>
                <div><strong>Nama WP:</strong> {{ $row->NAMA_WP }}</div>
            </div>
        </div>

        @if (session('info'))
            <div class="alert alert-info m-3" style="border-radius:10px;">{{ session('info') }}</div>
        @endif

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
                            <option value="TERSAMPAIKAN"
                                {{ strtoupper($status->STATUS_PENYAMPAIAN ?? '') == 'TERSAMPAIKAN' || ($status->STATUS_PENYAMPAIAN ?? '') == '1' ? 'selected' : '' }}>
                                Tersampaikan</option>
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
                                TIDAK
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Koordinat</label>
                        <div class="input-group">
                            <span class="input-group-text">📍</span>
                            <input type="text" id="coordDisplay" class="form-control" placeholder="Mencari GPS..."
                                disabled value="{{ $status->KOORDINAT_OP ?? '' }}">
                        </div>
                        <small class="text-muted" style="font-size:11px;">Otomatis terisi saat GPS aktif</small>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Penerima</label>
                        <input type="text" name="NAMA_PENERIMA" class="form-control"
                            placeholder="Masukkan nama penerima..."
                            value="{{ old('NAMA_PENERIMA', $status->NAMA_PENERIMA ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor HP Penerima</label>
                        <input type="text" name="HP_PENERIMA" class="form-control" placeholder="08xxxxxxxxxx"
                            value="{{ old('HP_PENERIMA', $status->HP_PENERIMA ?? '') }}">
                    </div>
                </div>

                <div class="row g-3 mt-3">
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
                            data-bs-target="#modalCamera" {{ $expired ? 'disabled' : '' }}>
                            📷 Kamera
                        </button>

                        @if (!$expired)
                            <button type="submit" class="btn-glass-green" id="btnSubmit">💾 Simpan</button>
                        @else
                            <span class="text-muted ms-2">Update tidak tersedia (lebih dari 6 jam)</span>
                        @endif
                    </div>

                    <button type="button" id="btnRetake" class="btn-glass-blue btn-retake mt-3" style="display:none;">
                        🔄 Ulangi Foto
                    </button>
                    <img id="thumbPrev" class="thumb mt-3" style="display:none; max-width:180px;">

                    <div id="expiredMsg" class="mt-3 text-danger"
                        style="font-weight:600; display: {{ $expired ? 'block' : 'none' }};">
                        ⚠️ Update sudah tidak tersedia (lebih dari 6 jam)
                    </div>
                </div>
            </div>
        </form>

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
            // --- Konfigurasi Awal ---
            const expired = {{ $expired ? 'true' : 'false' }};
            const petugas = "{{ auth()->user()->name }}";
            const nomorSDT = "{{ $row->ID_SDT }}";

            // --- Referensi Elemen ---
            const form = document.getElementById('frmEdit');
            const editContainer = document.getElementById('editContainer');
            const btnOpenCam = document.getElementById('btnOpenCam');
            const expiredMsg = document.getElementById('expiredMsg');
            const btnSubmit = document.getElementById('btnSubmit');

            const statusPenyampaian = document.getElementById('STATUS');
            const foto64 = document.getElementById('FOTO_BASE64');
            const koordInput = document.getElementById('KOORDINAT_OP');
            const coordDisplay = document.getElementById('coordDisplay');

            // --- Elemen Kamera ---
            const video = document.getElementById('camVideo');
            const canvas = document.getElementById('camCanvas');
            const prev = document.getElementById('thumbPrev');
            const btnShot = document.getElementById('btnShot');
            const btnFlip = document.getElementById('btnFlip');
            const btnRetakeModal = document.getElementById('btnRetakeModal');
            const btnCloseCam = document.getElementById('btnCloseCam');
            const btnRetakeMain = document.getElementById('btnRetake');

            let stream = null;
            let currentFacingMode = 'environment'; // Default ke kamera belakang

            // ================= 1. LOGIKA EXPIRED =================
            if (expired) {
                const inputs = editContainer.querySelectorAll('input, select, textarea, button');
                inputs.forEach(el => el.disabled = true);
                if (btnSubmit) btnSubmit.style.display = 'none';

                btnOpenCam.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    alert("Update tidak tersedia (Waktu habis).");
                });
            }

            // ================= 2. LOGIKA GPS PINTAR (SMART LOCATION) =================
            // Strategi: Coba High Accuracy (GPS) -> Gagal? -> Coba Low Accuracy (Network)

            function requestLocation() {
                if (!navigator.geolocation) {
                    coordDisplay.value = "GPS Tidak Didukung";
                    return;
                }

                // Langkah 1: Coba High Accuracy (Timeout 15 detik)
                navigator.geolocation.getCurrentPosition(
                    successLocation,
                    function(err) {
                        console.warn("High Accuracy Failed/Timeout. Fallback to Low Accuracy...");
                        // Langkah 2: Fallback ke Low Accuracy (Lebih cepat, via Wifi/Seluler)
                        navigator.geolocation.getCurrentPosition(
                            successLocation,
                            function(err2) {
                                console.error("GPS Total Failure:", err2);
                                coordDisplay.value = "";
                                coordDisplay.placeholder = "Gagal mengambil lokasi. Pastikan GPS Aktif.";
                                coordDisplay.disabled = false; // Izinkan input manual jika perlu
                            }, {
                                enableHighAccuracy: false,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    }, {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            }

            function successLocation(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const acc = pos.coords.accuracy;

                const val = `${lat},${lng}`;
                koordInput.value = val;
                coordDisplay.value = val;
                console.log(`Lokasi didapat. Akurasi: ${acc} meter`);
            }

            // Jalankan pencarian lokasi saat halaman dimuat
            if (!expired && !koordInput.value) {
                requestLocation();
            }

            // ================= 3. LOGIKA KAMERA HYBRID =================
            // Strategi: Coba kamera belakang -> Error (di PC)? -> Coba webcam depan

            async function startCamera() {
                if (expired) return;

                // Stop stream lama jika ada
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                const constraints = {
                    audio: false,
                    video: true,
                };

                try {
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    video.srcObject = stream;
                } catch (err) {
                    console.warn("Gagal akses kamera:", err.name);

                    // Fallback khusus PC: Jika 'environment' gagal, coba 'user' (Webcam)
                    if (currentFacingMode === 'environment') {
                        console.log("Mencoba fallback ke Webcam (User)...");
                        currentFacingMode = 'user';
                        startCamera(); // Restart fungsi
                    } else {
                        alert("Tidak dapat mengakses kamera. Pastikan izin diberikan.");
                    }
                }
            }

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                video.srcObject = null;
            }

            function takePicture() {
                if (!stream) return;

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext("2d");

                // Flip horizontal jika pakai kamera depan (mirroring)
                if (currentFacingMode === 'user') {
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                }

                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Kembalikan konteks agar teks tidak terbalik
                if (currentFacingMode === 'user') {
                    ctx.setTransform(1, 0, 0, 1, 0, 0);
                }

                // Watermark Data
                addWatermark(ctx);

                // Simpan ke Hidden Input & Preview
                const dataURL = canvas.toDataURL("image/jpeg", 0.85);
                foto64.value = dataURL;
                prev.src = dataURL;
                prev.style.display = "block";
                btnRetakeMain.style.display = "block";

                // Tutup Modal
                stopCamera();
                const modalEl = document.getElementById('modalCamera');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            }

            function addWatermark(ctx) {
                // Ambil koordinat saat ini (atau dari input jika GPS belum lock)
                const loc = koordInput.value || "Mencari Lokasi...";
                const time = new Date().toLocaleString("id-ID");

                ctx.font = "bold 24px Arial";
                ctx.fillStyle = "white";
                ctx.shadowColor = "black";
                ctx.shadowBlur = 6;
                ctx.lineWidth = 3;

                const lines = [
                    `Petugas: ${petugas}`,
                    `SDT: ${nomorSDT}`,
                    `Lokasi: ${loc}`,
                    `Waktu: ${time}`
                ];

                // Posisi teks di pojok kiri atas
                let y = 40;
                lines.forEach(line => {
                    ctx.strokeText(line, 20, y);
                    ctx.fillText(line, 20, y);
                    y += 35;
                });
            }

            // Event Listeners Kamera
            const modalEl = document.getElementById('modalCamera');
            modalEl.addEventListener('shown.bs.modal', startCamera);
            modalEl.addEventListener('hidden.bs.modal', stopCamera);

            btnShot.addEventListener('click', takePicture);

            btnFlip.addEventListener('click', () => {
                currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
                startCamera();
            });

            btnCloseCam.addEventListener('click', () => {
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            });

            // Fitur Retake (Ulangi Foto)
            btnRetakeMain.addEventListener('click', () => {
                prev.style.display = 'none';
                btnRetakeMain.style.display = 'none';
                foto64.value = '';
                // Buka modal lagi
                new bootstrap.Modal(modalEl).show();
            });

            // Validasi Submit Form
            form.addEventListener('submit', (e) => {
                if (statusPenyampaian.value === 'TERSAMPAIKAN' && !foto64.value) {
                    e.preventDefault();
                    alert("WAJIB FOTO bukti jika status 'Tersampaikan'!");
                    btnOpenCam.scrollIntoView({
                        behavior: 'smooth'
                    });
                    btnOpenCam.classList.add('btn-danger'); // Highlight tombol
                    setTimeout(() => btnOpenCam.classList.remove('btn-danger'), 2000);
                }
            });

        });
    </script>
@endsection
