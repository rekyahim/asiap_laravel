<!-- ========================= MODAL MASS KO ========================= -->
<div class="modal fade" id="modalMassKO" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">

      <form method="POST" action="{{ route('petugas.sdt.massupdate.ko.update') }}">
        @csrf

        <div class="modal-header bg-white border-0 px-4 pt-4 pb-2">
          <h5 class="modal-title fw-bold">Update Massal Berdasarkan KO</h5>
          <button type="button" class="btn btn-light btn-sm rounded-3 fw-semibold" data-bs-dismiss="modal">Kembali</button>
        </div>

        <div class="modal-body px-4 pb-4">

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih KO</label>
            <select id="selectKO" name="KO" class="form-select rounded-3" required>
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
              <select name="STATUS" class="form-select rounded-3" required>
                <option value="">-- Pilih Status --</option>
                <option value="TERSAMPAIKAN">Tersampaikan</option>
                <option value="TIDAK TERSAMPAIKAN">Tidak Tersampaikan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">NOP Benar?</label>
              <select name="NOP_BENAR" class="form-select rounded-3" required>
                <option value="">-- Pilih --</option>
                <option value="YA">YA</option>
                <option value="TIDAK">TIDAK</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama Penerima</label>
              <input type="text" name="NAMA_PENERIMA" class="form-control rounded-3">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">HP Penerima</label>
              <input type="text" name="HP_PENERIMA" class="form-control rounded-3">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status OP</label>
              <select name="STATUS_OP" class="form-select rounded-3">
                <option value="">-- Pilih Status OP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status WP</label>
              <select name="STATUS_WP" class="form-select rounded-3">
                <option value="">-- Pilih Status WP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Keterangan</label>
              <textarea name="KETERANGAN" class="form-control rounded-3" rows="2"></textarea>
            </div>

            <!-- Kamera KO -->
            <div class="col-12 mt-3">
              <label class="form-label fw-semibold">Ambil Foto KO</label>
              <div class="d-flex align-items-center gap-2">
                <img id="thumbPrevKO" style="display:none; max-height:80px; border-radius:8px; border:1px solid #ddd;">
                <button type="button" id="btnOpenCamKO" class="btn btn-primary btn-sm rounded-3"
                        data-bs-toggle="modal" data-bs-target="#modalCameraKO">Buka Kamera</button>
                <button type="button" id="btnRetakeKO" class="btn btn-outline-secondary btn-sm" style="display:none;">Ulangi</button>
                <span id="lockBadgeKO" class="badge bg-success" style="display:none;">GPS Locked</span>
              </div>

              <input type="hidden" name="FOTO_BASE64" id="FOTO_BASE64_KO">
              <input type="hidden" name="LATITUDE" id="LATITUDE_KO">
              <input type="hidden" name="LONGITUDE" id="LONGITUDE_KO">
            </div>

            <!-- Koordinat -->
            <div class="col-12 mt-2">
              <label class="form-label fw-semibold">Koordinat</label>
              <input type="text" id="KOORDINAT_KO" class="form-control bg-light rounded-3" readonly placeholder="—">
            </div>

          </div>
        </div>

        <div class="modal-footer border-0 px-4 pb-4">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4">Simpan</button>
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

        <div class="modal-header bg-white border-0 px-4 pt-4 pb-2">
          <h5 class="modal-title fw-bold">Update Massal Berdasarkan NOP</h5>
          <button type="button" class="btn btn-light btn-sm rounded-3 fw-semibold" data-bs-dismiss="modal">Kembali</button>
        </div>

        <div class="modal-body px-4 pb-4">

          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih NOP</label>
            <select id="selectNOP" name="NOP" class="form-select rounded-3" required>
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
              <select name="STATUS" class="form-select rounded-3" required>
                <option value="">-- Pilih Status --</option>
                <option value="TERSAMPAIKAN">Tersampaikan</option>
                <option value="TIDAK TERSAMPAIKAN">Tidak Tersampaikan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">NOP Benar?</label>
              <select name="NOP_BENAR" class="form-select rounded-3" required>
                <option value="">-- Pilih --</option>
                <option value="YA">YA</option>
                <option value="TIDAK">TIDAK</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Nama Penerima</label>
              <input type="text" name="NAMA_PENERIMA" class="form-control rounded-3">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">HP Penerima</label>
              <input type="text" name="HP_PENERIMA" class="form-control rounded-3">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status OP</label>
              <select name="STATUS_OP" class="form-select rounded-3">
                <option value="">-- Pilih Status OP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status WP</label>
              <select name="STATUS_WP" class="form-select rounded-3">
                <option value="">-- Pilih Status WP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Keterangan</label>
              <textarea name="KETERANGAN" class="form-control rounded-3" rows="2"></textarea>
            </div>

            <!-- Kamera NOP -->
            <div class="col-12 mt-3">
              <label class="form-label fw-semibold">Ambil Foto NOP</label>
              <div class="d-flex align-items-center gap-2">
                <img id="thumbPrevNOP" style="display:none; max-height:80px; border-radius:8px; border:1px solid #ddd;">
                <button type="button" id="btnOpenCamNOP" class="btn btn-primary btn-sm rounded-3"
                        data-bs-toggle="modal" data-bs-target="#modalCameraNOP">Buka Kamera</button>
                <button type="button" id="btnRetakeNOP" class="btn btn-outline-secondary btn-sm" style="display:none;">Ulangi</button>
                <span id="lockBadgeNOP" class="badge bg-success" style="display:none;">GPS Locked</span>
              </div>

              <input type="hidden" name="FOTO_BASE64" id="FOTO_BASE64_NOP">
              <input type="hidden" name="LATITUDE" id="LATITUDE_NOP">
              <input type="hidden" name="LONGITUDE" id="LONGITUDE_NOP">
            </div>

            <!-- Koordinat -->
            <div class="col-12 mt-2">
              <label class="form-label fw-semibold">Koordinat</label>
              <input type="text" id="KOORDINAT_NOP" class="form-control bg-light rounded-3" readonly placeholder="—">
            </div>

          </div>
        </div>

        <div class="modal-footer border-0 px-4 pb-4">
          <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold rounded-3 px-4">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- ========================= MODAL KAMERA KO ========================= -->
<div class="modal fade" id="modalCameraKO" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Ambil Foto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <video id="videoStreamKO" autoplay playsinline style="width:100%; border-radius:10px;"></video>
        <canvas id="canvasKO" style="display:none;"></canvas>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnCaptureKO" class="btn btn-primary w-100 fw-bold">Jepret</button>
      </div>
    </div>
  </div>
</div>

<!-- ========================= MODERN MODAL KAMERA NOP ========================= -->
<style>
  .camera-modal-content {
      background: #000;
      border-radius: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
  }

  .camera-header {
      background: rgba(0, 0, 0, 0.4);
      border: none !important;
  }

  .camera-header h5 {
      color: #fff;
      font-weight: bold;
  }

  #videoStreamNOP {
      width: 100%;
      height: auto;
      border-radius: 14px;
      box-shadow: 0 0 18px rgba(0,0,0,0.5);
  }

  .camera-footer {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      width: 100%;
      display: flex;
      justify-content: center;
  }

  #btnCaptureNOP {
      width: 85px;
      height: 85px;
      border-radius: 50%;
      background: white;
      border: 5px solid #ddd;
      box-shadow: 0px 0px 10px rgba(255,255,255,0.5);
  }

  #btnCaptureNOP:active {
      transform: scale(0.95);
  }
</style>

<div class="modal fade" id="modalCameraNOP" tabindex="-1">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content camera-modal-content border-0">

      <div class="modal-header camera-header">
        <h5 class="modal-title">Ambil Foto</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body d-flex justify-content-center align-items-center p-2">
        <video id="videoStreamNOP" autoplay playsinline></video>
        <canvas id="canvasNOP" style="display:none;"></canvas>
      </div>

      <div class="camera-footer">
        <button type="button" id="btnCaptureNOP"></button>
      </div>

    </div>
  </div>
</div>

<!-- ========================= SCRIPT CAMERA & GPS ========================= -->
<script>
function setupCamera(videoId, canvasId, inputId, thumbId, lockId, btnCaptureId, btnOpenId, btnRetakeId, massModalId){
    const video = document.getElementById(videoId);
    const canvas = document.getElementById(canvasId);
    const input = document.getElementById(inputId);
    const thumb = document.getElementById(thumbId);
    const lockBadge = document.getElementById(lockId);
    const btnCapture = document.getElementById(btnCaptureId);
    const btnOpen = document.getElementById(btnOpenId);
    const btnRetake = document.getElementById(btnRetakeId);
    let stream;

    function startCamera() {
        if(stream) stopCamera();
        navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'}}})
        .then(s => {
            stream = s;
            video.srcObject = s;
            video.play().finally(() => btnCapture.disabled = false);
        })
        .catch(err => {
            alert("Gagal membuka kamera: " + err);
            btnCapture.disabled = true;
        });
    }

    function stopCamera() {
        if(stream){
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
        btnCapture.disabled = true;
    }

    const modalEl = video.closest('.modal');
    modalEl.addEventListener('shown.bs.modal', startCamera);
    modalEl.addEventListener('hidden.bs.modal', stopCamera);

    btnCapture.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataURL = canvas.toDataURL('image/jpeg', 0.85);
        input.value = dataURL;
        thumb.src = dataURL;
        thumb.style.display = 'inline-block';
        btnRetake.style.display = 'inline-block';
        btnOpen.style.display = 'none';

        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lon = pos.coords.longitude;

            if (massModalId === 'modalMassKO') {
                document.getElementById('LATITUDE_KO').value = lat;
                document.getElementById('LONGITUDE_KO').value = lon;
                document.getElementById('KOORDINAT_KO').value = lat + ', ' + lon;
            } else {
                document.getElementById('LATITUDE_NOP').value = lat;
                document.getElementById('LONGITUDE_NOP').value = lon;
                document.getElementById('KOORDINAT_NOP').value = lat + ', ' + lon;
            }

            lockBadge.style.display = 'inline-block';
        });

        stopCamera();
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if(modalInstance) modalInstance.hide();

        const massModal = new bootstrap.Modal(document.getElementById(massModalId));
        massModal.show();
    });

   btnRetake.addEventListener('click', () => {
    input.value = '';
    thumb.src = '';
    thumb.style.display = 'none';
    btnRetake.style.display = 'none';
    btnOpen.style.display = 'inline-block';
    lockBadge.style.display = 'none';

    // RESET KOORDINAT + LAT + LON (BIAR JEPRET ULANG DAPAT GPS BARU)
    if (massModalId === 'modalMassKO') {
        document.getElementById('LATITUDE_KO').value = '';
        document.getElementById('LONGITUDE_KO').value = '';
        document.getElementById('KOORDINAT_KO').value = '';
    } else {
        document.getElementById('LATITUDE_NOP').value = '';
        document.getElementById('LONGITUDE_NOP').value = '';
        document.getElementById('KOORDINAT_NOP').value = '';
    }

    startCamera();
});

}

// Init KO & NOP
setupCamera('videoStreamKO','canvasKO','FOTO_BASE64_KO','thumbPrevKO','lockBadgeKO','btnCaptureKO','btnOpenCamKO','btnRetakeKO','modalMassKO');
setupCamera('videoStreamNOP','canvasNOP','FOTO_BASE64_NOP','thumbPrevNOP','lockBadgeNOP','btnCaptureNOP','btnOpenCamNOP','btnRetakeNOP','modalMassNOP');
</script>
