@extends('layouts.admin')

@section('title','Manajemen Modul')
@section('breadcrumb','Admin / Modul')

@section('content')
@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endonce

@push('styles')
<style>
  :root{ --brand:#5965e8; --brand-2:#7380ff; --border:#eef2f7; --aksi-offset:140px; }
  .shadow-soft{ box-shadow:0 .5rem 1.25rem rgba(18,26,41,.08)!important; }
  .card{ border:0; border-radius:18px; background:#fff; }
  .card-header{ border-bottom:1px solid var(--border); background:#fff; }
  .btn-brand{ background:var(--brand); border-color:var(--brand); color:#fff; }
  .btn-brand:hover{ background:var(--brand-2); border-color:var(--brand-2); color:#fff; }
  .btn-ghost{ background:#fff; border:1px solid rgba(18,26,41,.12); }
  .btn-ghost:hover{ background:#f8f9fe; }
  .pill{ border-radius:999px; }
  .table thead th{
    position:sticky; top:0; background:#fff; z-index:5;
    box-shadow:inset 0 -1px 0 var(--border);
    text-transform:uppercase; letter-spacing:.5px; font-size:.8rem; color:#5d6d8a;
  }
  th.col-date,td.col-date{ width:200px; }
  th.col-aksi{ padding-left:var(--aksi-offset); }
  .aksi-cell{ display:inline-flex; gap:.5rem; flex-wrap:wrap; align-items:center; }
</style>
@endpush

@php
  $tz     = 'Asia/Jakarta';
  $total  = method_exists($moduls,'total') ? $moduls->total() : $moduls->count();
  $nowTz  = \Carbon\Carbon::now($tz)->format('Y-m-d\TH:i');
@endphp

<div class="container-fluid">
  <div class="card shadow-soft">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <h2 class="h6 mb-0">Manajemen Modul</h2>
        <span class="badge rounded-pill text-bg-light">{{ $total }}</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-brand btn-sm pill" data-bs-toggle="modal" data-bs-target="#modalCreate">
          <i class="bi bi-plus-circle me-1"></i>Tambah Modul
        </button>
      </div>
    </div>

    <div class="card-body">
      @if (session('success')) 
        <div class="alert alert-success py-2">
          <i class="bi bi-check2-circle me-1"></i>{{ session('success') }}
        </div>
      @endif
      @if (session('error'))   
        <div class="alert alert-danger py-2">
          <i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}
        </div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          <div class="d-flex align-items-center mb-2 fw-semibold">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
            <span>Validasi gagal! Mohon periksa kembali isian Anda.</span>
          </div>
          <ul class="mb-0 ps-4">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
      @endif

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <h5 class="mb-2 mb-md-0 fw-semibold">Daftar Modul</h5>

        <div class="d-flex gap-2">
          <form method="GET" action="{{ route('modul.index') }}" class="input-group" style="max-width:360px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                   placeholder="Cari nama atau lokasi…">
            <input type="hidden" name="show" value="{{ request('show') }}">
            <button class="btn btn-ghost" type="submit">Cari</button>
          </form>

          <a class="btn btn-ghost"
             href="{{ route('modul.index', array_filter(['q'=>request('q'),'show'=> request('show') ? null : 1])) }}">
            {{ request('show') ? 'Sembunyikan Nonaktif' : 'Tampilkan Nonaktif' }}
          </a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th style="width:72px;">ID</th>
              <th>Nama Modul</th>
              <th>Lokasi</th>
              <th class="col-date">Tgl Post</th>
              <th class="col-aksi text-start">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($moduls as $m)
              <tr>
                <td class="fw-semibold text-muted">{{ $m->id }}</td>
                <td class="fw-semibold">{{ $m->nama_modul }}</td>
                <td><code>{{ $m->lokasi_modul }}</code></td>
                <td class="small text-muted">
                  {{ $m->tglpost?->setTimezone($tz)->format('Y-m-d H:i') }} WIB
                </td>
                <td class="text-start">
                  <div class="aksi-cell">
                    {{-- EDIT --}}
                    <button class="btn btn-ghost btn-sm"
                            data-bs-toggle="modal" data-bs-target="#modalEdit"
                            data-id="{{ $m->id }}"
                            data-nama="{{ $m->nama_modul }}"
                            data-lokasi="{{ $m->lokasi_modul }}"
                            data-tgl="{{ $m->tglpost?->setTimezone($tz)->format('Y-m-d\TH:i') }}">
                      <i class="bi bi-pencil-square me-1"></i>Edit
                    </button>

                    {{-- HAPUS --}}
                    <form action="{{ route('modul.destroy', $m->id) }}" method="POST"
                          onsubmit="return confirm('Nonaktifkan modul ini? Modul akan disembunyikan dari daftar.');">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-ghost btn-sm text-danger">
                        <i class="bi bi-trash3 me-1"></i>Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data modul.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if (method_exists($moduls,'hasPages') && $moduls->hasPages())
        <div class="mt-3 d-flex justify-content-center">{{ $moduls->onEachSide(1)->links() }}</div>
      @endif
    </div>
  </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="POST" action="{{ route('modul.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Modul</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Nama Modul</label>
          <input name="nama_modul" class="form-control" required maxlength="100" value="{{ old('nama_modul') }}">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Lokasi Modul (slug/route)</label>
          <input name="lokasi_modul" class="form-control" required maxlength="150" value="{{ old('lokasi_modul') }}">
          <div class="form-text">Contoh: <code>kelola_pengguna</code> / <code>manage_sdt</code></div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Tanggal Posting (WIB)</label>
          <input type="datetime-local" name="tglpost" class="form-control" required value="{{ old('tglpost', $nowTz) }}">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Tutup</button>
        <button class="btn btn-brand rounded-1 px-4">
          <i class="bi bi-check-circle me-1"></i>Simpan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formEditModul" class="modal-content" method="POST">
      @csrf @method('PATCH')
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Modul</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Nama Modul</label>
          <input id="editNama" name="nama_modul" class="form-control" required maxlength="100">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Lokasi Modul (slug/route)</label>
          <input id="editLokasi" name="lokasi_modul" class="form-control" required maxlength="150">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Tanggal Posting (WIB)</label>
          <input id="editTgl" type="datetime-local" name="tglpost" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-brand rounded-1 px-4">
          <i class="bi bi-check-circle me-1"></i>Simpan
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  const modal = document.getElementById('modalEdit');
  modal.addEventListener('show.bs.modal', (ev) => {
    const btn   = ev.relatedTarget;
    const id    = btn.getAttribute('data-id');
    const nama  = btn.getAttribute('data-nama') || '';
    const lokasi= btn.getAttribute('data-lokasi') || '';
    const tgl   = btn.getAttribute('data-tgl') || '';

    const form  = document.getElementById('formEditModul');
    form.action = "{{ url('admin/modul') }}/" + id;

    document.getElementById('editNama').value   = nama;
    document.getElementById('editLokasi').value = lokasi;
    document.getElementById('editTgl').value    = tgl;
  });
</script>
@endpush
@endsection
