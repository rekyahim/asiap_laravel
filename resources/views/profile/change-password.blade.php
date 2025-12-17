@extends('layouts.admin')

@section('title', 'Ganti Password')

@section('content')

    <div class="card border-0 shadow-lg rounded-4"> {{-- Container Card --}}

        {{-- HEADER --}}
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center gap-3">

                {{-- BACK TO DASHBOARD --}}
                <a href="{{ route('dashboard') }}" class="btn btn-lg btn-outline-secondary rounded-3"
                    title="Kembali ke Dashboard">
                    <i class="ti ti-arrow-left fs-5"></i>
                </a>

                <h5 class="fw-bold text-dark mb-0">
                    Ganti Password
                </h5>

            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body p-5">

            {{-- SUCCESS --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-start border-4 border-success"
                    role="alert">
                    <i class="ti ti-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ERROR --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-start border-4 border-danger"
                    role="alert">
                    <i class="ti ti-alert-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- VALIDATION --}}
            @if ($errors->any())
                <div class="alert alert-warning rounded-3 border-start border-4 border-warning" role="alert">
                    <p class="mb-2 fw-bold">
                        <i class="ti ti-info-circle me-1"></i> Periksa kembali input Anda:
                    </p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM --}}
            <div class="mx-auto" style="max-width: 650px;">
                <form action="{{ route('profile.change.password.update') }}" method="POST">
                    @csrf

                    {{-- PASSWORD LAMA --}}
                    <div class="mb-4">
                        <label for="old_password" class="form-label fw-bold text-dark">Password Lama</label>
                        <div class="input-group input-group-lg">
                            <input type="password" name="old_password" id="old_password"
                                class="form-control rounded-start-3 @error('old_password') is-invalid @enderror" required
                                placeholder="Masukkan password lama">

                            <button type="button" class="btn btn-outline-secondary toggle-password rounded-end-3"
                                data-target="old_password" title="Tampilkan/Sembunyikan Password">
                                <i class="ti ti-eye-off"></i>
                            </button>

                            @error('old_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- PASSWORD BARU --}}
                    <div class="mb-4">
                        <label for="new_password" class="form-label fw-bold text-dark">Password Baru</label>
                        <div class="input-group input-group-lg">
                            <input type="password" name="new_password" id="new_password"
                                class="form-control rounded-start-3 @error('new_password') is-invalid @enderror" required
                                placeholder="Masukkan password baru">

                            <button type="button" class="btn btn-outline-secondary toggle-password rounded-end-3"
                                data-target="new_password" title="Tampilkan/Sembunyikan Password">
                                <i class="ti ti-eye-off"></i>
                            </button>

                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- KONFIRMASI --}}
                    <div class="mb-5">
                        <label for="new_password_confirmation" class="form-label fw-bold text-dark">Konfirmasi Password
                            Baru</label>
                        <div class="input-group input-group-lg">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                class="form-control rounded-start-3" required placeholder="Ulangi password baru">

                            <button type="button" class="btn btn-outline-secondary toggle-password rounded-end-3"
                                data-target="new_password_confirmation" title="Tampilkan/Sembunyikan Password">
                                <i class="ti ti-eye-off"></i>
                            </button>
                        </div>
                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                        <i class="ti ti-device-floppy me-2"></i> Simpan Password Baru
                    </button>

                </form>
            </div>
        </div>
    </div>

@endsection


@push('styles')
    <style>
        /* Styling Global Card */
        .card {
            border-radius: 1rem !important;
        }

        /* Styling Header */
        .card-header {
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
            border-bottom: 1px solid #e9ecef !important;
        }

        /* Styling Input Group Besar (input-group-lg) */
        .input-group-lg>.form-control,
        .input-group-lg>.form-select {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            font-size: 1rem;
            height: auto;
            border-right: 0 !important;
        }

        .input-group-lg>.btn {
            padding: 0.75rem 1rem;
            border-left: 1px solid #ced4da;
            border-color: #ced4da;
        }


        /* Styling Fokus Input */
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            border-color: #0d6efd !important;
        }

        /* Styling Tombol Toggle Password */
        .toggle-password {
            background-color: #f8f9fa;
            border-color: #ced4da;
            transition: background-color 0.2s;
        }

        .toggle-password:hover {
            background-color: #e9ecef;
        }

        /* --- PERUBAHAN UNTUK ICON MATA --- */
        .toggle-password i {
            color: #212529 !important;
            /* Warna hitam pekat */
        }

        /* ---------------------------------- */


        /* Penanganan Border saat Invalid */
        .is-invalid+.toggle-password {
            border-color: #dc3545 !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }

        .is-invalid:focus+.toggle-password {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
            z-index: 3;
        }

        /* Penyesuaian Rounded Corner agar input dan tombol menyatu sempurna */
        .input-group>.form-control:not(:last-child):not(.dropdown-toggle),
        .input-group-lg>.form-control:not(:last-child):not(.dropdown-toggle) {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .input-group>.btn:not(:first-child),
        .input-group-lg>.btn:not(:first-child) {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
    </style>
@endpush


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {

                    const input = document.getElementById(this.dataset.target);
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('ti-eye-off', 'ti-eye');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('ti-eye', 'ti-eye-off');
                    }
                });
            });
        });
    </script>
@endpush
