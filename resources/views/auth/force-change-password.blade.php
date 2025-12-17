<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ganti Password - ASIAP SDT</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }

        .login-card {
            max-width: 380px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            border: 1px solid #e9ecef;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.8rem 1rem;
            transition: all 0.2s ease-in-out;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .btn-primary {
            background-color: #2563eb;
            border: none;
            border-radius: 8px;
            padding: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
        }

        .input-group {
            position: relative;
        }

        #toggleNewPassword,
        #toggleConfirmPassword {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            z-index: 5;
            border-radius: 0 8px 8px 0;
            border-left: none;
            background: transparent;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <div class="text-center mb-4">
            <img src="{{ asset('assets/images/logos/ic_bapenda_nobg.png') }}" alt="Bapenda" width="140">
            <h4 class="mt-4 mb-1 fw-bold">Ganti Password Baru</h4>
            <p class="text-muted small mb-0">Anda harus mengganti password default sebelum melanjutkan.</p>
        </div>

        {{-- Notifikasi --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('first.change.password.update') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru</label>
                <div class="input-group">
                    <input type="password" name="new_password" id="new_password"
                           class="form-control"
                           required minlength="8"
                           pattern="^(?=.*[A-Za-z])(?=.*\d).+$"
                           placeholder="Masukkan password baru"
                           title="Minimal 8 karakter, harus ada huruf & angka.">
                    <button type="button" class="btn btn-outline-secondary" id="toggleNewPassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Konfirmasi Password</label>
                <div class="input-group">
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                           class="form-control"
                           required placeholder="Ulangi password baru">
                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 mt-2">
                Simpan Password Baru
            </button>
        </form>

        <p class="text-center small text-muted mt-4 mb-0">© {{ date('Y') }} Bapenda Kota Pekanbaru</p>
    </div>

    <script>
        function toggleVisibility(buttonId, inputId) {
            const button = document.querySelector(buttonId);
            const input = document.querySelector(inputId);
            const icon = button.querySelector('i');

            button.addEventListener('click', function () {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        }

        toggleVisibility('#toggleNewPassword', '#new_password');
        toggleVisibility('#toggleConfirmPassword', '#new_password_confirmation');
    </script>

</body>
</html>
