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

    .info-label {
        font-size: 0.75rem;
        color: #9ca3af;
        font-weight: 600;
        text-transform: uppercase;
    }

    .info-value {
        font-weight: 600;
        color: #1f2937;
    }
</style>

<div class="modal fade" id="modalMassNOP" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formMassNOP" method="POST" action="{{ route('petugas.sdt.massupdate.nop.update') }}">
                @csrf
                <input type="hidden" name="ID_SDT" class="form-control" value="{{ $ID_SDT }}">
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
                            {{-- ID dan ONCHANGE ditambahkan --}}
                            <select id="selectStatusNOP" name="STATUS" class="form-select" required
                                onchange="togglePenerimaNOP()">
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
                        <div id="divPenerimaNOP" class="col-12" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Penerima</label>
                                    <input type="text" id="inputNamaNOP" name="NAMA_PENERIMA" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">HP Penerima</label>
                                    <input type="text" id="inputHpNOP" name="HP_PENERIMA" class="form-control">
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
                            <textarea name="KETERANGAN_PETUGAS" class="form-control" rows="3"
                                placeholder="Tambahkan catatan jika diperlukan..."></textarea>
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
                                    <button type="button" id="btnRetakeNOP"
                                        class="btn btn-outline-danger rounded-pill" style="display:none"
                                        onclick="openCam('NOP')">
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
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk Toggle Field Penerima
    function togglePenerimaNOP() {
        const status = document.getElementById('selectStatusNOP').value;
        const divPenerima = document.getElementById('divPenerimaNOP');
        const inputNama = document.getElementById('inputNamaNOP');
        const inputHp = document.getElementById('inputHpNOP');

        if (status === 'TERSAMPAIKAN') {
            divPenerima.style.display = 'block';
            inputNama.setAttribute('required', 'required');
            inputHp.setAttribute('required', 'required');
        } else {
            divPenerima.style.display = 'none';
            inputNama.value = ''; // Kosongkan nilai
            inputHp.value = ''; // Kosongkan nilai
            inputNama.removeAttribute('required');
            inputHp.removeAttribute('required');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modalNOP = document.getElementById('modalMassNOP');
        const latInput = document.getElementById('LATITUDE_NOP');
        const longInput = document.getElementById('LONGITUDE_NOP');
        const dispInput = document.getElementById('KOORDINAT_NOP');
        const badgeGPS = document.getElementById('lockBadgeNOP');
        const formMassNOP = document.getElementById('formMassNOP');

        // Reset Form & UI saat modal ditutup
        if (modalNOP) {
            modalNOP.addEventListener('hidden.bs.modal', function() {
                formMassNOP.reset();
                // Reset Info NOP
                ['NOP_NAMA_WP', 'NOP_ALAMAT_OP', 'NOP_TAHUN'].forEach(id => {
                    document.getElementById(id).innerText = '—';
                });
                // Reset UI Penerima
                document.getElementById('divPenerimaNOP').style.display = 'none';
                // Reset UI Kamera
                document.getElementById('thumbPrevNOP').style.display = 'none';
                document.getElementById('placeholderNOP').style.display = 'block';
                document.getElementById('btnRetakeNOP').style.display = 'none';
                document.getElementById('lockBadgeNOP').style.display = 'none';
            });

            modalNOP.addEventListener('shown.bs.modal', function() {
                if (!latInput.value || !longInput.value) {
                    getGeoLocation(true);
                }
            });
        }

        // --- Logika GPS (Seperti Kode Awal Anda) ---
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

        function getGeoLocation(isHighAccuracy) {
            if (navigator.geolocation) {
                dispInput.value = isHighAccuracy ? "Mencari GPS Satelit..." : "Mencari via Jaringan...";
                dispInput.classList.remove('text-danger', 'text-success', 'fw-bold');
                const options = {
                    enableHighAccuracy: isHighAccuracy,
                    timeout: 20000,
                    maximumAge: 0
                };
                navigator.geolocation.getCurrentPosition(showPosition, (err) => handleError(err,
                    isHighAccuracy), options);
            }
        }

        function showPosition(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            latInput.value = lat;
            longInput.value = lng;
            dispInput.value = `${lat}, ${lng}`;
            badgeGPS.style.display = 'inline-flex';
            dispInput.classList.add('text-success', 'fw-bold');
        }

        function handleError(error, wasHighAccuracy) {
            if (error.code === error.TIMEOUT && wasHighAccuracy) {
                getGeoLocation(false);
                return;
            }
            badgeGPS.style.display = 'none';
            dispInput.classList.add('text-danger');
            dispInput.value = "Gagal mendapatkan lokasi.";
        }

        // --- Logika AJAX Fetch (Seperti Kode Awal Anda) ---
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
                document.getElementById('NOP_NAMA_WP').innerText = 'Loading...';
                fetch(`{{ route('petugas.sdt.api.nop.detail') }}?nop=${nop}&sdt_id=${sdtId}`)
                    .then(r => r.json())
                    .then(d => {
                        document.getElementById('NOP_NAMA_WP').innerText = d.nama_wp ?? '-';
                        document.getElementById('NOP_ALAMAT_OP').innerText = d.alamat_op ?? '-';
                        document.getElementById('NOP_TAHUN').innerText = (d.tahun ?? []).join(', ');
                    })
                    .catch(() => {
                        document.getElementById('NOP_NAMA_WP').innerText = 'Gagal mengambil data';
                    });
            });
        }
    });
</script>
