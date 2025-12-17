<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale-1">
    <title>Masuk - ASIAP SDT</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Menggunakan font yang lebih modern */
        body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem; /* Menambahkan padding agar kartu tidak menempel di tepi layar kecil */
        }

        /* Kartu login dengan desain yang lebih halus */
        .login-card {
            max-width: 380px; 
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            border: 1px solid #e9ecef;
        }

        /* Styling input field yang lebih baik */
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

        /* Desain tombol utama */
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
            transform: translateY(-2px); /* Efek sedikit terangkat saat di-hover */
        }
        
        /* Grup input untuk ikon password */
        .input-group {
            position: relative;
        }

        /* Penempatan ikon di dalam kolom password */
        #togglePassword {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            z-index: 5;
            border-radius: 0 8px 8px 0;
            border-left: none;
            background: transparent;
        }
        
        /* Styling untuk tautan Manual Book */
        .manual-book-link {
            color: #2563eb;
            transition: color 0.2s;
        }
        
        .manual-book-link:hover {
            color: #1d4ed8;
        }
        
        /* Styling tambahan untuk teks panduan */
        .panduan-teks {
            margin-top: 1.5rem; /* Memberi jarak dari tombol Masuk */
            margin-bottom: 0.5rem; /* Memberi jarak ke link di bawahnya */
            font-size: 0.85rem;
            color: #6c757d; /* Warna abu-abu yang soft */
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <img src="{{ asset('assets/images/logos/ic_bapenda_nobg.png') }}" alt="Bapenda" width="140">
            <h4 class="mt-4 mb-1 fw-bold">Selamat Datang!</h4>
            <p class="text-muted small mb-0">Masuk ke akun ASIAP Anda</p>
        </div>

        {{-- Notifikasi --}}
        @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if (session('warning')) <div class="alert alert-warning">{{ session('warning') }}</div> @endif
        @if (session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Username</label>
                <input type="text" name="USERNAME" id="username"
                        value="{{ old('USERNAME') }}"
                        class="form-control @error('USERNAME') is-invalid @enderror"
                        placeholder="Masukkan username" required autofocus>
                @error('USERNAME') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Masukkan password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Lihat/Sembunyikan Password">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
        </form>

        <p class="text-center panduan-teks">
            <i class="bi bi-info-circle-fill me-1 text-primary"></i> Panduan penggunaan aplikasi di bawah ini:
        </p>
        <div class="d-flex justify-content-center align-items-center mb-3 gap-3">
            
            <a href="{{ asset('assets/manuals/MANUAL.pdf') }}" 
               target="_blank" 
               class="text-decoration-none small fw-medium text-primary">
               <i class="bi bi-eye me-1"></i> Lihat Panduan
            </a>

            <span class="text-muted border-start border-2 h-100 mx-1"></span>

            <a href="{{ asset('assets/manuals/MANUAL.pdf') }}" 
               download="Panduan_Penggunaan_ASIAP.pdf" 
               class="text-decoration-none small fw-medium text-primary">
               <i class="bi bi-download me-1"></i> Download
            </a>

        </div>
        <p class="text-center small text-muted mt-4 mb-0">© {{ date('Y') }} Bapenda Kota Pekanbaru</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Skrip untuk fungsionalitas tombol lihat/sembunyikan password
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const toggleIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function () {
            // Ganti tipe input dari password ke text atau sebaliknya
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Ganti ikon mata
            toggleIcon.classList.toggle('bi-eye');
            toggleIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>