@extends('layouts.admin')

@section('title', 'Import SDT')
@section('breadcrumb', 'Koordinator / Import Excel Tambah SDT')

@section('content')
    <div class="row">
        {{-- Form upload --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-semibold mb-0">Import SDT (Excel)</h5>
                        <a href="{{ route('sdt.import.form') }}" class="btn btn-light">
                            <i class="ti ti-refresh me-1"></i>Reset
                        </a>
                    </div>

                    @if (session('ok'))
                        <div class="alert alert-success">{{ session('ok') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <div class="fw-semibold mb-2">Upload gagal:</div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" enctype="multipart/form-data" action="{{ route('sdt.import.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" name="file" id="file" class="form-control" required
                                accept=".xlsx,.xls,.csv">
                            <div class="form-text">Maks 10MB. Tipe: .xlsx, .xls, atau .csv.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-upload me-1"></i>Import
                            </button>
                            <a href="{{ route('sdt.import.form') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-circle-x me-1"></i>Bersihkan
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Contoh format --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">Format Header (baris pertama)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-2">
                            <thead class="table-light">
                                <tr>
                                    <th>nama_sdt</th>
                                    <th>tgl_mulai</th>
                                    <th>tgl_selesai</th>
                                    <th>petugas_email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SDT 2025 Gelombang 1</td>
                                    <td>2025-08-01</td>
                                    <td>2025-08-31</td>
                                    <td>petugas@example.com</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0">
                        Tanggal boleh format Excel (angka) atau teks <code>YYYY-MM-DD</code>.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
