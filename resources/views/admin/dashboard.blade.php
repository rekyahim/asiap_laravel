{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.admin') {{-- pakai layout yang sudah ada di proyek kamu --}}

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
  @once
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @endonce

  @push('styles')
  <style>
    :root{
      --brand:#5965e8; --brand-2:#7380ff; --border:#eef2f7;
    }
    .shadow-soft{ box-shadow:0 .5rem 1.25rem rgba(18,26,41,.08)!important; }
    .card{ border:0; border-radius:18px; background:#fff; }
    .card-header{ border-bottom:1px solid var(--border); background:#fff; }
    .stat-card .icon{
      width:46px;height:46px;display:flex;align-items:center;justify-content:center;border-radius:12px;background:#f3f5ff;color:#5965e8;
    }
    .shortcut a{ text-decoration:none; }
    .shortcut .item{ border:1px solid var(--border); border-radius:14px; padding:12px 14px; transition:.15s; }
    .shortcut .item:hover{ transform:translateY(-2px); box-shadow:0 .5rem 1.25rem rgba(18,26,41,.08); }
    code.slug{ background:#f8f9fe; color:#6a6f86; padding:.1rem .3rem; border-radius:6px; }
  </style>
  @endpush

  @php
    $user = auth()->user();
    // mapping slug->route (index) — samakan dengan setup kamu
    $routeMap = [
      'kelola_modul'      => 'kelola_modul',
      'kelola_hakakses'   => 'kelola_hakakses',
      'kelola_aksesmodul' => 'kelola_aksesmodul', // perlu parameter ID saat klik pertama → kita arahkan ke default
      'kelola_pengguna'   => 'kelola_pengguna',
      'riwayat_petugas'   => 'riwayat_petugas',
      'kelola_sdt'        => 'kelola_sdt',
    ];

    // untuk kelola_aksesmodul kita butuh ID hak akses aktif pertama
    $firstHakId = \App\Models\HakAkses::where('STATUS',1)->orderBy('ID')->value('ID');
  @endphp

  <div class="container-fluid">
    <div class="card shadow-soft mb-4">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h2 class="h5 mb-1">Selamat datang{{ $user ? ', '.$user->NAMA : '' }}!</h2>
          <div class="text-muted">Ini adalah ringkasan singkat aplikasi.</div>
        </div>
        <div class="d-none d-md-block">
          <img src="{{ asset('assets/images/illustrations/dashboard.svg') }}" alt="" height="64" onerror="this.remove()">
        </div>
      </div>
    </div>

    {{-- STATISTIK --}}
    <div class="row g-3">
      <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-soft">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="icon"><i class="bi bi-puzzle"></i></div>
            <div>
              <div class="text-muted small">Modul Aktif</div>
              <div class="h5 mb-0">{{ $stats['modul_aktif'] ?? 0 }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-soft">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="icon"><i class="bi bi-shield-lock"></i></div>
            <div>
              <div class="text-muted small">Hak Akses Aktif</div>
              <div class="h5 mb-0">{{ $stats['hakakses_aktif'] ?? 0 }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-soft">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="icon"><i class="bi bi-people"></i></div>
            <div>
              <div class="text-muted small">Pengguna Aktif</div>
              <div class="h5 mb-0">{{ $stats['pengguna_aktif'] ?? 0 }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card stat-card shadow-soft">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="icon"><i class="bi bi-link-45deg"></i></div>
            <div>
              <div class="text-muted small">Mapping Aktif</div>
              <div class="h5 mb-0">{{ $stats['mapping_aktif'] ?? 0 }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- SHORTCUT MODUL (hanya jika login) --}}
    <div class="card shadow-soft mt-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Shortcut Modul</h6>
        @if(!$user)
          <span class="small text-muted">Login untuk melihat akses modul Anda.</span>
        @endif
      </div>
      <div class="card-body">
        @if($user && count($shortcuts))
          <div class="row g-3 shortcut">
            @foreach($shortcuts as $s)
              @php
                $slug = $s['slug'];
                // default link
                $href = '#';
                if ($slug === 'kelola_aksesmodul') {
                  // butuh ID → arahkan ke default
                  $href = $firstHakId ? route('kelola_aksesmodul', $firstHakId) : route('kelola_hakakses');
                } elseif(isset($routeMap[$slug])) {
                  $href = route($routeMap[$slug]);
                }
              @endphp
              <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <a href="{{ $href }}">
                  <div class="item d-flex align-items-center justify-content-between">
                    <div class="me-3">
                      <div class="fw-semibold">{{ $s['label'] }}</div>
                      <div class="small text-muted">route: <code class="slug">{{ $slug }}</code></div>
                    </div>
                    <i class="bi bi-arrow-right-circle fs-5 text-primary"></i>
                  </div>
                </a>
              </div>
            @endforeach
          </div>
        @elseif($user)
          <div class="text-muted">Hak akses Anda belum memiliki modul aktif.</div>
        @else
          <div class="text-muted">Silakan login untuk melihat modul yang tersedia.</div>
        @endif
      </div>
    </div>
  </div>
@endsection