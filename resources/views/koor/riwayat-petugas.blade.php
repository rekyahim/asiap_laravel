@extends('layouts.admin')

@section('title', 'Riwayat SDT Petugas')
@php($forceMenu = 'riwayat_petugas')

@section('breadcrumb', '')

@section('content')
    @push('styles')
        {{-- DataTables CSS --}}
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
        <style>
            .page-breadcrumb {
                margin: -.25rem 0 1rem 0
            }

            .crumbs {
                font-size: .9rem
            }

            .crumb {
                color: #6c757d;
                text-decoration: none;
            }

            .crumb.active {
                font-weight: 700;
                color: #212529;
                pointer-events: none;
            }

            /* Styling Table */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 1rem;
            }

            .aksi-btns .btn-icon {
                width: 36px;
                height: 36px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: .5rem;
            }

            .aksi-btns .btn-icon i {
                font-size: 1rem;
            }
        </style>
    @endpush

    <div class="row">
        <div class="page-breadcrumb">
            <div class="crumbs">
                <span class="crumb active">Riwayat SDT Petugas</span>
            </div>
        </div>

        <div class="row g-1">
            {{-- Filter Tahun --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold mb-3">Filter Tahun SDT</h5>
                        {{-- Form tidak perlu action/submit, cukup ID untuk JS --}}
                        <div class="mb-3">
                            <label for="filter-tahun" class="form-label">Tahun</label>
                            <select id="filter-tahun" class="form-select">
                                <option value="">— Semua Tahun —</option>
                                {{-- Loop tahun dari Controller (pastikan dikirim via compact) --}}
                                @foreach ($years as $y)
                                    <option value="{{ $y }}" @selected((string) request('year') === (string) $y)>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3 text-muted small">
                            Pilih tahun untuk menyaring daftar SDT.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Riwayat --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title fw-semibold mb-0">Riwayat SDT</h5>
                        </div>

                        <div class="table-responsive">
                            <table id="table-riwayat" class="table table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:5%">NO</th>
                                        <th>Nama SDT</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th>Total Data</th>
                                        <th style="width:120px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data diisi otomatis oleh DataTables --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Inisialisasi DataTable
            const table = $('#table-riwayat').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('riwayat.list-data') }}", // Panggil route baru
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        // Kirim parameter filter tahun ke server
                        d.year = $('#filter-tahun').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'NAMA_SDT',
                        name: 'NAMA_SDT'
                    },
                    {
                        data: 'TGL_MULAI',
                        name: 'TGL_MULAI'
                    },
                    {
                        data: 'TGL_SELESAI',
                        name: 'TGL_SELESAI'
                    },
                    {
                        data: 'details_count',
                        name: 'details_count',
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                }
            });

            // 2. Event Listener Filter Tahun
            $('#filter-tahun').on('change', function() {
                table.draw(); // Refresh tabel saat tahun berubah
            });
        });
    </script>
@endpush
