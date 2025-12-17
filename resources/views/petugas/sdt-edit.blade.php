@extends('layouts.admin')

@section('title', 'Petugas / Edit SDT')
@section('breadcrumb', 'Petugas / Edit SDT')

@php
    // Smart-back (kembali ke halaman utama SDT)
    $paramBack = request('back');
    $decodedBack = $paramBack ? urldecode($paramBack) : null;
    $goBack =
        $decodedBack && str_starts_with($decodedBack, url('/'))
            ? $decodedBack
            : route('petugas.sdt.detail', $row->ID_SDT);
@endphp

@section('content')
    <style>
        :root {
            --bg: #f6f7fb;
            --card: #fff;
            --line: #e6e8ec;
            --text: #0f172a;
            --muted: #64748b;
            --accent: #2563eb;
            --accent-2: #1d4ed8;
            --radius: 12px;
            --shadow: 0 8px 18px rgba(2, 6, 23, .08);
        }

        .section {
            background: var(--bg);
            border-radius: 16px;
            padding: 14px
        }

        .card-clean {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow)
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-bottom: 1px solid var(--line)
        }

        .page-title {
            margin: 0;
            font-weight: 800;
            color: var(--text);
            font-size: 1.05rem
        }

        .small-muted {
            font-size: .78rem;
            color: var(--muted)
        }

        .hr-soft {
            border-top: 1px solid var(--line);
            margin: 8px 0
        }

        .form-label {
            font-size: .86rem;
            margin-bottom: .25rem
        }

        .form-control,
        .form-select {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: .38rem .6rem;
            height: 2.15rem
        }

        .input-readonly {
            background: #f1f5f9;
            color: #475569
        }

        .btn-quiet {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 10px;
            font-weight: 700;
            padding: .3rem .55rem
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border: 1px solid var(--accent-2);
            color: #fff;
            font-weight: 800;
            border-radius: 10px;
            padding: .45rem .8rem
        }

        .badge-soft {
            background: #f3f4f6;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: .1rem .5rem;
            font-weight: 700;
            color: #334155;
            font-size: .72rem
        }

        .grid-tight {
            row-gap: .6rem
        }

        .col-gap-tight>[class^="col-"] {
            padding-right: .4rem;
            padding-left: .4rem
        }

        .mb-tight {
            margin-bottom: .4rem
        }

        .thumb {
            max-height: 78px;
            border: 1px solid var(--line);
            border-radius: 8px
        }

        #modalCamera .modal-content {
            border-radius: 12px
        }

        #modalCamera .modal-header {
            padding: 8px 12px
        }

        #modalCamera .modal-body {
            padding: 10px 12px
        }

        .input-group-sm>.form-select {
            height: 2rem
        }

        .shot-wrap {
            display: flex;
            justify-content: center;
            padding-top: .4rem
        }

        .btn-shot-round {
            width: 50px;
            height: 50px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 18px rgba(37, 99, 235, .22)
        }

        .btn-shot-round svg {
            width: 20px;
            height: 20
        }

        .form-section {
            display: none
        }

        .form-section.show {
            display: block
        }
    </style>

    <div class="section">
        <div class="card-clean">
            <div class="card-header">
                <h5 class="page-title">Edit Detail SDT</h5>
                <a href="{{ $goBack }}" class="btn btn-quiet btn-sm">Kembali</a>
            </div>

            @if (session('info'))
                <div class="alert alert-info m-3" style="border-radius:10px;">
                    {{ session('info') }}
                </div>
            @endif

            {{-- ========================= FORM ========================= --}}
            <form id="frmEdit" method="POST"
                action="{{ route('petugas.sdt.update', $row->ID) }}?back={{ urlencode($paramBack ?? $goBack) }}">
                @csrf

                {{-- Hidden guard / geo / foto --}}
                <input type="hidden" name="ID_SDT" value="{{ $row->ID_SDT }}">
                <input type="hidden" name="FOTO_BASE64" id="FOTO_BASE64">
                <input type="hidden" name="LATITUDE" id="LATITUDE">
                <input type="hidden" name="LONGITUDE" id="LONGITUDE">
                <input type="hidden" name="KOORDINAT_OP" id="KOORDINAT_OP">
                {{-- Keterangan dikirimkan ke dua field agar controller dapat membaca --}}
                <input type="hidden" name="KETERANGAN_PETUGAS" id="KETERANGAN_PETUGAS">
                <input type="hidden" name="KETERANGAN" id="KETERANGAN">

                @if ($paramBack)
                    <input type="hidden" name="back" value="{{ $paramBack }}">
                @else
                    <input type="hidden" name="back" value="{{ urlencode($goBack) }}">
                @endif

                {{-- ========================= ISI FORM ========================= --}}
                <div class="row grid-tight col-gap-tight">
                    {{-- Baris 1 --}}
                    <div class="col-lg-6">
                        <label class="form-label">Master SDT</label>
                        <select class="form-select input-readonly" disabled>
                            @foreach ($master as $m)
                                <option value="{{ $m->ID }}" {{ $m->ID == $row->ID_SDT ? 'selected' : '' }}>
                                    {{ $m->NAMA_SDT }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">NOP</label>
                        <input type="text" class="form-control input-readonly" value="{{ $row->NOP }}" disabled>
                    </div>

                    {{-- Baris 2 --}}
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label">Tahun</label>
                        <input type="text" class="form-control input-readonly" value="{{ $row->TAHUN }}" disabled>
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <label class="form-label">Alamat OP</label>
                        <input type="text" class="form-control input-readonly" value="{{ $row->ALAMAT_OP }}" disabled>
                    </div>

                    {{-- Baris 3 --}}
                    <div class="col-lg-4">
                        <label class="form-label">Nama WP</label>
                        <input type="text" class="form-control input-readonly" value="{{ $row->NAMA_WP }}" disabled>
                    </div>

                    {{-- Status penyampaian (sesuai controller) --}}
                    <div class="col-lg-4">
                        <label class="form-label">Status Penyampaian</label>
                        <select name="STATUS_PENYAMPAIAN" id="STATUS" class="form-select"
                            {{ $expired ? 'disabled' : '' }} required>
                            <option value="">— Pilih —</option>
                            <option value="TERSAMPAIKAN" {{ strtoupper($row->STATUS) == 'TERSAMPAIKAN' ? 'selected' : '' }}>
                                Tersampaikan</option>
                            <option value="TIDAK TERSAMPAIKAN"
                                {{ strtoupper($row->STATUS) == 'TIDAK TERSAMPAIKAN' ? 'selected' : '' }}>Tidak Tersampaikan
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-4">
                        <label class="form-label">Koordinat</label>
                        <input type="text" id="coordDisplay" class="form-control input-readonly" placeholder="—"
                            disabled>
                        <small class="small-muted" id="coordHint" style="display:none"></small>
                    </div>

                    {{-- NOP_BENAR --}}
                    <div class="col-lg-4">
                        <label class="form-label">Apakah NOP benar?</label>
                        <select name="NOP_BENAR" id="NOP_BENAR" class="form-select" required>
                            <option value="">— Pilih —</option>
                            <option value="YA" {{ strtoupper($row->NOP_BENAR ?? '') == 'YA' ? 'selected' : '' }}>YA</option>
                            <option value="TIDAK" {{ strtoupper($row->NOP_BENAR ?? '') == 'TIDAK' ? 'selected' : '' }}>TIDAK
                            </option>
                        </select>
                    </div>

                    {{-- Jika tersampaikan --}}
                    <div id="secTersampaikan" class="form-section col-12">
                        <div class="row grid-tight col-gap-tight">
                            <div class="col-md-6">
                                <label class="form-label">Nama Penerima</label>
                                <input type="text" name="NAMA_PENERIMA" id="NAMA_PENERIMA" class="form-control"
                                    value="{{ old('NAMA_PENERIMA', $row->NAMA_PENERIMA) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">HP Penerima</label>
                                <input type="text" name="HP_PENERIMA" id="HP_PENERIMA" class="form-control"
                                    inputmode="numeric" value="{{ old('HP_PENERIMA', $row->HP_PENERIMA) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <input type="text" id="KETERANGAN_OK" class="form-control"
                                    value="{{ old('KETERANGAN_PETUGAS', $row->KETERANGAN_PETUGAS) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Jika tidak tersampaikan --}}
                    <div id="secTidak" class="form-section col-12">
                        <div class="row grid-tight col-gap-tight">
                            <div class="col-md-6">
                                <label class="form-label">Status OP</label>
                                <select name="STATUS_OP" id="STATUS_OP" class="form-select">
                                    @php $sop = $row->STATUS_OP ?? 'Belum Diproses Petugas'; @endphp
                                    <option {{ $sop == 'Belum Diproses Petugas' ? 'selected' : '' }}>Belum Diproses Petugas
                                    </option>
                                    <option {{ $sop == 'Ditemukan' ? 'selected' : '' }}>Ditemukan</option>
                                    <option {{ $sop == 'Tidak Ditemukan' ? 'selected' : '' }}>Tidak Ditemukan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status WP</label>
                                <select name="STATUS_WP" id="STATUS_WP" class="form-select">
                                    @php $swp = $row->STATUS_WP ?? 'Belum Diproses Petugas'; @endphp
                                    <option {{ $swp == 'Belum Diproses Petugas' ? 'selected' : '' }}>Belum Diproses Petugas
                                    </option>
                                    <option {{ $swp == 'Ditemukan' ? 'selected' : '' }}>Ditemukan</option>
                                    <option {{ $swp == 'Tidak Ditemukan' ? 'selected' : '' }}>Tidak Ditemukan</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <input type="text" id="KETERANGAN_NO" class="form-control"
                                    value="{{ old('KETERANGAN_PETUGAS', $row->KETERANGAN_PETUGAS) }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr class="hr-soft">
                    </div>

                    {{-- Kamera --}}
                    <div class="col-12">
                        <div class="d-flex align-items-start gap-2 flex-wrap">
                            <div class="me-2">
                                <div class="small-muted mb-tight">Preview</div>
                                <img id="thumbPrev" class="thumb" style="display:none" alt="Preview">
                            </div>
                            <div class="d-flex flex-column gap-1">
                                <button type="button" id="btnOpenCam" class="btn btn-quiet btn-sm"
                                    {{ $expired ? 'disabled' : '' }} data-bs-toggle="modal"
                                    data-bs-target="#modalCamera">
                                    Buka Kamera
                                </button>
                                <div>
                                    <button type="button" id="btnRetake" class="btn btn-quiet btn-sm"
                                        style="display:none;">Ulangi</button>
                                </div>
                                <span id="lockBadge" class="badge-soft"
                                    style="display:none; width:max-content;">Geo-locked</span>
                            </div>

                            @if (!empty($row->EVIDENCE))
                                <div class="ms-auto">
                                    <a href="{{ asset('storage/' . $row->EVIDENCE) }}" class="small-muted" target="_blank"
                                        rel="noopener">Lihat foto saat ini →</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mt-3">
                    <button class="btn btn-accent" id="btnSave" {{ $expired ? 'disabled' : '' }}>Simpan</button>
                    <a href="{{ $goBack }}" class="btn btn-quiet">Batal</a>
                    @if ($expired)
                        <span class="small-muted ms-1">Terkunci (&gt;6 jam).</span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ========================= MODAL KAMERA ========================= --}}
    <div class="modal fade" id="modalCamera" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Kamera</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group input-group-sm mb-2">
                        <select id="cameraSelect" class="form-select form-select-sm"></select>
                        <button type="button" id="btnFlip" class="btn btn-outline-secondary">Putar</button>
                    </div>
                    <div class="border rounded p-2" style="border-color:var(--line)!important">
                        <video id="camVideo" playsinline autoplay muted
                            style="max-width:100%;width:100%;border-radius:.6rem;"></video>
                        <canvas id="camCanvas"
                            style="display:none;max-width:100%;width:100%;border-radius:.6rem;"></canvas>
                    </div>
                    <div class="shot-wrap">
                        <button type="button" id="btnShotModal" class="btn btn-primary btn-shot-round" title="Jepret"
                            disabled>
                            <svg viewBox="0 0 16 16" fill="currentColor">
                                <path
                                    d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .4.2l1.2 1.6H15a1 1 0 0 1 1 1V13a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4.8A1.3 1.3 0 0 1 1.3 3.5H3l1-1z" />
                                <circle cx="8" cy="9" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================= SCRIPT ========================= --}}
    <script>
        (() => {
            const $ = s => document.querySelector(s);
            const expired = {{ $expired ? 'true' : 'false' }};

            const foto64 = $('#FOTO_BASE64');
            const lat = $('#LATITUDE');
            const lng = $('#LONGITUDE');
            const prev = $('#thumbPrev');
            const hint = $('#coordHint');
            const lock = $('#lockBadge');
            const btnSave = $('#btnSave');
            const coordDisplay = $('#coordDisplay');
            const btnOpenCam = $('#btnOpenCam');
            const koordHidden = $('#KOORDINAT_OP');
            const ketPetugas = $('#KETERANGAN_PETUGAS');
            const ket = $('#KETERANGAN');
            const selStatus = $('#STATUS');
            const secOk = $('#secTersampaikan');
            const secNo = $('#secTidak');
            const namaP = $('#NAMA_PENERIMA');
            const hpP = $('#HP_PENERIMA');

            function syncStatusUI() {
                const v = (selStatus.value || '').toUpperCase();
                const isOk = v === 'TERSAMPAIKAN';
                secOk.classList.toggle('show', isOk);
                secNo.classList.toggle('show', !isOk && v);
                if (namaP) namaP.required = isOk;
                if (hpP) hpP.required = isOk;
            }
            selStatus?.addEventListener('change', syncStatusUI);
            syncStatusUI();

            function updateCoordDisplay() {
                if (lat.value && lng.value) {
                    coordDisplay.value = `${Number(lat.value).toFixed(6)},${Number(lng.value).toFixed(6)}`;
                }
            }

            function getLocationOnce() {
                if (lat.value && lng.value) {
                    updateCoordDisplay();
                    return;
                }
                if (!('geolocation' in navigator)) {
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        lat.value = pos.coords.latitude;
                        lng.value = pos.coords.longitude;
                        hint.textContent = `±${Math.round(pos.coords.accuracy||0)}m`;
                        hint.style.display = '';
                        lock.style.display = '';
                        updateCoordDisplay();
                    },
                    _ => {
                        lock.style.display = '';
                    }, {
                        enableHighAccuracy: true,
                        timeout: 8000,
                        maximumAge: 0
                    }
                );
            }

            function showPreview(d) {
                prev.src = d;
                prev.style.display = '';
            }

            const mCam = $('#modalCamera');
            const camSel = $('#cameraSelect');
            const video = $('#camVideo');
            const canvas = $('#camCanvas');
            const btnShot = $('#btnShotModal');
            const btnRet = $('#btnRetake');

            function stopStream() {
                try {
                    video.pause();
                    video.srcObject?.getTracks()?.forEach(t => t.stop());
                    video.srcObject = null;
                } catch (_) {}
                btnShot.disabled = true;
            }
            async function startStream(constraints) {
                stopStream();
                try {
                    const stream = await navigator.mediaDevices.getUserMedia(constraints || {
                        video: {
                            facingMode: {
                                ideal: 'environment'
                            },
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            }
                        },
                        audio: false
                    });
                    video.srcObject = stream;
                    video.onloadedmetadata = () => {
                        video.play().finally(() => btnShot.disabled = false);
                    };
                } catch (e) {
                    alert('Gagal membuka kamera');
                    stopStream();
                }
            }
            async function listCams() {
                try {
                    const devs = await navigator.mediaDevices.enumerateDevices();
                    const cams = devs.filter(d => d.kind === 'videoinput');
                    camSel.innerHTML = '';
                    cams.forEach((d, i) => {
                        const o = document.createElement('option');
                        o.value = d.deviceId;
                        o.textContent = d.label || `Kamera ${i+1}`;
                        camSel.appendChild(o);
                    });
                } catch (_) {}
            }

            function takeShot() {
                if (btnShot.disabled) return alert('Kamera belum siap');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                const d = canvas.toDataURL('image/jpeg', 0.85);
                foto64.value = d;
                showPreview(d);
                stopStream();
                window.bootstrap.Modal.getOrCreateInstance(mCam)?.hide();
                btnRet.style.display = 'inline-block';
                btnOpenCam.style.display = 'none';
                getLocationOnce();
            }

            function retake() {
                foto64.value = '';
                prev.src = '';
                prev.style.display = 'none';
                btnRet.style.display = 'none';
                btnOpenCam.style.display = '';
            }

            mCam?.addEventListener('shown.bs.modal', async () => {
                await listCams();
                startStream();
            });
            mCam?.addEventListener('hidden.bs.modal', stopStream);
            camSel?.addEventListener('change', e => startStream({
                video: {
                    deviceId: {
                        exact: e.target.value
                    }
                },
                audio: false
            }));
            $('#btnFlip')?.addEventListener('click', () => startStream({
                video: {
                    facingMode: {
                        ideal: 'user'
                    },
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    }
                },
                audio: false
            }));
            btnShot?.addEventListener('click', takeShot);
            btnRet?.addEventListener('click', retake);

            $('#frmEdit')?.addEventListener('submit', e => {
                // set koordinat hidden
                if (lat.value && lng.value) koordHidden.value = `${Number(lat.value)},${Number(lng.value)}`;
                else koordHidden.value = '';

                // ambil keterangan berdasarkan status
                const v = (selStatus.value || '').toUpperCase();
                const kOk = $('#KETERANGAN_OK')?.value?.trim() || '';
                const kNo = $('#KETERANGAN_NO')?.value?.trim() || '';
                const finalK = (v === 'TERSAMPAIKAN') ? kOk : kNo;
                if (ketPetugas) ketPetugas.value = finalK;
                if (ket) ket.value = finalK; // juga isi KETERANGAN agar controller menerima

                // validasi NOP_BENAR
                const nb = ($('#NOP_BENAR')?.value || '').toUpperCase();
                if (!['YA', 'TIDAK'].includes(nb)) {
                    e.preventDefault();
                    return alert('Pilih NOP BENAR: YA atau TIDAK');
                }

                // foto wajib kalau belum expired
                if (!foto64.value && !expired) {
                    e.preventDefault();
                    return alert('Harap ambil foto dari kamera terlebih dahulu.');
                }

                btnSave.disabled = true;
            });

            updateCoordDisplay();
        })();
    </script>
@endsection
