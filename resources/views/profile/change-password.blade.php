@extends('layouts.admin')

@section('title', 'Ganti Password')

@section('content')

<div class="container-fluid px-2 px-md-4">
    <div class="card border-0 shadow-sm rounded-4">

        {{-- HEADER --}}
        <div class="card-header bg-white border-0 py-3 px-3">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-3"
                    title="Kembali">
                    <i class="ti ti-arrow-left"></i>
                </a>
                <h6 class="fw-bold mb-0">Ganti Password</h6>
            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body px-3 py-3">

            {{-- SUCCESS --}}
            @if (session('success'))
                <div class="alert alert-success py-2 small rounded-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ERROR --}}
            @if (session('error'))
                <div class="alert alert-danger py-2 small rounded-3">
                    {{ session('error') }}
                </div>
            @endif

            {{-- VALIDATION --}}
            @if ($errors->any())
                <div class="alert alert-warning py-2 small rounded-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM --}}
            <form action="{{ route('profile.change.password.update') }}" method="POST">
                @csrf

                {{-- PASSWORD LAMA --}}
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Lama</label>
                    <div class="input-group">
                        <input type="password" name="old_password" id="old_password"
                            class="form-control form-control-sm @error('old_password') is-invalid @enderror"
                            required>
                        <button type="button" class="btn btn-outline-secondary btn-sm toggle-password"
                            data-target="old_password">
                            <i class="ti ti-eye-off"></i>
                        </button>
                        @error('old_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- PASSWORD BARU --}}
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="new_password" id="new_password"
                            class="form-control form-control-sm @error('new_password') is-invalid @enderror"
                            required>
                        <button type="button" class="btn btn-outline-secondary btn-sm toggle-password"
                            data-target="new_password">
                            <i class="ti ti-eye-off"></i>
                        </button>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- KONFIRMASI --}}
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="new_password_confirmation"
                            id="new_password_confirmation"
                            class="form-control form-control-sm"
                            required>
                        <button type="button" class="btn btn-outline-secondary btn-sm toggle-password"
                            data-target="new_password_confirmation">
                            <i class="ti ti-eye-off"></i>
                        </button>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <button type="submit"
                    class="btn btn-primary btn-sm w-100 fw-semibold rounded-3">
                    Simpan Password
                </button>

            </form>
        </div>
    </div>
</div>

@endsection


@push('styles')
<style>
    .toggle-password i {
        color: #212529 !important;
    }

    @media (max-width: 576px) {
        .card {
            border-radius: 1rem;
        }

        .form-label {
            margin-bottom: .25rem;
        }

        .alert {
            font-size: .75rem;
        }

        button[type="submit"] {
            padding: .6rem;
        }
    }
</style>
@endpush


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function () {
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
