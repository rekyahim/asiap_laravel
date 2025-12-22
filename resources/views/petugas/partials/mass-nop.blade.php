<style>
  :root {
    --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
    --glass-bg: rgba(255, 255, 255, 0.95);
  }

  .modal-content {
    border-radius: 24px !important;
    border: none;
    overflow: hidden;
    background: var(--glass-bg);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  }

  .modal-header {
    background: var(--primary-gradient);
    color: white;
    padding: 1.5rem 2rem;
    border-bottom: 0;
  }

  .modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
  }

  .form-label {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    margin-bottom: 0.5rem;
  }

  .form-control, .form-select {
    border-radius: 12px;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e5e7eb;
  }

  .photo-preview-container {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
  }

  .badge-gps {
    padding: 0.6rem 1.2rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
</style>

<div class="modal fade"
     id="modalMassNOP"
     tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form id="formMassNOP"
            method="POST"
            action="{{ route('petugas.sdt.massupdate.nop.update') }}">
        @csrf

        {{-- HEADER --}}
        <div class="modal-header">
          <div>
            <h5 class="modal-title fw-bold">
              <i class="bi bi-files me-2"></i>Update Massal NOP
            </h5>
            <small class="opacity-75">
              Perbarui status berdasarkan NOP
            </small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        {{-- BODY --}}
        <div class="modal-body p-4">

          {{-- PILIH NOP --}}
          <div class="card bg-light border-0 rounded-4 mb-4">
            <div class="card-body p-3">
              <label class="form-label text-primary">Pilih NOP</label>
              <select id="selectNOP"
                      name="NOP"
                      class="form-select border-0 shadow-sm"
                      required>
                <option value="">— Cari NOP —</option>
                @foreach($dataNOP as $n)
                  <option value="{{ $n->NOP }}">{{ $n->NOP }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Status Penyampaian</label>
              <select name="STATUS" class="form-select" required>
                <option value="">— Pilih —</option>
                <option value="TERSAMPAIKAN">Tersampaikan</option>
                <option value="TIDAK TERSAMPAIKAN">Tidak Tersampaikan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">NOP Benar?</label>
              <select name="NOP_BENAR" class="form-select" required>
                <option value="YA">YA</option>
                <option value="TIDAK">TIDAK</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nama Penerima</label>
              <input type="text" name="NAMA_PENERIMA" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">HP Penerima</label>
              <input type="text" name="HP_PENERIMA" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">Status OP</label>
              <select name="STATUS_OP" class="form-select">
                <option value="">-- Pilih Status OP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
                <option value="Sudah Dijual">Sudah Dijual</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Status WP</label>
              <select name="STATUS_WP" class="form-select">
                <option value="">-- Pilih Status WP --</option>
                <option value="Belum Diproses Petugas">Belum Diproses Petugas</option>
                <option value="Ditemukan">Ditemukan</option>
                <option value="Tidak Ditemukan">Tidak Ditemukan</option>
                <option value="Diluar Kota">Diluar Kota</option>
              </select>
            </div>

            {{-- FOTO NOP --}}
            <div class="col-12 mt-4">
              <div class="photo-preview-container">

                <div id="placeholderNOP">
                  <i class="bi bi-camera-fill fs-1 text-muted opacity-50"></i>
                  <p class="small text-muted mt-2">Ambil foto lokasi</p>
                </div>

                <img id="thumbPrevNOP"
                     class="img-fluid rounded-4 mb-3 shadow"
                     style="display:none; max-height:250px;">

                <div class="d-flex justify-content-center gap-2">
                  <button type="button"
                          id="btnOpenCamNOP"
                          class="btn btn-primary rounded-pill"
                          onclick="openCam('NOP')">
                    <i class="bi bi-camera me-2"></i>Buka Kamera
                  </button>

                  <button type="button"
                          id="btnRetakeNOP"
                          class="btn btn-outline-danger rounded-pill"
                          style="display:none"
                          onclick="openCam('NOP')">
                    <i class="bi bi-arrow-clockwise me-2"></i>Ulangi
                  </button>
                </div>

                <div id="lockBadgeNOP"
                     class="badge-gps bg-success-subtle text-success mt-3"
                     style="display:none">
                  <i class="bi bi-geo-alt-fill"></i> Lokasi GPS Terkunci
                </div>

              </div>
            </div>

            {{-- KOORDINAT --}}
            <div class="col-12 mt-3">
              <label class="form-label">Titik Koordinat</label>
              <input type="text"
                     id="KOORDINAT_NOP"
                     class="form-control bg-light"
                     readonly>

              <input type="hidden" name="FOTO_BASE64_NOP" id="FOTO_BASE64_NOP">
              <input type="hidden" name="LATITUDE_NOP" id="LATITUDE_NOP">
              <input type="hidden" name="LONGITUDE_NOP" id="LONGITUDE_NOP">
            </div>

          </div>
        </div>

        {{-- FOOTER --}}
        <div class="modal-footer border-0 p-4 pt-0">
          <button type="button"
                  class="btn btn-link text-muted"
                  data-bs-dismiss="modal">
            Batal
          </button>

          <button type="submit"
                  class="btn btn-primary px-5 rounded-pill fw-bold">
            Simpan Data
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- MODAL KAMERA UNIVERSAL --}}
@include('petugas.partials.modal-camera')
