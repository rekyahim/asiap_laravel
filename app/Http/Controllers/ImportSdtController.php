<?php
namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Sdt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportSdtController extends Controller
{
    /* =====================================================
     *  FORM
     * ===================================================== */
    public function form()
    {
        return view('koor.import-sdt');
    }

    /* =====================================================
     *  STORE (IMPORT EXCEL → CREATE SDT + LOG)
     * ===================================================== */
    public function store(Request $r)
    {
        $r->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $user = Pengguna::find(session('auth_uid'));
        abort_if(! $user, 403);

        // ===================== BACA FILE =====================
        $sheets = Excel::toArray([], $r->file('file'));
        $rows   = $sheets[0] ?? [];

        if (count($rows) < 2) {
            return back()->with('ok', 'File kosong atau hanya berisi header.');
        }

        // ===================== HEADER =====================
        $header = array_map(
            fn($h) => strtolower(trim((string) $h)),
            $rows[0]
        );

        $map = array_flip($header);
        // contoh: ['nama_sdt' => 0, 'tgl_mulai' => 1, ...]

        // ===================== COUNTER =====================
        $inserted = 0;
        $skipped  = 0;
        $errors   = 0;

        // ===================== HELPER TANGGAL =====================
        $toDate = function ($val): ?string {
            if ($val === null || $val === '') {
                return null;
            }

            if (is_numeric($val)) {
                try {
                    return ExcelDate::excelToDateTimeObject($val)->format('Y-m-d');
                } catch (\Throwable) {}
            }

            try {
                return Carbon::parse((string) $val)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        };

        // ===================== TRANSAKSI =====================
        DB::beginTransaction();

        try {
            foreach (array_slice($rows, 1) as $i => $row) {

                $namaSdt = trim((string) ($row[$map['nama_sdt']] ?? ''));

                if ($namaSdt === '') {
                    $skipped++;
                    continue;
                }

                // ===================== CEK DUPLIKAT =====================
                $exists = Sdt::where('NAMA_SDT', $namaSdt)
                    ->where('STATUS', 1)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // ===================== CREATE SDT =====================
                $sdt = Sdt::create([
                    'NAMA_SDT'    => $namaSdt,
                    'TGL_MULAI'   => $toDate($row[$map['tgl_mulai']] ?? null),
                    'TGL_SELESAI' => $toDate($row[$map['tgl_selesai']] ?? null),
                    'KD_UNIT'     => $user->KD_UNIT,
                ]);

                // ===================== ACTIVITY LOG =====================
                activity('sdt')
                    ->event('created')
                    ->performedOn($sdt)
                    ->causedBy($user)
                    ->withProperties([
                        'sdt'    => [
                            'id'      => $sdt->ID,
                            'nama'    => $sdt->NAMA_SDT,
                            'mulai'   => $sdt->TGL_MULAI,
                            'selesai' => $sdt->TGL_SELESAI,
                            'kd_unit' => $sdt->KD_UNIT,
                        ],
                        'import' => [
                            'file' => $r->file('file')->getClientOriginalName(),
                            'row'  => $i + 2, // baris excel (header = 1)
                        ],
                    ])
                    ->log("SDT \"{$sdt->NAMA_SDT}\" dibuat melalui import Excel");

                $inserted++;
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return back()->with(
            'ok',
            "Import selesai. Berhasil: {$inserted}, dilewati: {$skipped}, error: {$errors}."
        );
    }
}
