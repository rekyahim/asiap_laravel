@extends('layouts.admin')

@section('title', 'Petugas / Update Massal NOP Komplek (KO)')
@section('breadcrumb', 'Petugas / Update Massal NOP Komplek')

@section('content')
<style>
  .page-title{font-weight:700;letter-spacing:.2px}
  .btn-brand{background:#4f46e5;border-color:#4f46e5;color:#fff}
  .btn-brand:hover{filter:brightness(.95)}
  .badge-ko{background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe}
</style>

<div class="card shadow-sm border-0">
  <div class="card-header bg-white d-flex align-items-center justify-content-between">
    <h5 class="mb-0 page-title">Update Massal NOP Komplek (KO)</h5>
    <a href="{{ route('petugas.sdt.index') }}" class="btn btn-outline-secondary">← Kembali</a>
  </div>

  <div class="card-body">
    @if (session('ok'))  <div class="alert alert-success">{{ session('ok') }}</div>  @endif
    @if (session('err')) <div class="alert alert-warning">{{ session('err') }}</div> @endif
    @if ($errors->any())
      <div class="alert alert-danger">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    {{-- Filter pencarian --}}
    <form class="row g-2 mb-3" method="GET" action="{{ route('petugas.sdt.massupdate.ko.form') }}">
      <div class="col-auto">
        <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Cari NOP / WP / Alamat">
      </div>
      <div class="col-auto">
        <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
      </div>
    </form>

    {{-- Form update massal (POST) --}}
    <form method="POST" action="{{ route('petugas.sdt.massupdate.ko.update') }}">
      @csrf

      <div class="row g-2 align-items-end mb-2">
        <div class="col-auto">
          <label class="form-label">Set STATUS menjadi</label>
          <select name="STATUS" class="form-select" required>
            <option value="" disabled selected>— pilih —</option>
            <option value="TERSAMPAIKAN">TERSAMPAIKAN</option>
            <option value="TIDAK TERSAMPAIKAN">TIDAK TERSAMPAIKAN</option>
          </select>
        </div>
        <div class="col-auto">
          <button class="btn btn-brand">
            <i class="bi bi-check2-circle me-1"></i> Update Terpilih
          </button>
        </div>
        <div class="col-auto">
          <div class="form-text">Checkbox hanya aktif untuk baris dengan Alamat OP diawali “KO”.</div>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:42px"><input type="checkbox" id="checkAll" aria-label="Pilih semua"></th>
              <th>ID</th>
              <th>NOP</th>
              <th>Alamat OP</th>
              <th>Nama WP</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($data as $d)
              @php
                $alamat = strtoupper((string) ($d->ALAMAT_OP ?? ''));
                $isKO   = substr($alamat, 0, 2) === 'KO';
              @endphp
              <tr class="{{ $isKO ? '' : 'table-light' }}">
                <td>
                  <input type="checkbox" class="rowcheck" name="selected[]" value="{{ $d->ID }}" {{ $isKO ? '' : 'disabled' }} aria-label="Pilih baris {{ $d->ID }}">
                </td>
                <td>{{ $d->ID }}</td>
                <td>{{ $d->NOP }}</td>
                <td>
                  {{ $d->ALAMAT_OP }}
                  @if ($isKO) <span class="badge badge-ko ms-2">KO</span> @endif
                </td>
                <td>{{ $d->NAMA_WP }}</td>
                <td>{{ $d->STATUS }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-5">Belum ada data KO yang ditampilkan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $data->withQueryString()->links() }}
    </form>
  </div>
</div>

<script>
  (function () {
    const all = document.getElementById('checkAll');
    if (!all) return;
    all.addEventListener('change', function (e) {
      document.querySelectorAll('.rowcheck').forEach(function (cb) {
        if (!cb.disabled) cb.checked = e.target.checked;
      });
    });
  })();
</script>
@endsection
