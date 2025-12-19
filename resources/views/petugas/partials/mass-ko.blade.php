<!-- ========================= MODAL MASS KO ========================= -->
<div class="modal fade" id="modalMassKO" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">

      <form method="POST" action="{{ route('petugas.sdt.massupdate.ko.update') }}">
        @csrf

        <div class="modal-header" style="background: linear-gradient(135deg,#1e3c72,#2a5298); color:#fff;">
          <h5 class="modal-title fw-bold">Update Massal Berdasarkan KO</h5>
          <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Kembali</button>
        </div>

        <div class="modal-body px-4 pb-4">

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih KO</label>
            <select id="selectKO" name="KO"
              class="form-select rounded-3 shadow-sm select-search" required>
              <option value="">-- Pilih KO --</option>
              @foreach($dataKO as $k)
                <option value="{{ $k->ALAMAT_OP }}">{{ $k->ALAMAT_OP }}</option>
              @endforeach
            </select>
          </div>
          <hr class="my-3">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status Penyampaian</label>
              <select name="STATUS" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Pilih Status --</option>
                <option value="TERSAMPAIKAN">Tersampaikan</option>
                <option value="TIDAK TERSAMPAIKAN">Tidak Tersampaikan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">NOP Benar?</label>
              <select name="NOP_BENAR" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Pilih --</option>
                <option value="YA">YA</option>
                <option value="TIDAK">TIDAK</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama Penerima</label>
              <input type="text" name="NAMA_PENERIMA" class="form-control rounded-3 shadow-sm">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">HP Penerima</label>
              <input type="text" name="HP_PENERIMA" class="form-control rounded-3 shadow-sm">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status OP</label>
              <select name="STATUS_OP" class="form-select rounded-3 shadow-sm">
                <option value="">-- Pilih Status OP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status WP</label>
              <select name="STATUS_WP" class="form-select rounded-3 shadow-sm">
                <option value="">-- Pilih Status WP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>

            <!-- Koordinat (pindah di atas keterangan) -->
            <div class="col-12 mt-2">
              <label class="form-label fw-semibold">Koordinat</label>
              <input type="text" id="KOORDINAT_KO" name="KOORDINAT" class="form-control bg-light rounded-3 shadow-sm" readonly placeholder="—">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Keterangan</label>
              <textarea name="KETERANGAN" class="form-control rounded-3 shadow-sm" rows="2"></textarea>
            </div>

            <!-- Kamera KO -->
            <div class="col-12 mt-3">
              <label class="form-label fw-semibold">Ambil Foto KO</label>
              <div class="d-flex align-items-center gap-2">
                <img id="thumbPrevKO" style="display:none; max-height:80px; border-radius:8px; border:1px solid #ddd;">
                <button type="button" id="btnOpenCamKO" class="btn btn-primary btn-sm rounded-3"
                        data-bs-toggle="modal" data-bs-target="#modalCameraKO" style="background:#246BFD; border-color:#246BFD;">Buka Kamera</button>
                <button type="button" id="btnRetakeKO" class="btn btn-outline-secondary btn-sm" style="display:none;">Ulangi</button>
                <span id="lockBadgeKO" class="badge bg-success" style="display:none;">GPS Locked</span>
              </div>

              <input type="hidden" name="FOTO_BASE64" id="FOTO_BASE64_KO">
              <input type="hidden" name="LATITUDE" id="LATITUDE_KO">
              <input type="hidden" name="LONGITUDE" id="LONGITUDE_KO">
            </div>

          </div>
        </div>

        <div class="modal-footer border-0 px-4 pb-4" style="background: linear-gradient(180deg,#f7fbff, #fff);">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn" style="background:#246BFD; color:#fff; font-weight:600; border-radius:10px; padding:8px 18px;">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- ========================= MODAL MASS NOP ========================= -->
<div class="modal fade" id="modalMassNOP" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">

      <form method="POST" action="{{ route('petugas.sdt.massupdate.nop.update') }}">
        @csrf

        <div class="modal-header" style="background: linear-gradient(135deg,#1e3c72,#2a5298); color:#fff;">
          <h5 class="modal-title fw-bold">Update Massal Berdasarkan NOP</h5>
          <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Kembali</button>
        </div>

        <div class="modal-body px-4 pb-4">

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih NOP</label>
            <select id="selectNOP" name="NOP"
              class="form-select rounded-3 shadow-sm select-search" required>
              <option value="">-- Pilih NOP --</option>
              @foreach($dataNOP as $n)
                <option value="{{ $n->NOP }}">{{ $n->NOP }}</option>
              @endforeach
            </select>
          </div>
          <hr class="my-3">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status Penyampaian</label>
              <select name="STATUS" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Pilih Status --</option>
                <option value="TERSAMPAIKAN">Tersampaikan</option>
                <option value="TIDAK TERSAMPAIKAN">Tidak Tersampaikan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">NOP Benar?</label>
              <select name="NOP_BENAR" class="form-select rounded-3 shadow-sm" required>
                <option value="">-- Pilih --</option>
                <option value="YA">YA</option>
                <option value="TIDAK">TIDAK</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama Penerima</label>
              <input type="text" name="NAMA_PENERIMA" class="form-control rounded-3 shadow-sm">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">HP Penerima</label>
              <input type="text" name="HP_PENERIMA" class="form-control rounded-3 shadow-sm">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status OP</label>
              <select name="STATUS_OP" class="form-select rounded-3 shadow-sm">
                <option value="">-- Pilih Status OP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status WP</label>
              <select name="STATUS_WP" class="form-select rounded-3 shadow-sm">
                <option value="">-- Pilih Status WP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>

            <!-- Koordinat (pindah di atas keterangan) -->
            <div class="col-12 mt-2">
              <label class="form-label fw-semibold">Koordinat</label>
              <input type="text" id="KOORDINAT_NOP" name="KOORDINAT" class="form-control bg-light rounded-3 shadow-sm" readonly placeholder="—">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Keterangan</label>
              <textarea name="KETERANGAN" class="form-control rounded-3 shadow-sm" rows="2"></textarea>
            </div>

            <!-- Kamera NOP -->
            <div class="col-12 mt-3">
              <label class="form-label fw-semibold">Ambil Foto NOP</label>
              <div class="d-flex align-items-center gap-2">
                <img id="thumbPrevNOP" style="display:none; max-height:80px; border-radius:8px; border:1px solid #ddd;">
                <button type="button" id="btnOpenCamNOP" class="btn btn-primary btn-sm rounded-3"
                        data-bs-toggle="modal" data-bs-target="#modalCameraNOP" style="background:#246BFD; border-color:#246BFD;">Buka Kamera</button>
                <button type="button" id="btnRetakeNOP" class="btn btn-outline-secondary btn-sm" style="display:none;">Ulangi</button>
                <span id="lockBadgeNOP" class="badge bg-success" style="display:none;">GPS Locked</span>
              </div>

              <input type="hidden" name="FOTO_BASE64" id="FOTO_BASE64_NOP">
              <input type="hidden" name="LATITUDE" id="LATITUDE_NOP">
              <input type="hidden" name="LONGITUDE" id="LONGITUDE_NOP">
            </div>

          </div>
        </div>

        <div class="modal-footer border-0 px-4 pb-4" style="background: linear-gradient(180deg,#f7fbff, #fff);">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn" style="background:#246BFD; color:#fff; font-weight:600; border-radius:10px; padding:8px 18px;">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- ========================= STYLE UNTUK KEDUA MODAL KAMERA (BLUE PREMIUM) ========================= -->
<style>
  .camera-modal {
      background: #000;
      border-radius: 0;
      height: 100vh;
      padding: 0;
      margin: 0;
      overflow: hidden;
  }

  .camera-header {
      background: linear-gradient(135deg,#1e3c72,#2a5298);
      border: none !important;
      height: 70px;
      display: flex;
      align-items: center;
      padding: 0 18px;
  }

  .camera-header h5 {
      color: #fff;
      font-weight: 700;
      margin: 0;
      font-size: 18px;
  }

  .camera-close {
      filter: brightness(300%);
  }

  .camera-body {
      padding: 0;
      margin: 0;
      width: 100%;
      height: calc(100vh - 140px);
      display: flex;
      justify-content: center;
      align-items: center;
      background: #000;
  }

  .camera-body video {
      width: 100%;
      height: auto;
      border-radius: 12px;
      max-height: 90vh;
      object-fit: cover;
      box-shadow: 0 12px 30px rgba(16, 24, 40, 0.4);
  }

  .camera-footer {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      width: 100%;
      text-align: center;
      z-index: 50;
  }

  .btn-shot {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background: #fff;
      border: 7px solid #246BFD;
      box-shadow: 0 10px 30px rgba(36,107,253,0.35);
      transition: 0.18s ease;
  }

  .btn-shot:active {
      transform: scale(0.94);
      box-shadow: 0 6px 22px rgba(36,107,253,0.5);
  }

  /* Form elements modern */
  .form-select.rounded-3, .form-control.rounded-3, textarea.form-control {
      border: 1px solid rgba(16,24,40,0.06);
      padding: 10px 12px;
      transition: box-shadow .15s ease, border-color .15s ease;
  }

  .form-select.rounded-3:focus, .form-control.rounded-3:focus, textarea.form-control:focus {
      outline: none;
      box-shadow: 0 6px 20px rgba(36,107,253,0.12);
      border-color: #246BFD;
  }

  .shadow-sm {
      box-shadow: 0 6px 18px rgba(16,24,40,0.04) !important;
  }

  .badge.bg-success {
      background:#2ecc71 !important;
      color:#fff;
  }

</style>

<!-- ========================= MODAL KAMERA KO ========================= -->
<div class="modal fade" id="modalCameraKO" tabindex="-1">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content camera-modal">

      <div class="modal-header camera-header">
        <h5 class="modal-title">Ambil Foto KO</h5>
        <button type="button" class="btn-close camera-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body camera-body">
        <video id="videoStreamKO" autoplay playsinline></video>
        <canvas id="canvasKO" style="display:none;"></canvas>
      </div>

      <div class="camera-footer">
        <button id="btnCaptureKO" class="btn-shot" title="Jepret"></button>
      </div>

    </div>
  </div>
</div>

<!-- ========================= MODAL KAMERA NOP ========================= -->
<div class="modal fade" id="modalCameraNOP" tabindex="-1">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content camera-modal">

      <div class="modal-header camera-header">
        <h5 class="modal-title">Ambil Foto NOP</h5>
        <button type="button" class="btn-close camera-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body camera-body">
        <video id="videoStreamNOP" autoplay playsinline></video>
        <canvas id="canvasNOP" style="display:none;"></canvas>
      </div>

      <div class="camera-footer">
        <button id="btnCaptureNOP" class="btn-shot" title="Jepret"></button>
      </div>

    </div>
  </div>
</div>

<!-- ========================= JQUERY ========================= -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ========================= SELECT2 ========================= -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('shown.bs.modal', function (event) {
    // Hilangkan focus trap Bootstrap supaya input Select2 bisa diketik
    event.target.removeAttribute('tabindex');
});
</script>


<script>
$(document).on('shown.bs.modal', '#modalMassKO, #modalMassNOP', function () {

    const $modal = $(this);

    $modal.find('.select-search').select2({
        width: '100%',
        dropdownParent: $modal,
        placeholder: 'Ketik untuk mencari...',
        allowClear: true,
        minimumResultsForSearch: 0
    });

});
</script>

<!-- ========================= SCRIPT CAMERA & GPS ========================= -->
<script>
(function(){
  function $id(id){ return document.getElementById(id); }

  function setupCamera(videoId, canvasId, inputId, thumbId, lockId, btnCaptureId, btnOpenId, btnRetakeId, massModalId){
      const video = $id(videoId);
      const canvas = $id(canvasId);
      const input = $id(inputId);
      const thumb = $id(thumbId);
      const lockBadge = $id(lockId);
      const btnCapture = $id(btnCaptureId);
      const btnOpen = $id(btnOpenId);
      const btnRetake = $id(btnRetakeId);
      let stream;

      if(!video || !canvas || !input || !btnCapture) return;

      const modalEl = video.closest('.modal');

      function startCamera() {
          if(stream) stopCamera();
          navigator.mediaDevices.getUserMedia({ video:{ facingMode:{ ideal:'environment' }}})
          .then(s => {
              stream = s;
              video.srcObject = s;
              video.play();
              btnCapture.disabled = false;
          })
          .catch(err => {
              console.error(err);
              btnCapture.disabled = true;
              alert("Gagal membuka kamera");
          });
      }

      function stopCamera() {
          if(stream){
              stream.getTracks().forEach(t => t.stop());
              stream = null;
          }
          btnCapture.disabled = true;
      }

      if(modalEl){
          modalEl.addEventListener('shown.bs.modal', startCamera);
          modalEl.addEventListener('hidden.bs.modal', stopCamera);
      }

      btnCapture.addEventListener('click', () => {
          canvas.width = video.videoWidth || 1280;
          canvas.height = video.videoHeight || 720;
          const ctx = canvas.getContext('2d');
          ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

          const dataURL = canvas.toDataURL('image/jpeg', 0.9);
          input.value = dataURL;

          if(thumb){
              thumb.src = dataURL;
              thumb.style.display = 'inline-block';
          }
          if(btnRetake) btnRetake.style.display = 'inline-block';
          if(btnOpen) btnOpen.style.display = 'none';

          if(navigator.geolocation){
              navigator.geolocation.getCurrentPosition(pos => {
                  const lat = pos.coords.latitude;
                  const lon = pos.coords.longitude;

                  if (massModalId === 'modalMassKO') {
                      if($id('LATITUDE_KO')) $id('LATITUDE_KO').value = lat;
                      if($id('LONGITUDE_KO')) $id('LONGITUDE_KO').value = lon;
                      if($id('KOORDINAT_KO')) $id('KOORDINAT_KO').value = lat+', '+lon;
                  } else {
                      if($id('LATITUDE_NOP')) $id('LATITUDE_NOP').value = lat;
                      if($id('LONGITUDE_NOP')) $id('LONGITUDE_NOP').value = lon;
                      if($id('KOORDINAT_NOP')) $id('KOORDINAT_NOP').value = lat+', '+lon;
                  }

                  if(lockBadge) lockBadge.style.display = 'inline-block';
              });
          }

          stopCamera();
          bootstrap.Modal.getInstance(modalEl)?.hide();
          new bootstrap.Modal($id(massModalId)).show();
      });

      if(btnRetake){
          btnRetake.addEventListener('click', () => {
              input.value = '';
              if(thumb){ thumb.src=''; thumb.style.display='none'; }
              if(lockBadge) lockBadge.style.display='none';
              btnRetake.style.display='none';
              if(btnOpen) btnOpen.style.display='inline-block';

              ['LATITUDE','LONGITUDE','KOORDINAT'].forEach(f=>{
                const el = $id(f + (massModalId==='modalMassKO'?'_KO':'_NOP'));
                if(el) el.value='';
              });

              new bootstrap.Modal(modalEl).show();
          });
      }
  }

  setupCamera(
    'videoStreamKO','canvasKO','FOTO_BASE64_KO',
    'thumbPrevKO','lockBadgeKO',
    'btnCaptureKO','btnOpenCamKO','btnRetakeKO','modalMassKO'
  );

  setupCamera(
    'videoStreamNOP','canvasNOP','FOTO_BASE64_NOP',
    'thumbPrevNOP','lockBadgeNOP',
    'btnCaptureNOP','btnOpenCamNOP','btnRetakeNOP','modalMassNOP'
  );

})();
</script>
