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

          <!-- Pilih NOP -->
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
              <select name="STATUS" id="STATUS_NOP" class="form-select rounded-3" required>
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

            <!-- Jika Tersampaikan -->
            <div class="col-md-6 d-none" id="wrapPenerimaNOP">
              <label class="form-label fw-semibold">Nama Penerima</label>
              <input type="text" name="NAMA_PENERIMA" class="form-control rounded-3">
            </div>
            <div class="col-md-6 d-none" id="wrapHPPenerimaNOP">
              <label class="form-label fw-semibold">HP Penerima</label>
              <input type="text" name="HP_PENERIMA" class="form-control rounded-3">
            </div>

            <!-- Jika Tidak Tersampaikan -->
            <div class="col-md-6 d-none" id="wrapStatusOP_NOP">
              <label class="form-label fw-semibold">Status OP</label>
              <select name="STATUS_OP" class="form-select rounded-3">
                <option value="">-- Pilih Status OP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
              </select>
            </div>
            <div class="col-md-6 d-none" id="wrapStatusWP_NOP">
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

            <!-- Kamera -->
            <div class="col-12 mt-3">
              <label class="form-label fw-semibold">Ambil Foto</label>
              <div class="d-flex align-items-center gap-2">
                <img id="thumbPrevNOP" style="display:none; max-height:80px; border-radius:8px; border:1px solid #ddd;">
                <button type="button" id="btnOpenCamNOP" class="btn btn-primary btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#modalCameraNOP">Buka Kamera</button>
                <button type="button" id="btnRetakeNOP" class="btn btn-outline-secondary btn-sm" style="display:none;">Ulangi</button>
                <span id="lockBadgeNOP" class="badge bg-success" style="display:none;">GPS Locked</span>
              </div>
              <input type="hidden" name="FOTO_BASE64" id="FOTO_BASE64_NOP">
              <input type="hidden" name="LATITUDE" id="LATITUDE_NOP">
              <input type="hidden" name="LONGITUDE" id="LONGITUDE_NOP">
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

<!-- ========================= MODAL KAMERA NOP ========================= -->
<div class="modal fade" id="modalCameraNOP" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Ambil Foto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <video id="videoStreamNOP" autoplay playsinline style="width:100%; border-radius:10px;"></video>
        <canvas id="canvasNOP" style="display:none;"></canvas>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnCaptureNOP" class="btn btn-primary w-100 fw-bold">Jepret</button>
      </div>
    </div>
  </div>
</div>


<!-- ========================= SELECT2 ========================= -->
<script>
$(document).ready(function(){
    $('#selectKO').select2({
        dropdownParent: $('#modalMassKO'),
        placeholder: "-- Pilih KO --",
        allowClear: true,
        width: '100%'
    });

    $('#selectNOP').select2({
        dropdownParent: $('#modalMassNOP'),
        placeholder: "-- Pilih NOP --",
        allowClear: true,
        width: '100%'
    });
});
</script>

<!-- ========================= LOGIKA STATUS PENYAMPAIAN ========================= -->
<script>
function toggleFields(statusId, wrapP, wrapHP, wrapOP, wrapWP){
    const statusEl = document.getElementById(statusId);
    statusEl.addEventListener('change', function(){
        const val = this.value;
        [wrapP, wrapHP, wrapOP, wrapWP].forEach(el => {
            if(el) document.getElementById(el).classList.add('d-none');
        });
        if(val === "TERSAMPAIKAN"){
            if(wrapP) document.getElementById(wrapP).classList.remove('d-none');
            if(wrapHP) document.getElementById(wrapHP).classList.remove('d-none');
        }
        if(val === "TIDAK TERSAMPAIKAN"){
            if(wrapOP) document.getElementById(wrapOP).classList.remove('d-none');
            if(wrapWP) document.getElementById(wrapWP).classList.remove('d-none');
        }
    });
}

// KO
toggleFields('STATUS_MASS','groupPenerima','groupHPPenerima','groupStatusOP','groupStatusWP');
// NOP
toggleFields('STATUS_NOP','wrapPenerimaNOP','wrapHPPenerimaNOP','wrapStatusOP_NOP','wrapStatusWP_NOP');
</script>

<!-- ========================= CAMERA & GPS LOGIC FINAL KO/NOP ========================= -->
<script>
function setupCamera(videoId, canvasId, inputId, thumbId, lockId, btnCaptureId, btnOpenId, btnRetakeId){
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

    // Tombol Jepret
    btnCapture.addEventListener('click', () => {
        // Ambil snapshot
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataURL = canvas.toDataURL('image/jpeg', 0.85);
        input.value = dataURL;
        thumb.src = dataURL;
        thumb.style.display = 'inline-block';

        btnRetake.style.display = 'inline-block';
        btnOpen.style.display = 'none';

        // Ambil GPS
        navigator.geolocation.getCurrentPosition(pos => {
            document.getElementById(inputId.replace('FOTO_BASE64','LATITUDE')).value = pos.coords.latitude;
            document.getElementById(inputId.replace('FOTO_BASE64','LONGITUDE')).value = pos.coords.longitude;
            lockBadge.style.display = 'inline-block';
        });

        stopCamera(); // Hentikan kamera

        // Tutup modal kamera
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if(modalInstance) modalInstance.hide();

        // Buka kembali modal Mass KO/NOP otomatis
        if(videoId.includes("KO")){
            const massModalKO = new bootstrap.Modal(document.getElementById('modalMassKO'));
            massModalKO.show();
        } else if(videoId.includes("NOP")){
            const massModalNOP = new bootstrap.Modal(document.getElementById('modalMassNOP'));
            massModalNOP.show();
        }
    });

    // Tombol Ulangi
    btnRetake.addEventListener('click', () => {
        input.value = '';
        thumb.src = '';
        thumb.style.display = 'none';
        btnRetake.style.display = 'none';
        btnOpen.style.display = 'inline-block';
        lockBadge.style.display = 'none';
        startCamera();
    });
}

// Inisialisasi KO & NOP
setupCamera('videoStreamKO','canvasKO','FOTO_BASE64_KO','thumbPrevKO','lockBadgeKO','btnCaptureKO','btnOpenCamKO','btnRetakeKO');
setupCamera('videoStreamNOP','canvasNOP','FOTO_BASE64_NOP','thumbPrevNOP','lockBadgeNOP','btnCaptureNOP','btnOpenCamNOP','btnRetakeNOP');
</script>
