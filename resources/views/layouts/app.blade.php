<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>@yield('title','ASIAP SDT')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root { --sbw: 240px; }
    body { overflow-x: hidden; }
    .sidebar {
      position: fixed; inset: 0 auto 0 0; width: var(--sbw);
      background: #0043a6; color: #fff; padding: 1rem; z-index: 1030;
    }
    .sidebar a { color: #cfe2ff; text-decoration: none; }
    .sidebar .nav-link { border-radius: .5rem; padding: .5rem .75rem; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover { background: #084298; color: #fff; }
    .content-wrap { margin-left: var(--sbw); min-height: 100vh; }
    .topbar {
      position: sticky; top: 0; z-index: 1020; background: #fff; border-bottom: 1px solid #eee;
    }
    @media (max-width: 991.98px){
      .sidebar { left: -100%; transition: left .25s ease; }
      .sidebar.show { left: 0; }
      .content-wrap { margin-left: 0; }
    }
  </style>
</head>
<body>

  {{-- Sidebar --}}
  <aside id="sidebar" class="sidebar">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <a href="{{ url('/') }}" class="h5 mb-0 text-white text-decoration-none">ASIAP SDT</a>
      <button id="sidebarClose" class="btn btn-sm btn-light d-lg-none">✕</button>
    </div>

    <nav class="nav flex-column gap-1">
      <a class="nav-link {{ request()->routeIs('sdt.index') ? 'active' : '' }}"
         href="{{ route('sdt.index') }}">📋 Daftar SDT</a>

      <a class="nav-link {{ request()->routeIs('sdt.create') ? 'active' : '' }}"
         href="{{ route('sdt.create') }}">➕ Tambah SDT</a>

      <a class="nav-link {{ request()->routeIs('sdt.import.*') ? 'active' : '' }}"
         href="{{ route('sdt.import.form') }}">⬆️ Import SDT (Excel)</a>

      <a class="nav-link {{ request()->routeIs('riwayat.petugas') ? 'active' : '' }}"
         href="{{ route('riwayat.petugas') }}">🕘 Riwayat SDT per Petugas</a>
    </nav>
  </aside>

  {{-- Konten --}}
  <div class="content-wrap">
    <header class="topbar">
      <div class="container-fluid py-2 d-flex align-items-center gap-2">
        <button id="sidebarToggle" class="btn btn-outline-primary d-lg-none">☰ Menu</button>
        <div class="fw-semibold">@yield('breadcrumb','')</div>
      </div>
    </header>

    <main class="container-fluid p-3 p-sm-4">
      @yield('content')
    </main>
  </div>

  <script>
    const sb = document.getElementById('sidebar');
    document.getElementById('sidebarToggle')?.addEventListener('click', ()=> sb.classList.add('show'));
    document.getElementById('sidebarClose')?.addEventListener('click', ()=> sb.classList.remove('show'));
  </script>
  @stack('scripts')
</body>
</html>
