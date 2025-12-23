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

    .form-control,
    .form-select {
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

<div class="modal fade" id="modalMassKO" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formMassKO" method="POST" action="{{ route('petugas.sdt.massupdate.ko.update') }}">
                @csrf
                <input type="hidden" name="ID_SDT" class="form-control" value="{{ $ID_SDT }}">

                {{-- HEADER --}}
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-layers-half me-2"></i>Update Massal KO
                        </h5>
                        <small class="opacity-75">
                            Perbarui status berdasarkan alamat objek pajak
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body p-4">
                    {{-- PILIH KO --}}
                    <div class="card bg-light border-0 rounded-4 mb-4">
                        <div class="card-body p-3">
                            <label class="form-label text-primary">Pilih KO (Alamat OP)</label>
                            <select id="selectKO" name="KO" class="form-select border-0 shadow-sm" required>
                                <option value="">— Cari Alamat KO —</option>
                                @foreach ($dataKO as $k)
                                    <option value="{{ $k->ALAMAT_OP }}">{{ $k->ALAMAT_OP }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Status Penyampaian</label>
                            <select id="selectStatusMass" name="STATUS" class="form-select" required
                                onchange="togglePenerimaMass()">
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

                        {{-- CONTAINER PENERIMA (HIDDEN BY DEFAULT) --}}
                        <div id="divPenerimaMass" class="col-12" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Penerima</label>
                                    <input type="text" id="inputNamaPenerima" name="NAMA_PENERIMA"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">HP Penerima</label>
                                    <input type="text" id="inputHpPenerima" name="HP_PENERIMA" class="form-control">
                                </div>
                            </div>
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

                        <div class="col-12 mt-3">
                            <label class="form-label">Keterangan Petugas</label>
                            <textarea name="KETERANGAN_PETUGAS" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
                        </div>

                        {{-- FOTO KO --}}
                        <div class="col-12 mt-4">
                            <div class="photo-preview-container">
                                <div id="placeholderKO">
                                    <i class="bi bi-camera-fill fs-1 text-muted opacity-50"></i>
                                    <p class="small text-muted mt-2">Ambil foto lokasi</p>
                                </div>

                                <img id="thumbPrevKO" class="img-fluid rounded-4 mb-3 shadow"
                                    style="display:none; max-height:250px;">

                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" id="btnOpenCamKO" class="btn btn-primary rounded-pill"
                                        onclick="openCam('KO')">
                                        <i class="bi bi-camera me-2"></i>Buka Kamera
                                    </button>
                                    <button type="button" id="btnRetakeKO"
                                        class="btn btn-outline-danger rounded-pill" style="display:none"
                                        onclick="openCam('KO')">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Ulangi
                                    </button>
                                </div>

                                <div id="lockBadgeKO" class="badge-gps bg-success-subtle text-success mt-3"
                                    style="display:none">
                                    <i class="bi bi-geo-alt-fill"></i> Lokasi GPS Terkunci
                                </div>
                            </div>
                        </div>

                        {{-- KOORDINAT --}}
                        <div class="col-12 mt-3">
                            <label class="form-label">Titik Koordinat</label>
                            <input type="text" id="KOORDINAT_KO" class="form-control bg-light" readonly>
                            <input type="hidden" name="FOTO_BASE64_KO" id="FOTO_BASE64_KO">
                            <input type="hidden" name="LATITUDE_KO" id="LATITUDE_KO">
                            <input type="hidden" name="LONGITUDE_KO" id="LONGITUDE_KO">
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /**
     * Logika Show/Hide Penerima
     */
    function togglePenerimaMass() {
        const status = document.getElementById('selectStatusMass').value;
        const divPenerima = document.getElementById('divPenerimaMass');
        const inputNama = document.getElementById('inputNamaPenerima');
        const inputHp = document.getElementById('inputHpPenerima');

        if (status === 'TERSAMPAIKAN') {
            divPenerima.style.display = 'block';
            inputNama.setAttribute('required', 'required');
            inputHp.setAttribute('required', 'required');
        } else {
            divPenerima.style.display = 'none';
            inputNama.value = '';
            inputHp.value = '';
            inputNama.removeAttribute('required');
            inputHp.removeAttribute('required');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // ==========================================
        // 1. LOGIKA PENGAMBILAN KOORDINAT (GPS)
        // ==========================================
        const modalKO = document.getElementById('modalMassKO');
        const latInput = document.getElementById('LATITUDE_KO');
        const longInput = document.getElementById('LONGITUDE_KO');
        const dispInput = document.getElementById('KOORDINAT_KO');
        const badgeGPS = document.getElementById('lockBadgeKO');

        // Bikin tombol refresh manual di dalam input
        const refreshBtn = document.createElement('button');
        refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        refreshBtn.className =
            'btn btn-sm btn-outline-secondary position-absolute end-0 top-0 mt-1 me-1 border-0';
        refreshBtn.type = 'button';
        refreshBtn.onclick = function() {
            getGeoLocationKO(true);
        };

        const wrapper = document.createElement('div');
        wrapper.className = 'position-relative';
        if (dispInput) {
            dispInput.parentNode.insertBefore(wrapper, dispInput);
            wrapper.appendChild(dispInput);
            wrapper.appendChild(refreshBtn);
        }

        // Jalankan saat modal terbuka
        if (modalKO) {
            modalKO.addEventListener('shown.bs.modal', function() {
                if (!latInput.value || !longInput.value) {
                    getGeoLocationKO(true); // Mulai dengan High Accuracy
                }
            });

            // Reset Form saat Modal ditutup
            modalKO.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('formMassKO');
                form.reset();

                // Reset UI Manual
                document.getElementById('divPenerimaMass').style.display = 'none';
                document.getElementById('thumbPrevKO').style.display = 'none';
                document.getElementById('placeholderKO').style.display = 'block';
                document.getElementById('btnRetakeKO').style.display = 'none';
                document.getElementById('btnOpenCamKO').style.display = 'block';
                badgeGPS.style.display = 'none';
                dispInput.classList.remove('text-success', 'fw-bold', 'text-danger');
            });
        }

        function getGeoLocationKO(isHighAccuracy) {
            if (navigator.geolocation) {
                dispInput.value = isHighAccuracy ? "Mencari GPS Satelit..." : "Mencari via Jaringan...";
                dispInput.classList.remove('text-danger', 'text-success', 'fw-bold');
                dispInput.classList.add('text-muted');

                const options = {
                    enableHighAccuracy: isHighAccuracy,
                    timeout: 20000, // 20 Detik
                    maximumAge: 0
                };

                navigator.geolocation.getCurrentPosition(
                    showPositionKO,
                    function(error) {
                        handleErrorKO(error, isHighAccuracy)
                    },
                    options
                );
            } else {
                alert("Browser tidak mendukung Geolocation.");
            }
        }

        function showPositionKO(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            latInput.value = lat;
            longInput.value = lng;
            dispInput.value = `${lat}, ${lng}`;

            badgeGPS.style.display = 'inline-flex';
            dispInput.classList.remove('text-muted', 'text-danger');
            dispInput.classList.add('text-success', 'fw-bold');
        }

        function handleErrorKO(error, wasHighAccuracy) {
            // Jika Timeout saat Mode GPS -> Pindah ke Mode Jaringan
            if (error.code === error.TIMEOUT && wasHighAccuracy) {
                console.log("GPS Timeout, beralih ke Network...");
                getGeoLocationKO(false);
                return;
            }

            badgeGPS.style.display = 'none';
            dispInput.classList.remove('text-muted', 'text-success');
            dispInput.classList.add('text-danger');

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("Izin Lokasi Ditolak. Harap izinkan akses lokasi di browser.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    dispInput.value = "Sinyal lokasi tidak ditemukan.";
                    break;
                case error.TIMEOUT:
                    dispInput.value = "Gagal. Klik tombol panah di kanan untuk coba lagi.";
                    break;
                default:
                    dispInput.value = "Error tidak diketahui.";
                    break;
            }
        }
    });
</script>

@include('petugas.partials.modal-camera')
