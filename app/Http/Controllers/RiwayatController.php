<?php
namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Sdt;
use Illuminate\Http\Request;

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
        $needFiltering = ! ($isSuperAdmin || $isAdmin || $isBapenda);

        // ================================
        // LIST TAHUN (urut ASC supaya rapi)
        // ================================
        $years = Sdt::withInactive()
            ->when($needFiltering, fn($q) => $q->where('KD_UNIT', $kdUnit))
            ->whereNotNull('TGL_MULAI')
            ->selectRaw('YEAR(TGL_MULAI) AS y')
            ->distinct()
            ->orderBy('y', 'asc')
            ->pluck('y');

        // ================================
        // LIST RIWAYAT SDT (ID kecil → besar)
        // ================================
        $rows = Sdt::query()
            ->when($needFiltering, fn($q) => $q->where('KD_UNIT', $kdUnit))
            ->when($year, fn($q) => $q->whereYear('TGL_MULAI', $year))
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
        $row = \App\Models\DtSdt::find($id);
        if (! $row) {
            return response()->json(['error' => true, 'message' => 'Data tidak ditemukan']);
        }

        $sp = \App\Models\StatusPenyampaian::where('ID_DT_SDT', $row->ID)
            ->orderBy('id', 'desc')
            ->first();

        // Safe status values
        $statusPenyampaian = $sp ? $sp->STATUS_PENYAMPAIAN : null;
        $statusOP          = $sp ? $sp->STATUS_OP : null;
        $statusWP          = $sp ? $sp->STATUS_WP : null;

        // Evidence
        $evidenceHtml = '<div class="text-muted small">Tidak ada evidence</div>';

        if ($sp && $sp->EVIDENCE) {

            $paths = preg_split('/[|,]+/', $sp->EVIDENCE);
            $imgs  = '';

            foreach ($paths as $p) {
                $p = trim($p);
                $p = str_replace('\\', '/', $p);

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
            'petugas'            => $row->PETUGAS_SDT,
            'nop'                => $row->NOP,
            'tahun'              => $row->TAHUN,

            'alamat_op'          => $row->ALAMAT_OP,
            'blok_kav_no_op'     => $row->BLOK_KAV_NO_OP,
            'rt_op'              => $row->RT_OP,
            'rw_op'              => $row->RW_OP,
            'kel_op'             => $row->KEL_OP,
            'kec_op'             => $row->KEC_OP,

            'nama_wp'            => $row->NAMA_WP,
            'alamat_wp'          => $row->ALAMAT_WP,
            'blok_kav_no_wp'     => $row->BLOK_KAV_NO_WP,
            'rt_wp'              => $row->RT_WP,
            'rw_wp'              => $row->RW_WP,
            'kel_wp'             => $row->KEL_WP,
            'kota_wp'            => $row->KOTA_WP,

            'jatuh_tempo'        => optional($row->JATUH_TEMPO)->format("Y-m-d"),
            'terhutang'          => number_format($row->TERHUTANG, 0, ',', '.'),
            'pengurangan'        => number_format($row->PENGURANGAN, 0, ',', '.'),
            'pbb_harus_dibayar'  => number_format($row->PBB_HARUS_DIBAYAR, 0, ',', '.'),

            'status_penyampaian' => $statusPenyampaian,
            'status_op'          => $statusOP,
            'status_wp'          => $statusWP,

            'nop_benar'          => $sp->NOP_BENAR ?? '-',
            'keterangan_petugas' => $sp->KETERANGAN_PETUGAS ?? '-',
            'tgl_penyampaian'    => $sp->TGL_PENYAMPAIAN ?? '-',
            'nama_penerima'      => $sp->NAMA_PENERIMA ?? '-',
            'hp_penerima'        => $sp->HP_PENERIMA ?? '-',
            'koordinat_op'       => $sp->KOORDINAT_OP ?? '-',

            'evidence_html'      => $evidenceHtml,
        ]);
    }
}
