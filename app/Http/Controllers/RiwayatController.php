<?php

namespace App\Http\Controllers;

use App\Models\Sdt;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RiwayatController extends Controller
{
    public function petugas(Request $r)
    {
        $user = Pengguna::find(session('auth_uid'));

        $kdUnit = $user->KD_UNIT;
        $year   = $r->input('year');

        // Role based access
        $isSuperAdmin = ($user->HAKAKSES_ID == 35);
        $isAdmin      = ($user->HAKAKSES_ID == 2);
        $isBapenda    = ($kdUnit == 1);

        // Hanya koordinator (1) yang dibatasi
        $needFiltering = !($isSuperAdmin || $isAdmin || $isBapenda);

        // ================================
        // LIST TAHUN (urut ASC supaya rapi)
        // ================================
        $years = Sdt::withInactive()
            ->when($needFiltering, fn ($q) => $q->where('KD_UNIT', $kdUnit))
            ->whereNotNull('TGL_MULAI')
            ->selectRaw('YEAR(TGL_MULAI) AS y')
            ->distinct()
            ->orderBy('y', 'asc')
            ->pluck('y');

        // ================================
        // LIST RIWAYAT SDT (ID kecil → besar)
        // ================================
        $rows = Sdt::query()
            ->when($needFiltering, fn ($q) => $q->where('KD_UNIT', $kdUnit))
            ->when($year, fn ($q) => $q->whereYear('TGL_MULAI', $year))
            ->withCount('details')
            ->orderBy('ID', 'asc')
            ->paginate(10, ['ID', 'NAMA_SDT', 'TGL_MULAI', 'TGL_SELESAI', 'STATUS']);

        return view('koor.riwayat-petugas', compact('years', 'year', 'rows'));
    }
    public function exportSdt($sdtId)
    {
        $sdt = \App\Models\Sdt::findOrFail($sdtId);

        // Nama file: Laporan_SDT_<NamaSDT>_<Tahun>.xlsx
        $safeNama = preg_replace('/[^A-Za-z0-9_\-]/', '_', $sdt->NAMA_SDT);
        $tahun    = \Carbon\Carbon::parse($sdt->TGL_MULAI)->format('Y');

        $filename = "Laporan_SDT_{$safeNama}_{$tahun}.xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SdtExport($sdtId),
            $filename
        );
    }
    public function detailRow($id)
    {

        $row = \App\Models\DtSdt::query()
            ->Join('pengguna', 'pengguna.ID', '=', 'dt_sdt.PETUGAS_SDT')
            ->where('dt_sdt.ID', $id)
            ->select([
                'dt_sdt.*',
                \DB::raw('pengguna.NAMA AS petugas_nama'),
            ])
            ->first();

        if (!$row) {
            return response()->json([
                'error'   => true,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $sp = \App\Models\StatusPenyampaian::where('ID_DT_SDT', $row->ID)
            ->orderBy('id', 'desc')
            ->first();

        // Status aman
        $statusPenyampaian = $sp?->STATUS_PENYAMPAIAN;
        $statusOP          = $sp?->STATUS_OP;
        $statusWP          = $sp?->STATUS_WP;

        // Evidence
        $evidenceHtml = '<div class="text-muted small">Tidak ada evidence</div>';

        if ($sp && $sp->EVIDENCE) {
            $paths = preg_split('/[|,]+/', $sp->EVIDENCE);
            $imgs  = '';

            foreach ($paths as $p) {
                $p = trim(str_replace('\\', '/', $p));

                if ($p !== '' && \Storage::disk('public')->exists($p)) {
                    $imgs .= '<img src="' . asset("storage/$p") . '"
                    class="img-fluid rounded mb-2"
                    style="max-width:150px;"> ';
                }
            }

            if ($imgs !== '') {
                $evidenceHtml = $imgs;
            }
        }

        return response()->json([
            'id'                 => $row->ID,
            'id_sdt'             => $row->ID_SDT,

            // 🔥 INI YANG PENTING
            'petugas'            => $row->petugas_nama ?? '-',

            'nop'                => $row->NOP,
            'tahun'              => $row->TAHUN,

            // OP
            'alamat_op'          => $row->ALAMAT_OP,
            'blok_kav_no_op'     => $row->BLOK_KAV_NO_OP,
            'rt_op'              => $row->RT_OP,
            'rw_op'              => $row->RW_OP,
            'kel_op'             => $row->KEL_OP,
            'kec_op'             => $row->KEC_OP,

            // WP
            'nama_wp'            => $row->NAMA_WP,
            'alamat_wp'          => $row->ALAMAT_WP,
            'blok_kav_no_wp'     => $row->BLOK_KAV_NO_WP,
            'rt_wp'              => $row->RT_WP,
            'rw_wp'              => $row->RW_WP,
            'kel_wp'             => $row->KEL_WP,
            'kota_wp'            => $row->KOTA_WP,

            // PBB
            'jatuh_tempo'        => optional($row->JATUH_TEMPO)->format('Y-m-d'),
            'terhutang'          => number_format((int) $row->TERHUTANG, 0, ',', '.'),
            'pengurangan'        => number_format((int) $row->PENGURANGAN, 0, ',', '.'),
            'pbb_harus_dibayar'  => number_format((int) $row->PBB_HARUS_DIBAYAR, 0, ',', '.'),

            // Status
            'status_penyampaian' => $statusPenyampaian,
            'status_op'          => $statusOP,
            'status_wp'          => $statusWP,

            // Tambahan
            'nop_benar'          => $sp?->NOP_BENAR ?? '-',
            'keterangan_petugas' => $sp?->KETERANGAN_PETUGAS ?? '-',
            'tgl_penyampaian'    => $sp?->TGL_PENYAMPAIAN ?? '-',
            'nama_penerima'      => $sp?->NAMA_PENERIMA ?? '-',
            'hp_penerima'        => $sp?->HP_PENERIMA ?? '-',
            'koordinat_op'       => $sp?->KOORDINAT_OP ?? '-',

            'evidence_html'      => $evidenceHtml,
        ]);
    }
    public function riwayatData(Request $request)
    {
        if ($request->ajax()) {
            $user = \App\Models\Pengguna::find(session('auth_uid'));
            $kdUnit = $user->KD_UNIT ?? null;

            // Query Dasar
            $query = Sdt::query()
                ->select(['sdt.*'])
                ->withCount('details');

            // Filter Unit (Sama seperti index)
            if ($kdUnit !== null && $kdUnit != 1) {
                $query->where('KD_UNIT', $kdUnit);
            }

            // Filter Tahun (Dari Dropdown)
            if ($request->has('year') && $request->year != '') {
                $query->whereYear('TGL_MULAI', $request->year);
            }

            return DataTables::of($query)
                ->addIndexColumn() // DT_RowIndex
                ->editColumn('TGL_MULAI', function ($row) {
                    return $row->TGL_MULAI ? $row->TGL_MULAI->format('Y-m-d') : '-';
                })
                ->editColumn('TGL_SELESAI', function ($row) {
                    return $row->TGL_SELESAI ? $row->TGL_SELESAI->format('Y-m-d') : '-';
                })
                ->addColumn('action', function ($row) {
                    // Tombol Aksi: Lihat Detail & Export
                    $urlShow   = route('sdt.show', $row->ID);
                    $urlExport = route('sdt.export', $row->ID); // Pastikan route ini ada

                    $btnShow = '<a href="' . $urlShow . '" class="btn btn-secondary btn-icon" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>';

                    $btnExport = '<a href="' . $urlExport . '" class="btn btn-success btn-icon" title="Export SDT">
                                <i class="bi bi-file-earmark-excel"></i>
                              </a>';

                    return '<div class="aksi-btns d-flex align-items-center gap-2">' .
                        $btnShow . $btnExport .
                        '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        abort(404);
    }
}
