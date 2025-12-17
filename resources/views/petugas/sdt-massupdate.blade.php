@extends('layouts.admin')

@section('title', 'Petugas / Update Massal NOP Komplek (KO)')
@section('breadcrumb', 'Petugas / Update Massal NOP Komplek')

@section('content')
<style>
  :root{
    --bg:#f5f7fb; --card:#ffffff; --line:#e6e8ec; --text:#0f172a; --muted:#64748b;
    --accent:#2563eb; --accent-2:#1d4ed8;
  }
  .page-wrap{
    background:
      radial-gradient(1200px 600px at -10% -10%, #eef2ff 0%, transparent 55%),
      radial-gradient(900px 320px at 120% -5%, #e8f1ff 0%, transparent 60%),
      var(--bg);
    border-radius: 18px;
    padding: 20px;
  }
  .card-keren{background:var(--card);border:1px solid var(--line);border-radius:18px;box-shadow:0 16px 36px rgba(2,6,23,.10);overflow:hidden}
  .card-keren .card-header{border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fff,#f8fafc);padding:16px 20px}
  .page-title{margin:0;font-weight:800;letter-spacing:.2px;color:var(--text)}
  .btn-quiet{border:1px solid var(--line);background:#fff;font-weight:700;border-radius:12px}
  .btn-quiet:hover{background:#f8fafc}
  .btn-brand{background:linear-gradient(135deg,var(--accent),var(--accent-2));border:1px solid var(--accent-2);color:#fff;font-weight:800;border-radius:12px;padding:.48rem .86rem;box-shadow:0 12px 24px rgba(37,99,235,.22)}
  .toolbar{display:flex;flex-wrap:wrap;gap:.6rem;align-items:center}
  .table-wrap{border:1px solid var(--line);border-radius:16px;overflow:hidden;background:#fff}
  table.table-keren{width:100%;border-collapse:separate;border-spacing:0}
  .table-keren thead th{position:sticky;top:0;z-index:1;background:linear-gradient(180deg,#f8fafc,#eef2ff);color:var(--text);font-weight:800;letter-spacing:.2px;padding:11px 14px;border-bottom:1px solid var(--line);white-space:nowrap}
  .table-keren tbody td{padding:13px 14px;border-bottom:1px solid var(--line);vertical-align:middle;color:#334155}
  .table-keren tbody tr:nth-child(odd){background:#fcfdff}
  .table-keren tbody tr:hover{background:#f6f8ff}
  .td-no{width:48px;text-align:center}
  .code{font-family:ui-monospace,Menlo,monospace;letter-spacing:.2px}
  .badge-ko{background:#eef2ff;color:#3730a3;border:1px solid #c7d2fe;border-radius:999px;font-weight:700;padding:.12rem .5rem;font-size:.75rem}
  .row-disabled{opacity:.55}
  .pagination .page-link{border-radius:10px;border-color:var(--line)}
  .pagination .active .page-link{background:var(--accent);border-color:var(--accent);color:#fff}
  .form-control:focus,.form-select:focus,.btn:focus,input[type="checkbox"]:focus{box-shadow:0 0 0 .2rem rgba(37,99,235,.15);outline:none}
</style>

<div class="page-wrap">
  <div class="card-keren">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h5 class="page-title">Update Massal NOP Komplek (KO)</h5>
      <a href="{{ route('petugas.sdt.index') }}" class="btn btn-quiet">← Kembali</a>
    </div>

    <div class="card-body">
      {{-- Alerts --}}
      @if (session('ok'))  <div class="alert alert-success">{{ session('ok') }}</div>  @endif
      @if (session('err')) <div class="alert alert-warning">{{ session('err') }}</div> @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
      @endif

      {{-- Toolbar (search + actions) --}}
      <div class="toolbar mb-3">
        <form class="d-flex gap-2 flex-wrap" method="GET" action="{{ route('petugas.sdt.massupdate.ko.form') }}">
          <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Cari NOP / WP / Alamat" style="min-width:260px">
          <button class="btn btn-quiet" type="submit"><i class="bi bi-search me-1"></i>Cari</button>
        </form>

        <div class="ms-auto d-flex gap-2 flex-wrap">
          <button type="button" id="btnSelectVisible" class="btn btn-quiet">
            <i class="bi bi-check2-square me-1"></i>Pilih yang terlihat
          </button>
          <button type="button" id="btnClear" class="btn btn-quiet">
            <i class="bi bi-x-circle me-1"></i>Bersihkan
          </button>
          <button type="button" class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#massModal">
            <i class="bi bi-check2-circle me-1"></i> Update Terpilih
          </button>
        </div>
      </div>

      {{-- Table --}}
      <div class="table-wrap">
        <div class="table-responsive">
          <table class="table-keren" id="tblKO">
            <thead>
              <tr>
                <th style="width:46px"><input type="checkbox" id="checkAll" aria-label="Pilih semua"></th>
                <th style="min-width:80px">ID</th>
                <th style="min-width:140px">NOP</th>
                <th>Alamat OP</th>
                <th style="min-width:160px">Nama WP</th>
                <th style="min-width:120px">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($data as $d)
                @php
                  $alamat = strtoupper((string) ($d->ALAMAT_OP ?? ''));
                  $isKO   = substr($alamat, 0, 2) === 'KO';
                @endphp
                <tr class="{{ $isKO ? '' : 'row-disabled' }}">
                  <td class="td-no">
                    <input type="checkbox" class="rowcheck" value="{{ $d->ID }}" {{ $isKO ? '' : 'disabled' }} aria-label="Pilih baris {{ $d->ID }}">
                  </td>
                  <td class="code">{{ $d->ID }}</td>
                  <td class="code">{{ $d->NOP }}</td>
                  <td>
                    {{ $d->ALAMAT_OP }}
                    @if ($isKO) <span class="badge-ko ms-2">KO</span> @endif
                  </td>
                  <td>{{ $d->NAMA_WP }}</td>
                  <td>{{ $d->STATUS }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted py-5">Belum ada data KO yang ditampilkan.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-3">{{ $data->withQueryString()->links() }}</div>
    </div>
  </div>
</div>

{{-- Modal mengambang: Update Massal --}}
<div class="modal fade" id="massModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content" style="border:1px solid var(--line);border-radius:14px">
      <div class="modal-header">
        <h5 class="modal-title">Update Status (Massal)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info py-2 mb-3">Hanya baris dengan alamat diawali <b>KO</b> yang bisa dipilih.</div>
        <div class="mb-2">
          <label class="form-label">Set status menjadi</label>
          <select id="massStatus" class="form-select" aria-label="Pilih status baru">
            <option value="" selected disabled>— pilih —</option>
            <option value="TERSAMPAIKAN">TERSAMPAIKAN</option>
            <option value="TIDAK TERSAMPAIKAN">TIDAK TERSAMPAIKAN</option>
          </select>
          <div class="form-text mt-1">Penerapan status hanya pada baris terpilih.</div>
        </div>
        <div class="form-text">Akan diterapkan ke <b><span id="selCount">0</span> baris terpilih</b>.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="btnDoMass" class="btn btn-brand">
          <i class="bi bi-save me-1"></i> Simpan
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Hidden form untuk submit massal (POST) --}}
<form id="massForm" class="d-none" method="POST" action="{{ route('petugas.sdt.massupdate.ko.update') }}">
  @csrf
  <input type="hidden" name="STATUS" id="massHiddenStatus">
  <div id="massIds"></div>
</form>

<script>
  const $$   = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));
  const byId = (id) => document.getElementById(id);

  const master = byId('checkAll');

  function refreshSelectedCount(){
    const all = $$('.rowcheck:not(:disabled)');
    const sel = $$('.rowcheck:checked');
    byId('selCount').textContent = sel.length;
    if(!master) return;
    master.indeterminate = sel.length > 0 && sel.length < all.length;
    master.checked = sel.length === all.length && all.length > 0;
  }

  master?.addEventListener('change', e => {
    $$('.rowcheck').forEach(cb => { if (!cb.disabled) cb.checked = e.target.checked; });
    refreshSelectedCount();
  });

  document.querySelector('#tblKO tbody')?.addEventListener('click', (ev) => {
    const tr = ev.target.closest('tr'); if (!tr) return;
    if (ev.target.closest('input, a, button, label, select')) return;
    const cb = tr.querySelector('.rowcheck'); if (!cb || cb.disabled) return;
    cb.checked = !cb.checked;
    refreshSelectedCount();
  });

  byId('btnSelectVisible')?.addEventListener('click', () => {
    $$('.rowcheck').forEach(cb => { if (!cb.disabled) cb.checked = true; });
    refreshSelectedCount();
  });

  byId('btnClear')?.addEventListener('click', () => {
    $$('.rowcheck').forEach(cb => cb.checked = false);
    refreshSelectedCount();
  });

  document.addEventListener('change', e => {
    if (e.target.classList?.contains('rowcheck')) refreshSelectedCount();
  });
  document.getElementById('massModal')?.addEventListener('show.bs.modal', refreshSelectedCount);

  byId('btnDoMass')?.addEventListener('click', () => {
    const status = byId('massStatus').value;
    const picked = $$('.rowcheck:checked').map(cb => cb.value);

    if (!status) { alert('Pilih status terlebih dahulu.'); return; }
    if (picked.length === 0) { alert('Belum ada baris yang dipilih.'); return; }

    byId('massHiddenStatus').value = status;
    const box = byId('massIds');
    box.innerHTML = '';
    picked.forEach(id => {
      const i = document.createElement('input');
      i.type = 'hidden'; i.name = 'selected[]'; i.value = id;
      box.appendChild(i);
    });

    byId('massForm').submit();
  });

  refreshSelectedCount();
</script>
@endsection
