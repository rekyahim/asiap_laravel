@extends('layouts.admin')

@section('title', 'Buat SDT Baru')
@section('breadcrumb', '')

@section('content')
    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endonce

    @push('styles')
        <style>
            .page-breadcrumb {
                margin: -.25rem 0 1rem 0
            }

            .crumbs {
                font-size: .9rem
            }

            .crumb {
                color: #6c757d;
                text-decoration: none;
                transition: color .15s ease
            }

            .crumb:hover {
                color: #212529;
                text-decoration: underline
            }

            .crumb.active {
                font-weight: 700;
                color: #212529;
                pointer-events: none;
                text-decoration: none
            }

            .crumb-sep {
                margin: 0 .35rem;
                color: #adb5bd
            }

            .sdt-card {
                box-shadow: 0 6px 18px rgba(0, 0, 0, .06), 0 2px 6px rgba(0, 0, 0, .04);
                border: 1px solid rgba(0, 0, 0, .03);
                border-radius: .75rem;
            }

            .sdt-card:hover {
                box-shadow: 0 10px 24px rgba(0, 0, 0, .08), 0 3px 10px rgba(0, 0, 0, .05)
            }

            .sdt-card .card-header {
                border-bottom: 0;
            }

            .form-floating>.form-control:focus~label,
            .form-floating>.form-control:not(:placeholder-shown)~label {
                opacity: .65;
                transform: scale(.85) translateY(-.5rem) translateX(.15rem);
            }

            .table-example {
                box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
                border-radius: .5rem;
            }

            .req {
                color: #dc3545;
            }

            .alert ul {
                margin-bottom: 0;
                padding-left: 1.2rem;
            }

            .alert li+li {
                margin-top: 2px;
            }

            .alert[role="alert"] {
                font-size: .88rem;
            }

            /* bintang merah untuk kolom wajib */
        </style>
    @endpush

    <div class="container-lg px-0">
        <div class="page-breadcrumb">
            <div class="crumbs">
                <a href="{{ url('/koor') }}" class="crumb">Koordinator</a>
                <span class="crumb-sep">•</span>
                <a href="{{ url('/koor/sdt') }}" class="crumb">Daftar SDT</a>
                <span class="crumb-sep">•</span>
                <span class="crumb active">Tambah SDT</span>
            </div>
        </div>
        {{-- <div class="col-lg-8 col-xl-7"> --}}
        <div class="sdt-card card border-0">
            <div class="card-header bg-white p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="h4 mb-1">Buat SDT Baru</h2>
                        <div class="text-muted small">Lengkapi form di bawah untuk menambahkan Surat Dasar Tugas (SDT).
                        </div>
                    </div>
                    <a href="{{ route('sdt.index') }}" class="btn btn-light">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                @if (session('ok'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ti ti-check me-1"></i> {{ session('ok') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @php
                    // ====== Grup Error Import ======
                    $groups = [
                        'petugas_not_found' => 'Nama Petugas Tidak Ditemukan di Sistem',
                        'petugas_wrong_role' => 'Nama Petugas Bukan dengan Hak Akses "Petugas"',
                        'petugas_not_uppercase' => 'Format Nama Petugas Wajib Huruf Kapital',
                        'row_errors' => 'Kesalahan Data',
                    ];

                    // Ambil named bag "import" dengan cara yang benar
                    $import = $errors->getBag('import');
                @endphp

                @if ($import->any())
                    @foreach ($groups as $key => $title)
                        @if ($import->has($key))
                            <div class="alert alert-danger shadow-sm" role="alert" style="border-left:6px solid #dc3545;">
                                <div class="fw-semibold mb-1">{{ $title }}</div>
                                <ul class="mb-0 small" style="max-height:200px;overflow-y:auto;">
                                    @foreach ($import->get($key) as $msg)
                                        <li>{!! nl2br(e($msg)) !!}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                @endif



                {{-- enctype diperlukan untuk upload file --}}
                <form method="post" action="{{ route('sdt.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('NAMA_SDT') is-invalid @enderror" id="namaSdt"
                            name="NAMA_SDT" placeholder="Nama SDT" value="{{ old('NAMA_SDT') }}" required>
                        <label for="namaSdt">Nama SDT <span class="req">*</span></label>
                        @error('NAMA_SDT')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control @error('TGL_MULAI') is-invalid @enderror"
                                    id="tglMulai" name="TGL_MULAI" placeholder="Tanggal Mulai"
                                    value="{{ old('TGL_MULAI') }}">
                                <label for="tglMulai">Tanggal Mulai<span class="req">*</span></label>
                                @error('TGL_MULAI')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control @error('TGL_SELESAI') is-invalid @enderror"
                                    id="tglSelesai" name="TGL_SELESAI" placeholder="Tanggal Selesai"
                                    value="{{ old('TGL_SELESAI') }}">
                                <label for="tglSelesai">Tanggal Selesai<span class="req">*</span></label>
                                @error('TGL_SELESAI')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ==== Import detail SDT di halaman ini ==== --}}
                    <div class="mb-3">
                        <label for="detailFile" class="form-label fw-semibold">
                            Import SDT Excel (Detail)
                        </label>
                        <input type="file" name="detail_file" id="detailFile"
                            class="form-control @error('detail_file') is-invalid @enderror" accept=".xlsx,.xls,.csv">
                        @error('detail_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="form-text">
                            {{-- Opsional. Jika diisi, baris detail dari file akan langsung ditambahkan ke SDT. --}}
                            Mendukung file <code>.csv</code> , <code>.xlsx/.xls</code>. Maks 10MB.
                            {{-- (butuh paket <code>phpoffice/phpspreadsheet</code>) --}}
                        </div>
                    </div>

                    {{-- Keterangan kolom wajib & alias header --}}
                    <div class="small text-muted mb-2">
                        <strong>Kolom wajib ada & terisi di file:</strong>
                        <span class="ms-1">NAMA PETUGAS, NOP, TAHUN PAJAK</span>.
                        {{-- <br>
                            <strong>Alias header yang didukung:</strong>
                            <code>NAMA PETUGAS</code> → <code>PETUGAS_SDT</code>,
                            <code>TAHUN PAJAK</code> → <code>TAHUN</code>.
                            (Kolom lain opsional dan bisa kamu sesuaikan belakangan.) --}}
                    </div>

                    {{-- Contoh format header/kolom yang didukung --}}
                    <div class="table-responsive small mb-3 table-example">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>NAMA PETUGAS <span class="req">*</span></th>
                                    <th>NOP <span class="req">*</span></th>
                                    <th>TAHUN PAJAK <span class="req">*</span></th>
                                    <th>ALAMAT_OP</th>
                                    <th>BLOK_KAV_NO_OP</th>
                                    <th>RT_OP</th>
                                    <th>RW_OP</th>
                                    <th>KEL_OP</th>
                                    <th>KEC_OP</th>
                                    <th>NAMA_WP</th>
                                    <th>ALAMAT_WP</th>
                                    <th>BLOK_KAV_NO_WP</th>
                                    <th>RT_WP</th>
                                    <th>RW_WP</th>
                                    <th>KEL_WP</th>
                                    <th>KOTA_WP</th>
                                    <th>JATUH_TEMPO</th>
                                    <th>TERHUTANG</th>
                                    <th>PENGURANGAN</th>
                                    <th>PBB_HARUS_DIBAYAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ANDI</td>
                                    <td>14.71.040.012.345-6789.0</td>
                                    <td>2024</td>
                                    <td>Jl. Contoh No. 1</td>
                                    <td>Blok A-1</td>
                                    <td>01</td>
                                    <td>02</td>
                                    <td>Kel. X</td>
                                    <td>Kec. Y</td>
                                    <td>Nama WP</td>
                                    <td>Alamat WP</td>
                                    <td>A-2</td>
                                    <td>03</td>
                                    <td>04</td>
                                    <td>Kel. Z</td>
                                    <td>Pekanbaru</td>
                                    <td>2024-09-30</td>
                                    <td>150000</td>
                                    <td>0</td>
                                    <td>150000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a class="btn btn-outline-secondary" href="{{ route('sdt.index') }}">
                            <i class="ti ti-x me-1"></i> Batal
                        </a>
                        <button class="btn btn-primary fw-semibold" type="submit">
                            <i class="ti ti-check me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 mt-3" style="box-shadow:0 4px 12px rgba(0,0,0,.05); border-radius:.75rem;">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="ti ti-info-circle text-primary mt-1"></i>
                    <div class="small text-muted">
                        <strong>Tips:</strong> Kosongkan tanggal bila belum ditetapkan.
                        Kamu bisa impor detail belakangan dari halaman ini (unggah ulang file),
                        dan petugas per NOP bisa ditambah manual dari halaman daftar SDT → tombol <em>Tambah Petugas
                            Manual</em>.
                    </div>
                </div>
            </div>
        </div>
        {{-- </div> --}}
    </div>
@endsection
