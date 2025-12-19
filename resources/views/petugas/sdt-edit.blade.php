@extends('layouts.admin')

@section('title', 'Petugas / Edit SDT')
@section('breadcrumb', 'Petugas / Edit SDT')

@php
$paramBack   = request('back');
$decodedBack = $paramBack ? urldecode($paramBack) : null;
$goBack      = ($decodedBack && str_starts_with($decodedBack, url('/'))) ? $decodedBack : route('petugas.sdt.detail', $row->ID_SDT);
@endphp

@section('content')

<style>
/* ======= STYLING CARD & BUTTON ======= */
.edit-card { background: linear-gradient(145deg,#003b8e,#005ed9);border-radius:18px;padding:22px;box-shadow:0 8px 18px rgba(0,0,0,0.22);color:#fff; }
.edit-section-title{ font-size:20px;font-weight:700;margin-bottom:6px; }
.white-card{ background: rgba(255,255,255,0.25);backdrop-filter: blur(12px);-webkit-backdrop-filter: blur(12px);border-radius:20px;padding:20px 25px;border:1px solid rgba(255,255,255,0.4);box-shadow:0 8px 25px rgba(0,0,0,0.15);color:#222; }
.form-control,.form-select{ border-radius:10px;padding:10px 12px;font-size:14px; }
.btn-glass-blue{ background: rgba(79,147,255,0.2);backdrop-filter: blur(10px);color:#1c66ff;font-weight:600;border:1px solid rgba(79,147,255,0.5);border-radius:12px;padding:0.45rem 1rem;box-shadow:0 4px 20px rgba(31,96,255,0.3);transition: all 0.3s ease; }
.btn-glass-blue:hover{ background: rgba(79,147,255,0.35);transform:translateY(-2px);box-shadow:0 6px 25px rgba(31,96,255,0.5);}
.btn-glass-green{ background: rgba(0,208,132,0.2);backdrop-filter: blur(10px);color:#009e5f;font-weight:600;border:1px solid rgba(0,208,132,0.5);border-radius:12px;padding:0.45rem 1.2rem;box-shadow:0 4px 20px rgba(0,208,132,0.3);transition: all 0.3s ease; }
.btn-glass-green:hover{ background: rgba(0,208,132,0.35);transform:translateY(-2px);box-shadow:0 6px 25px rgba(0,208,132,0.5);}
.btn-retake{ background: rgba(240,173,78,0.2);backdrop-filter: blur(10px);color:#e6951c;font-weight:600;border:1px solid rgba(240,173,78,0.5);border-radius:12px;padding:0.45rem 1rem;box-shadow:0 4px 20px rgba(240,173,78,0.3);transition: all 0.3s ease;}
.btn-retake:hover{ background: rgba(240,173,78,0.35);transform:translateY(-2px);box-shadow:0 6px 25px rgba(240,173,78,0.5);}
.thumb{ border-radius:12px;border:1px solid rgba(255,255,255,0.3);box-shadow:0 4px 10px rgba(0,0,0,0.2);transition:all 0.3s ease; }
.thumb:hover{ transform:scale(1.05);box-shadow:0 6px 20px rgba(0,0,0,0.3); }
@media(max-width:576px){.edit-card{padding:16px;border-radius:12px;}.white-card{padding:14px;border-radius:10px;}.btn-glass-blue,.btn-retake{width:100%;display:block;margin-bottom:8px;}}
</style>

<div class="section">

    <div class="edit-card mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="edit-section-title mb-0">Edit Detail SDT</h5>
                <div style="opacity:.95;font-size:13px;margin-top:6px;">
                    <strong>Master SDT :</strong>
                    <span style="font-weight:700;color:#ffd;">{{ $row->NAMA_SDT }}</span>
                </div>
            </div>
            <div>
                <a href="{{ $goBack }}" class="btn btn-light btn-sm" style="border-radius:10px;font-weight:600;">⟵ Kembali</a>
            </div>
        </div>
        <div class="mt-3" style="font-size:13px;opacity:.92;">
            <div><strong>NOP:</strong> {{ $row->NOP }}</div>
            <div><strong>Tahun:</strong> {{ $row->TAHUN }}</div>
            <div><strong>Alamat OP:</strong> {{ $row->ALAMAT_OP }}</div>
            <div><strong>Nama WP:</strong> {{ $row->NAMA_WP }}</div>
        </div>
    </div>

@if(session('info'))
<div class="alert alert-info m-3" style="border-radius:10px;">{{ session('info') }}</div>
@endif

<form id="frmEdit" method="POST" action="{{ route('petugas.sdt.update', $row->ID) }}?back={{ urlencode($paramBack ?? $goBack) }}">
    @csrf
    <input type="hidden" name="FOTO_BASE64" id="FOTO_BASE64">
    <input type="hidden" name="KOORDINAT_OP" id="KOORDINAT_OP">
    <input type="hidden" name="back" value="{{ $paramBack ?? urlencode($goBack) }}">

    <div class="white-card glass-card mb-3" id="editContainer">
        <div class="row g-3">
            <div class="col-md-4">
                <?php
                if(isset(($status->STATUS_PENYAMPAIAN))){
                    if($status->STATUS_PENYAMPAIAN == '1'){
                        $status->STATUS_PENYAMPAIAN = 'TERSAMPAIKAN';
                    }else if(($status->STATUS_PENYAMPAIAN == '2')){
                        $status->STATUS_PENYAMPAIAN = 'TIDAK TERSAMPAIKAN';
                    }
                }
                    ?>
                <label class="form-label">Status Penyampaian
                    </label>
                <select name="STATUS_PENYAMPAIAN" id="STATUS" class="form-select" required>
                    <option value="">— Pilih —</option>
                    <option value="TERSAMPAIKAN" {{ strtoupper($status->STATUS_PENYAMPAIAN??'')=='TERSAMPAIKAN'?'selected':'' }}>Tersampaikan</option>
                    <option value="TIDAK TERSAMPAIKAN" {{ strtoupper($status->STATUS_PENYAMPAIAN??'')=='TIDAK TERSAMPAIKAN'?'selected':'' }}>Tidak Tersampaikan</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Apakah NOP benar?</label>
                <select name="NOP_BENAR" id="NOP_BENAR" class="form-select" required>
                    <option value="">— Pilih —</option>
                    <option value="YA" {{ strtoupper($status->NOP_BENAR??'')=='YA'?'selected':'' }}>YA</option>
                    <option value="TIDAK" {{ strtoupper($status->NOP_BENAR??'')=='TIDAK'?'selected':'' }}>TIDAK</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Koordinat</label>
                <input type="text" id="coordDisplay" class="form-control" placeholder="—" disabled value="{{ $status->KOORDINAT_OP ?? '' }}">
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Penerima</label>
                <input type="text" name="NAMA_PENERIMA" class="form-control" placeholder="Masukkan nama penerima..." value="{{ old('NAMA_PENERIMA', $status->NAMA_PENERIMA ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Nomor HP Penerima</label>
                <input type="text" name="HP_PENERIMA" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('HP_PENERIMA', $status->HP_PENERIMA ?? '') }}">
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label class="form-label">Status OP</label>
                <?php
                if(isset(($status->STATUS_OP))){
                    if($status->STATUS_OP == '1'){
                        $status->STATUS_OP = 'Belum Diproses Petugas';
                    }else if(($status->STATUS_OP == '2')){
                        $status->STATUS_OP = 'Ditemukan';
                    }else if(($status->STATUS_OP == '3')){
                        $status->STATUS_OP = 'Tidak Ditemukan';
                    }else if(($status->STATUS_OP == '4')){
                        $status->STATUS_OP = 'Sudah Dijual';
                    }
                }
                    ?>
                <select name="STATUS_OP" class="form-select" required>
                    <option value="">-- Pilih Status OP --</option>
                    <option value="Belum Diproses Petugas" {{ ($status->STATUS_OP ?? '')=='Belum Diproses Petugas'?'selected':'' }}>Belum Diproses Petugas</option>
                    <option value="Ditemukan" {{ ($status->STATUS_OP ?? '')=='Ditemukan'?'selected':'' }}>Ditemukan</option>
                    <option value="Tidak Ditemukan" {{ ($status->STATUS_OP ?? '')=='Tidak Ditemukan'?'selected':'' }}>Tidak Ditemukan</option>
                    <option value="Sudah Dijual" {{ ($status->STATUS_OP ?? '')=='Sudah Dijual'?'selected':'' }}>Sudah Dijual</option>

                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Status WP</label>
                  <?php
                if(isset(($status->STATUS_WP))){
                    if($status->STATUS_WP == '1'){
                        $status->STATUS_OP = 'Belum Diproses Petugas';
                    }else if(($status->STATUS_WP == '2')){
                        $status->STATUS_WP = 'Ditemukan';
                    }else if(($status->STATUS_WP == '3')){
                        $status->STATUS_WP = 'Tidak Ditemukan';
                    }else if(($status->STATUS_WP == '4')){
                        $status->STATUS_WP = 'Luar Kota';
                    }
                    }

                    ?>
                <select name="STATUS_WP" class="form-select" required>
                    <option value="">-- Pilih Status WP -- </option>
                    <option value="Belum Diproses Petugas" {{ ($status->STATUS_WP ?? '')=='Belum Diproses Petugas'?'selected':'' }}>Belum Diproses Petugas</option>
                    <option value="Ditemukan" {{ ($status->STATUS_WP ?? '')=='Ditemukan'?'selected':'' }}>Ditemukan</option>
                    <option value="Tidak Ditemukan" {{ ($status->STATUS_WP ?? '')=='Tidak Ditemukan'?'selected':'' }}>Tidak Ditemukan</option>
                    <option value="Luar Kota" {{ ($status->STATUS_WP ?? '')=='Luar Kota'?'selected':'' }}>Luar Kota</option>

                </select>
            </div>
        </div>

        <div class="col-12 mt-3">
            <label class="form-label">Keterangan</label>
            <textarea name="KETERANGAN_PETUGAS" class="form-control" rows="3" placeholder="Masukkan keterangan tambahan...">{{ old('KETERANGAN_PETUGAS', $status->KETERANGAN_PETUGAS ?? '') }}</textarea>
        </div>

        <div class="mt-4">
          <div class="btn-wrapper-bottom">
            <button type="button"
                    id="btnOpenCam"
                    class="btn-glass-blue"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCamera"
                    {{ $expired ? 'disabled' : '' }}>
                📷 Kamera
            </button>

            @if(!$expired)
                <button type="submit" class="btn-glass-green" id="btnSubmit">💾 Simpan</button>
            @else
                <span class="text-muted">Update tidak tersedia (lebih dari 6 jam)</span>
            @endif
        </div>


            <button type="button" id="btnRetake" class="btn-glass-blue btn-retake mt-3" style="display:none;">🔄 Ulangi Foto</button>
            <img id="thumbPrev" class="thumb mt-3" style="display:none; max-width:180px;">

            <div id="expiredMsg" class="mt-3 text-muted" style="font-weight:600; display: {{ $expired ? 'block' : 'none' }};">
            ⚠️ Update sudah tidak tersedia (lebih dari 6 jam)
        </div>
        </div>
    </div>
</form>

<!-- Modal kamera tetap sama seperti sebelumnya -->
<div class="modal fade" id="modalCamera" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content cam-container">
        <!-- TOP BAR -->
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
<!-- ====================== END MODAL KAMERA ====================== -->


<style>
/* ===== CONTAINER ===== */
.cam-container {
    background: #000;
    overflow: hidden;
    position: relative;
}

/* ===== TOP BAR ===== */
.cam-topbar {
    position: absolute;
    width: 100%;
    top: 0;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    backdrop-filter: blur(12px);
    background: rgba(255,255,255,0.05);
    z-index: 10;
}

.cam-title {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
}

.cam-btn-close {
    background: rgba(255,255,255,0.15);
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

/* ===== CAMERA VIEW ===== */
.cam-view {
    width: 100%;
    height: 100vh;
    position: relative;
}

#camVideo {
    width: 100%;
    height: 100vh;
    object-fit: cover;
}

#camCanvas {
    display: none;
}

/* ===== BOTTOM BAR ===== */
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
    background: rgba(0,0,0,0.25);
}

/* ===== BUTTON ROUND SMALL ===== */
.cam-btn-round-small {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: rgba(255,255,255,0.18);
    border: 2px solid rgba(255,255,255,0.35);
    color: #fff;
    font-size: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===== SHUTTER BUTTON (iPhone Style) ===== */
.cam-shutter {
    width: 75px;
    height: 75px;
    border-radius: 50%;
    background: white;
    border: 5px solid rgba(255,255,255,0.5);
    box-shadow: 0 0 20px rgba(255,255,255,0.4);
}
</style>



<script>
(() => {
    const $ = s => document.querySelector(s);

    const expired = {{ $expired ? 'true' : 'false' }};
    const form = $('#frmEdit');
    const editContainer = $('#editContainer');
    const btnOpenCam = $('#btnOpenCam');
    const expiredMsg = $('#expiredMsg');
    const btnSubmit = form.querySelector('button[type="submit"]');

    const statusPenyampaian = $('#STATUS');
    const foto64 = $('#FOTO_BASE64');

    const video = $('#camVideo'),
          canvas = $('#camCanvas'),
          prev = $('#thumbPrev');

    const koord = $('#KOORDINAT_OP'),
          coordDisplay = $('#coordDisplay');

    const btnShot = $('#btnShot'),
          btnFlip = $('#btnFlip'),
          btnRetakeModal = $('#btnRetakeModal'),
          btnCloseCam = $('#btnCloseCam'),
          btnRetake = $('#btnRetake');

    const petugas = "{{ auth()->user()->name }}",
          nomorSDT = "{{ $row->ID_SDT }}";

    // ================= EXPIRED HANDLING =================
    if(expired){
        // Disable semua input, select, textarea, button
        editContainer.querySelectorAll('input, select, textarea, button').forEach(el=>{
            el.disabled = true;
        });

        // Sembunyikan tombol submit
        if(btnSubmit) btnSubmit.style.display = 'none';

        // Alert jika kamera diklik
        btnOpenCam.addEventListener('click', function(e){
            e.preventDefault();
            alert("Update sudah tidak tersedia (lebih dari 6 jam).");
        });

        // Tampilkan pesan expired
        expiredMsg.style.display = 'block';
    }

    // ================= CAMERA FUNCTIONS =================
    let facing = "environment";

    function startCam() {
        if(expired) return; // cegah akses kamera jika expired
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: facing },
            audio: false
        }).then(stream => {
            video.srcObject = stream;
            video.play();
        }).catch(() => alert("Tidak bisa mengakses kamera!"));
    }

    function stopCam() {
        if(video.srcObject)
            video.srcObject.getTracks().forEach(t => t.stop());
        video.srcObject = null;
    }

    function shoot() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext("2d");

        if(facing === "user"){
            ctx.scale(-1, 1);
            ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
        } else {
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        }

        navigator.geolocation.getCurrentPosition(pos => {
            const gps = pos.coords;
            koord.value = `${gps.latitude},${gps.longitude}`;
            coordDisplay.value = koord.value;

            ctx.fillStyle = "white";
            ctx.font = "32px Arial";
            ctx.textBaseline = "top";
            ctx.shadowColor = "black";
            ctx.shadowBlur = 8;

            const timestamp = new Date().toLocaleString("id-ID");
            const lines = [
                `Petugas : ${petugas}`,
                `SDT : ${nomorSDT}`,
                `Koordinat : ${gps.latitude.toFixed(6)}, ${gps.longitude.toFixed(6)}`,
                `Waktu : ${timestamp}`
            ];
            lines.forEach((t, i) => ctx.fillText(t, 30, 30 + i*38));

            foto64.value = canvas.toDataURL("image/jpeg", 0.9);
            prev.src = foto64.value;
            prev.style.display = "block";
            btnRetake.style.display = "block";

            btnShot.style.display = "none";
            btnRetakeModal.style.display = "none";

            stopCam();
            bootstrap.Modal.getInstance($('#modalCamera')).hide();
        });
    }

    function retake() {
        prev.style.display = "none";
        btnRetake.style.display = "none";

        btnShot.style.display = "flex";
        btnRetakeModal.style.display = "none";

        koord.value = "";
        coordDisplay.value = "";

        let modal = new bootstrap.Modal(document.getElementById("modalCamera"));
        modal.show();
    }

    // ================= EVENT LISTENERS =================
    btnShot.addEventListener("click", shoot);
    btnRetakeModal.addEventListener("click", retake);
    btnFlip.addEventListener("click", () => {
        facing = (facing === "environment") ? "user" : "environment";
        startCam();
    });
    btnCloseCam.addEventListener("click", () => {
        stopCam();
        bootstrap.Modal.getInstance(document.getElementById('modalCamera')).hide();
    });
    btnRetake.addEventListener("click", retake);

    $('#modalCamera').addEventListener("shown.bs.modal", startCam);
    $('#modalCamera').addEventListener("hidden.bs.modal", stopCam);

    // ================= VALIDASI SUBMIT =================
    form.addEventListener('submit', function(e){
        const status = statusPenyampaian.value.toUpperCase();
        if(status === 'TERSAMPAIKAN' && !foto64.value){
            e.preventDefault();
            alert("Status Tersampaikan wajib mengambil foto!");
            $('#modalCamera').scrollIntoView({behavior: 'smooth'});
        }
    });



})();
</script>

<script>
    window.onload = function() {
    checkPermissionAndGetLocation();
};

function checkPermissionAndGetLocation() {
    if (!navigator.permissions) {
        // Fallback for older browsers that don't support Permissions API
        requestBrowserLocation();
        return;
    }

    navigator.permissions.query({ name: 'geolocation' }).then((result) => {
        if (result.state === 'denied') {
            alert("Location access is required. Please enable it in your browser settings.");
            // Optional: You can force a reload, but usually, users must manually unblock in settings.
            // location.reload();
        } else {
            // State is 'granted' or 'prompt'
            requestBrowserLocation();
        }

        // Watch for permission changes (if user changes it while on page)
        result.onchange = () => {
            if (result.state === 'granted') location.reload();
        };
    });
}

function requestBrowserLocation() {
    const options = { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 };

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const inputElement = document.getElementById('coordDisplay');
            inputElement.value = pos.coords.latitude +',' +pos.coords.longitude;

            console.log("GPS Location:", pos.coords.latitude, pos.coords.longitude);
            // Success logic here
        },
        (err) => {
            if (err.code === err.PERMISSION_DENIED) {
                console.error("User denied location.");
                alert("You denied location access. The page will reload to try again.");
                location.reload(); // Refresh as requested
            } else {
                console.warn("GPS failed or timed out. Falling back to IP Geolocation...");
                getIpLocation();
            }
        },
        options
    );
}

function getIpLocation() {
    fetch('ipapi.co')
        .then(res => res.json())
        .then(data => {
            console.log("IP Location Fallback:", data.latitude, data.longitude);
        })
        .catch(err => console.error("IP Geolocation also failed:", err));
}



</script>
@endsection
