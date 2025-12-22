{{-- =========================================================
     MODAL KAMERA UNIVERSAL (KO & NOP) - FINAL FIX
========================================================= --}}

<style>
  .camera-modal-content {
    background: #000 !important;
    border-radius: 0 !important;
  }

  .camera-footer {
    position: absolute;
    bottom: 50px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 20;
  }

  .camera-capture-btn {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #fff;
    border: 6px solid rgba(255,255,255,0.3);
    transition: transform .2s;
  }

  .camera-capture-btn:active {
    transform: scale(.9);
  }
</style>

<div class="modal fade"
     id="modalCamera"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     style="z-index:1065">

  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content camera-modal-content">

      <div class="modal-header border-0 position-absolute w-100"
           style="z-index:10; background:linear-gradient(to bottom, rgba(0,0,0,.5), transparent);">
        <h5 class="modal-title text-white">Kamera Petugas</h5>
        <button type="button"
                class="btn-close btn-close-white"
                onclick="closeCamera()"></button>
      </div>

      <div class="modal-body p-0 d-flex align-items-center justify-content-center">
        <video id="videoStream"
               autoplay
               playsinline
               style="width:100%; height:100%; object-fit:cover;"></video>
        <canvas id="canvasSnap" style="display:none;"></canvas>
      </div>

      <div class="camera-footer">
        <button type="button"
                id="btnCapture"
                class="camera-capture-btn shadow-lg"
                disabled></button>
      </div>

    </div>
  </div>
</div>

<script>
/* =========================================================
   MODAL KAMERA FIX (KO & NOP)
========================================================= */

let currentTarget = '';
let stream = null;

document.addEventListener('DOMContentLoaded', () => {

  const camModalEl = document.getElementById('modalCamera');
  const video     = document.getElementById('videoStream');
  const canvas    = document.getElementById('canvasSnap');
  const btnCapture= document.getElementById('btnCapture');

  /* OPEN CAMERA */
  window.openCam = (target) => {
    currentTarget = target;
    new bootstrap.Modal(camModalEl, {
      backdrop: 'static',
      keyboard: false
    }).show();
  };

  /* CLOSE CAMERA */
  window.closeCamera = () => {
    if (stream) {
      stream.getTracks().forEach(t => t.stop());
      stream = null;
    }
    bootstrap.Modal.getInstance(camModalEl).hide();
  };

  /* CAMERA ON */
  camModalEl.addEventListener('shown.bs.modal', async () => {
    try {
      stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: 'environment' } }
      });
      video.srcObject = stream;
      btnCapture.disabled = false;
    } catch (e) {
      alert('Kamera tidak dapat diakses');
      closeCamera();
    }
  });

  /* CAMERA OFF */
  camModalEl.addEventListener('hidden.bs.modal', () => {
    if (stream) {
      stream.getTracks().forEach(t => t.stop());
      stream = null;
    }
    btnCapture.disabled = true;
  });

  /* CAPTURE */
  btnCapture.addEventListener('click', () => {
    if (!currentTarget) return;

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const base64 = canvas.toDataURL('image/jpeg', 0.8);

    document.getElementById(`FOTO_BASE64_${currentTarget}`).value = base64;
    document.getElementById(`thumbPrev${currentTarget}`).src = base64;
    document.getElementById(`thumbPrev${currentTarget}`).style.display = 'block';
    document.getElementById(`placeholder${currentTarget}`).style.display = 'none';
    document.getElementById(`btnRetake${currentTarget}`).style.display = 'inline-block';
    document.getElementById(`btnOpenCam${currentTarget}`).style.display = 'none';

    navigator.geolocation.getCurrentPosition(pos => {
      document.getElementById(`LATITUDE_${currentTarget}`).value  = pos.coords.latitude;
      document.getElementById(`LONGITUDE_${currentTarget}`).value = pos.coords.longitude;
      document.getElementById(`KOORDINAT_${currentTarget}`).value =
        pos.coords.latitude + ', ' + pos.coords.longitude;
      document.getElementById(`lockBadge${currentTarget}`).style.display = 'inline-flex';
    });

    closeCamera();
  });

});
</script>
