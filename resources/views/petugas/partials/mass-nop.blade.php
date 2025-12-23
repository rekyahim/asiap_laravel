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

<div class="modal fade" id="modalMassNOP" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form id="formMassNOP" method="POST" action="{{ route('petugas.sdt.massupdate.nop.update') }}">
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
                            <select id="selectNOP" name="NOP" class="form-select border-0 shadow-sm" required>
                                <option value="">— Cari NOP —</option>
                                @foreach ($dataNOP as $n)
                                    <option value="{{ $n->NOP }}">{{ $n->NOP }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- INFO NOP --}}
                    <div class="card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-body">

                            <div class="mb-3">
                                <div class="info-label">Nama Wajib Pajak</div>
                                <div id="NOP_NAMA_WP" class="info-value">—</div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Alamat Objek Pajak</div>
                                <div id="NOP_ALAMAT_OP" class="info-value">—</div>
                            </div>

                            <div>
                                <div class="info-label">Tahun Pajak</div>
                                <div id="NOP_TAHUN" class="info-value text-primary">—</div>
                            </div>

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

                                <img id="thumbPrevNOP" class="img-fluid rounded-4 mb-3 shadow"
                                    style="display:none; max-height:250px;">

                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" id="btnOpenCamNOP" class="btn btn-primary rounded-pill"
                                        onclick="openCam('NOP')">
                                        <i class="bi bi-camera me-2"></i>Buka Kamera
                                    </button>

                                    <button type="button" id="btnRetakeNOP" class="btn btn-outline-danger rounded-pill"
                                        style="display:none" onclick="openCam('NOP')">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Ulangi
                                    </button>
                                </div>

                                <div id="lockBadgeNOP" class="badge-gps bg-success-subtle text-success mt-3"
                                    style="display:none">
                                    <i class="bi bi-geo-alt-fill"></i> Lokasi GPS Terkunci
                                </div>

                            </div>
                        </div>

                        {{-- KOORDINAT --}}
                        <div class="col-12 mt-3">
                            <label class="form-label">Titik Koordinat</label>
                            <input type="text" id="KOORDINAT_NOP" class="form-control bg-light" readonly>

                            <input type="hidden" name="FOTO_BASE64_NOP" id="FOTO_BASE64_NOP">
                            <input type="hidden" name="LATITUDE_NOP" id="LATITUDE_NOP">
                            <input type="hidden" name="LONGITUDE_NOP" id="LONGITUDE_NOP">
                        </div>

                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold">
                        Simpan Data
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- MODAL KAMERA UNIVERSAL --}}
@include('petugas.partials.modal-camera')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ==========================================
        // 1. LOGIKA PENGAMBILAN KOORDINAT (GPS)
        // ==========================================
        const modalNOP = document.getElementById('modalMassNOP');
        const latInput = document.getElementById('LATITUDE_NOP');
        const longInput = document.getElementById('LONGITUDE_NOP');
        const dispInput = document.getElementById('KOORDINAT_NOP');
        const badgeGPS = document.getElementById('lockBadgeNOP');

        // Bikin tombol refresh manual di dalam input
        const refreshBtn = document.createElement('button');
        refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        refreshBtn.className =
            'btn btn-sm btn-outline-secondary position-absolute end-0 top-0 mt-1 me-1 border-0';
        refreshBtn.type = 'button';
        refreshBtn.onclick = function() {
            getGeoLocation(true);
        };

        const wrapper = document.createElement('div');
        wrapper.className = 'position-relative';
        if (dispInput) {
            dispInput.parentNode.insertBefore(wrapper, dispInput);
            wrapper.appendChild(dispInput);
            wrapper.appendChild(refreshBtn);
        }

        // Jalankan saat modal terbuka
        if (modalNOP) {
            modalNOP.addEventListener('shown.bs.modal', function() {
                latInput.value = '';
                longInput.value = '';
                dispInput.value = '';
                if (!latInput.value || !longInput.value) {
                    getGeoLocation(true); // Mulai dengan High Accuracy
                }
            });
        }

        function getGeoLocation(isHighAccuracy) {
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
                    showPosition,
                    function(error) {
                        handleError(error, isHighAccuracy)
                    },
                    options
                );
            } else {
                alert("Browser tidak mendukung Geolocation.");
            }
        }

        function showPosition(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const acc = position.coords.accuracy;

            latInput.value = lat;
            longInput.value = lng;
            dispInput.value = `${lat}, ${lng}`;

            badgeGPS.style.display = 'inline-flex';
            dispInput.classList.remove('text-muted', 'text-danger');
            dispInput.classList.add('text-success', 'fw-bold');
        }

        function handleError(error, wasHighAccuracy) {
            // Jika Timeout saat Mode GPS -> Pindah ke Mode Jaringan
            if (error.code === error.TIMEOUT && wasHighAccuracy) {
                console.log("GPS Timeout, beralih ke Network...");
                getGeoLocation(false);
                return;
            }

            badgeGPS.style.display = 'none';
            dispInput.classList.remove('text-muted', 'text-success');
            dispInput.classList.add('text-danger');

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("Izin Lokasi Ditolak. Halaman akan dimuat ulang.");
                    window.location.reload();
                    break;
                case error.POSITION_UNAVAILABLE:
                    dispInput.value = "Sinyal lokasi tidak ditemukan.";
                    break;
                case error.TIMEOUT:
                    dispInput.value = "Gagal. Klik tombol panah di kanan untuk coba lagi.";
                    break;
                case error.UNKNOWN_ERROR:
                    dispInput.value = "Error tidak diketahui.";
                    break;
            }
        }

        // ==========================================
        // 2. LOGIKA FETCH DETAIL NOP (AJAX)
        // ==========================================
        const selectNOP = document.getElementById('selectNOP');

        if (selectNOP) {
            selectNOP.addEventListener('change', function() {
                const nop = this.value;
                const sdtId = "{{ $sdt->ID }}";

                if (!nop) {
                    ['NOP_NAMA_WP', 'NOP_ALAMAT_OP', 'NOP_TAHUN'].forEach(id => {
                        document.getElementById(id).innerText = '—';
                    });
                    return;
                }

                // Efek loading sederhana
                document.getElementById('NOP_NAMA_WP').innerText = 'Loading...';

                fetch(`{{ route('petugas.sdt.api.nop.detail') }}?nop=${nop}&sdt_id=${sdtId}`)
                    .then(r => r.json())
                    .then(d => {
                        document.getElementById('NOP_NAMA_WP').innerText = d.nama_wp ?? '-';
                        document.getElementById('NOP_ALAMAT_OP').innerText = d.alamat_op ?? '-';
                        document.getElementById('NOP_TAHUN').innerText = (d.tahun ?? []).join(', ');
                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('NOP_NAMA_WP').innerText = 'Gagal mengambil data';
                    });
            });
        }
    });
</script>
