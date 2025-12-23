@extends('layouts.admin')

@section('title', 'My Profile')
@section('breadcrumb', 'Admin / My Profile')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-4 profile-card">

                    {{-- HEADER --}}
                    <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-md btn-outline-secondary rounded-3"
                               title="Kembali ke Dashboard">
                                <i class="ti ti-arrow-left fs-6"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        {{-- NOTIFIKASI --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-3 border-start border-4 border-success">
                                <i class="ti ti-check me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-start border-4 border-danger">
                                <i class="ti ti-alert-triangle me-2"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- FOTO PROFIL --}}
                        <div class="text-center mb-5 border-bottom pb-4">
                            @php
                                $foto = $user->ID_FOTO
                                    ? asset('assets/images/profile/' . $user->ID_FOTO)
                                    : asset('assets/images/profile/default.jpg');
                            @endphp

                            <img src="{{ $foto }}" alt="avatar"
                                 class="rounded-circle mb-3 border border-5 border-light-subtle shadow-lg profile-img"
                                 width="150" height="150" style="object-fit: cover;">

                            <h5 class="fw-bolder text-dark mb-1">{{ $user->NAMA }}</h5>

                            <div class="mt-4 mx-auto btn-group" role="group" style="max-width: 300px;">
                                <button class="btn btn-outline-primary py-2 rounded-start-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#updateFotoModal">
                                    <i class="ti ti-photo-plus me-1"></i> Ganti Foto
                                </button>

                                @if ($user->ID_FOTO)
                                    <button class="btn btn-outline-danger py-2 rounded-end-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteFotoModal">
                                        <i class="ti ti-trash me-1"></i> Hapus
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- DETAIL AKUN --}}
                        <h6 class="text-primary fw-bold mb-3">
                            <i class="ti ti-file-text me-2"></i> Detail Akun
                        </h6>

                        <div class="row g-3">

                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4 bg-light">
                                    <div class="fw-semibold text-muted small mb-1">
                                        <i class="ti ti-user me-2 text-primary"></i> Username
                                    </div>
                                    <div class="text-dark fw-medium">{{ $user->USERNAME }}</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4">
                                    <div class="fw-semibold text-muted small mb-1">
                                        <i class="ti ti-id me-2 text-primary"></i> NIP
                                    </div>
                                    <div class="text-dark fw-medium">{{ $user->NIP ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4 bg-light">
                                    <div class="fw-semibold text-muted small mb-1">
                                        <i class="ti ti-lock-access me-2 text-primary"></i> Hak Akses
                                    </div>
                                    <span class="badge bg-success fw-medium py-1 px-2">
                                        {{ $user->HAKAKSES }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4">
                                    <div class="fw-semibold text-muted small mb-1">
                                        <i class="ti ti-address-book me-2 text-primary"></i> Nama Lengkap
                                    </div>
                                    <div class="text-dark fw-medium">{{ $user->NAMA }}</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4 bg-light">
                                    <div class="fw-semibold text-muted small mb-1">
                                        <i class="ti ti-building-community me-2 text-primary"></i> Nama Unit
                                    </div>
                                    <div class="text-dark fw-medium">{{ $user->NAMA_UNIT }}</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="detail-item p-3 border rounded-4">
                                    <div class="fw-semibold text-muted small mb-1">
                                        <i class="ti ti-briefcase me-2 text-primary"></i> Jabatan
                                    </div>
                                    <div class="text-dark fw-medium">{{ $user->JABATAN }}</div>
                                </div>
                            </div>

                        </div>

                        {{-- GANTI PASSWORD --}}
                        <div class="mt-4 pt-4 border-top text-center">
                            <a href="{{ route('profile.change.password') }}"
                               class="btn btn-primary w-100 py-2 fw-bold"
                               style="max-width: 300px;">
                                <i class="ti ti-key me-1"></i> Ganti Password
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GANTI FOTO --}}
    <div class="modal fade" id="updateFotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data" action="{{ route('profile.updatePhoto') }}">
                @csrf
                <div class="modal-content rounded-4">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title">
                            <i class="ti ti-photo-plus me-2"></i> Ganti Foto Profil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label fw-semibold">
                            Foto harus berformat JPG, JPEG, dan PNG
                        </label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-primary rounded-3">
                            Upload Foto
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS FOTO --}}
    @if ($user->ID_FOTO)
        <div class="modal fade" id="deleteFotoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <form method="POST" action="{{ route('profile.deletePhoto') }}">
                    @csrf
                    <div class="modal-content rounded-4">
                        <div class="modal-header bg-danger text-white rounded-top-4">
                            <h5 class="modal-title">
                                <i class="ti ti-alert-triangle me-2"></i> Konfirmasi
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body text-center">
                            Apakah Anda yakin ingin menghapus foto profil?
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button class="btn btn-danger rounded-3">
                                Ya, Hapus
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <style>
        .profile-card {
            border-radius: 1rem !important;
        }

        .profile-img {
            margin-top: -100px;
            position: relative;
            z-index: 1;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
        }

        .detail-item {
            transition: box-shadow .2s ease, background-color .2s ease;
            border-color: #dee2e6 !important;
        }

        .detail-item:hover {
            background-color: #fcfcfc !important;
            box-shadow: 0 0 10px rgba(0,0,0,.08);
        }

        /* ===== MOBILE FRIENDLY (TAMBAHAN AMAN) ===== */
       /* =========================================
   HP MODE: SATU LAYAR TANPA SCROLL
   AMAN - CSS ONLY
========================================= */
@media (max-width: 576px) {

    /* Container lebih rapat */
    .container {
        padding-top: .5rem !important;
        padding-bottom: .5rem !important;
    }

    /* Header lebih tipis */
    .card-header {
        padding: .5rem .75rem !important;
    }

    /* Body dipadatkan */
    .card-body {
        padding: .75rem !important;
    }

    /* Foto diperkecil */
    .profile-img {
        margin-top: -40px;
        width: 90px;
        height: 90px;
    }

    /* Nama user lebih kecil */
    h5 {
        font-size: .9rem;
        margin-bottom: .25rem;
    }

    /* Section foto */
    .text-center.mb-5 {
        margin-bottom: .5rem !important;
        padding-bottom: .5rem !important;
    }

    /* Detail akun title */
    h6 {
        font-size: .85rem;
        margin-bottom: .5rem !important;
    }

    /* Detail item super compact */
    .detail-item {
        padding: .4rem .6rem !important;
        font-size: .75rem;
    }

    .detail-item .small {
        font-size: .7rem;
        margin-bottom: 0;
    }

    /* Badge hak akses */
    .badge {
        font-size: .65rem;
        padding: .2rem .4rem;
    }

    /* Jarak antar baris dipersempit */
    .row.g-3 {
        --bs-gutter-y: .3rem;
    }

    /* Tombol foto */
    .btn-group {
        margin-top: .5rem !important;
    }

    .btn-group .btn {
        font-size: .7rem;
        padding: .25rem .5rem;
    }

    /* Tombol ganti password */
    a.btn.w-100 {
        font-size: .75rem;
        padding: .4rem !important;
        margin-top: .5rem !important;
    }

    /* Hilangkan hover effect (HP ga perlu) */
    .detail-item:hover {
        box-shadow: none;
    }
}

    </style>
@endsection
