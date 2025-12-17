@extends('layouts.admin')

@section('title', 'My Profile')
@section('breadcrumb', 'Admin / My Profile')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8"> {{-- Membatasi lebar profil agar lebih fokus di tengah --}}
                <div class="card shadow-lg border-0 rounded-4 profile-card">

                    {{-- HEADER (Minimalis & Bersih) --}}
                    <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                        <div class="d-flex align-items-center gap-3">

                            {{-- Tombol Back --}}
                            <a href="{{ route('dashboard') }}" class="btn btn-md btn-outline-secondary rounded-3"
                                {{-- Diubah: btn-lg -> btn-md --}} title="Kembali ke Dashboard">
                                <i class="ti ti-arrow-left fs-6"></i> {{-- Diubah: fs-5 -> fs-6 --}}
                            </a>

                            {{-- Judul --}}


                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        {{-- Notifikasi --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-3 border-start border-4 border-success"
                                role="alert">
                                <i class="ti ti-check me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-start border-4 border-danger"
                                role="alert">
                                <i class="ti ti-alert-triangle me-2"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- FOTO PROFIL & Nama --}}
                        <div class="text-center mb-5 border-bottom pb-4">
                            @php
                                // Ambil foto profil
                                $foto = $user->ID_FOTO
                                    ? asset('assets/images/profile/' . $user->ID_FOTO)
                                    : asset('assets/images/profile/default.jpg');
                            @endphp

                            <img src="{{ $foto }}" alt="avatar"
                                class="rounded-circle mb-3 border border-5 border-light-subtle shadow-lg profile-img"
                                width="150" height="150" style="object-fit: cover;">

                            <h5 class="fw-bolder text-dark mb-1">{{ $user->NAMA }}</h5> {{-- Diubah: h4 -> h5 --}}
                            {{-- HAKAKSES (Status) Dihapus dari bawah nama --}}

                            <div class="mt-4 mx-auto btn-group" role="group" style="max-width: 300px;">
                                {{-- Tombol Modal Upload Foto --}}
                                <button class="btn btn-outline-primary py-2 rounded-start-3" data-bs-toggle="modal"
                                    data-bs-target="#updateFotoModal">
                                    <i class="ti ti-photo-plus me-1"></i> Ganti Foto
                                </button>

                                {{-- Tombol Hapus Foto (Hanya tampil jika ada foto) --}}
                                @if ($user->ID_FOTO)
                                    <button class="btn btn-outline-danger py-2 rounded-end-3" data-bs-toggle="modal"
                                        data-bs-target="#deleteFotoModal">
                                        <i class="ti ti-trash me-1"></i> Hapus
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- DETAIL AKUN --}}
                        <h6 class="text-primary fw-bold mb-3"> {{-- Diubah: mb-4 -> mb-3 --}}
                            <i class="ti ti-file-text me-2"></i> Detail Akun
                        </h6>

                        <div class="row g-3"> {{-- Diubah: g-4 -> g-3 (jarak antar item) --}}

                            {{-- Data: Username --}}
                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4 bg-light"> {{-- Diubah: p-4 -> p-3 (padding) --}}
                                    <div class="fw-semibold text-muted small mb-1"><i
                                            class="ti ti-user me-2 text-primary"></i> Username</div> {{-- Diubah: mb-2 -> mb-1 --}}
                                    <div class="text-dark fw-medium">{{ $user->USERNAME }}</div> {{-- Dihapus: fs-6 --}}
                                </div>
                            </div>

                            {{-- Data: NIP --}}
                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4"> {{-- Diubah: p-4 -> p-3 --}}
                                    <div class="fw-semibold text-muted small mb-1"><i
                                            class="ti ti-id me-2 text-primary"></i> NIP</div>
                                    <div class="text-dark fw-medium">{{ $user->NIP ?? '-' }}</div> {{-- Dihapus: fs-6 --}}
                                </div>
                            </div>

                            {{-- Data: Hak Akses --}}
                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4 bg-light"> {{-- Diubah: p-4 -> p-3 --}}
                                    <div class="fw-semibold text-muted small mb-1"><i
                                            class="ti ti-lock-access me-2 text-primary"></i> Hak Akses</div>
                                    <span class="badge bg-success fw-medium py-1 px-2">{{ $user->HAKAKSES }}</span>
                                    {{-- Diubah: py-2 px-3 -> py-1 px-2 --}}
                                </div>
                            </div>

                            {{-- Data: Nama Lengkap (TANPA TOMBOL EDIT) --}}
                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4"> {{-- Diubah: p-4 -> p-3 --}}
                                    <div class="fw-semibold text-muted small mb-1"><i
                                            class="ti ti-address-book me-2 text-primary"></i> Nama Lengkap</div>
                                    <div class="text-dark fw-medium">{{ $user->NAMA }}</div> {{-- Dihapus: fs-6 --}}
                                </div>
                            </div>

                            {{-- Data: Nama Unit --}}
                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4 bg-light"> {{-- Diubah: p-4 -> p-3 --}}
                                    <div class="fw-semibold text-muted small mb-1"><i
                                            class="ti ti-building-community me-2 text-primary"></i> Nama Unit</div>
                                    <div class="text-dark fw-medium">{{ $user->NAMA_UNIT }}</div> {{-- Dihapus: fs-6 --}}
                                </div>
                            </div>

                            {{-- Data: Jabatan --}}
                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4"> {{-- Diubah: p-4 -> p-3 --}}
                                    <div class="fw-semibold text-muted small mb-1"><i
                                            class="ti ti-briefcase me-2 text-primary"></i> Jabatan</div>
                                    <div class="text-dark fw-medium">{{ $user->JABATAN }}</div> {{-- Dihapus: fs-6 --}}
                                </div>
                            </div>

                        </div>

                        {{-- TOMBOL GANTI PASSWORD --}}
                        <div class="mt-4 pt-4 border-top text-center"> {{-- Diubah: mt-5 -> mt-4 --}}
                            <a href="{{ route('profile.change.password') }}"
                                class="btn btn-primary w-100 py-2 fw-bold shadow-sm" style="max-width: 300px;">
                                {{-- Diubah: py-3 -> py-2 --}}
                                <i class="ti ti-key me-1"></i> Ganti Password
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===============================
    MODAL: GANTI FOTO PROFIL
    (Sama seperti sebelumnya)
================================= --}}
    <div class="modal fade" id="updateFotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data" action="{{ route('profile.updatePhoto') }}">
                @csrf
                <div class="modal-content rounded-4">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title"><i class="ti ti-photo-plus me-2"></i> Ganti Foto Profil</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label fw-semibold">Pilih Foto Baru (Maks. 2MB)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary rounded-3">Upload Foto</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===============================
    MODAL: HAPUS FOTO PROFIL
    (Sama seperti sebelumnya)
================================= --}}
    @if ($user->ID_FOTO)
        <div class="modal fade" id="deleteFotoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form method="POST" action="{{ route('profile.deletePhoto') }}">
                    @csrf
                    <div class="modal-content rounded-4">
                        <div class="modal-header bg-danger text-white rounded-top-4">
                            <h5 class="modal-title"><i class="ti ti-alert-triangle me-2"></i> Konfirmasi</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body text-center">
                            Apakah Anda yakin ingin menghapus foto profil?
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-danger rounded-3">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    ---

    <style>
        /* CSS Tambahan untuk penyesuaian visual */
        .profile-card {
            border-radius: 1rem !important;
        }

        /* Efek foto seolah keluar dari card */
        .profile-img {
            margin-top: -100px;
            /* Jarak foto ke atas */
            position: relative;
            z-index: 1;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .card-body {
            padding-top: 1rem !important;
            /* Memberi ruang di atas card body */
        }

        /* Styling untuk Detail Item */
        .detail-item {
            transition: box-shadow 0.2s ease, background-color 0.2s ease;
            border-color: #dee2e6 !important;
        }

        .detail-item:hover {
            background-color: #fcfcfc !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
        }

        .detail-item .small {
            font-size: 0.85rem;
            /* Menjaga label tetap kecil */
        }

        .card-header {
            border-bottom: 1px solid #e9ecef !important;
        }
    </style>
@endsection
