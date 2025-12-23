<?php
namespace App\Imports;

use App\Models\DtSdt;
use App\Models\Pengguna;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DtSdtImport implements ToCollection, WithHeadingRow
{
    private int $sdtId;
    private ?int $kdUnit; // <--- TAMBAHAN

    public function __construct(int $sdtId)
    {
        $this->sdtId = $sdtId;

        // ============================
        // AMBIL KD_UNIT DARI TABEL SDT
        // ============================
        $this->kdUnit = DB::table('sdt')
            ->where('ID', $sdtId)
            ->value('KD_UNIT');
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows)
    {
        // ============================
        //  VALIDASI NAMA PETUGAS (FK)
        // ============================
        $excelPetugas      = [];
        $excelPetugasLines = [];

        foreach ($rows as $idx => $row) {
            $line = $idx + 2;

            $nm = $row['nama_petugas'] ?? $row['petugas'] ?? $row['petugas_sdt'] ?? null;

            if ($nm !== null && trim($nm) !== '') {
                $nmLower                     = mb_strtolower(trim((string) $nm), 'UTF-8');
                $excelPetugas[]              = $nmLower;
                $excelPetugasLines[$nmLower] = $line;
            }
        }

        $excelPetugas = array_values(array_unique($excelPetugas));

        if (! empty($excelPetugas)) {
            $validNames = Pengguna::query()
                ->selectRaw('LOWER(pengguna.NAMA) AS ln')
                ->join('hak_akses', 'hak_akses.ID', '=', 'pengguna.HAKAKSES_ID')
                ->whereIn(DB::raw('LOWER(pengguna.NAMA)'), $excelPetugas)
                ->whereRaw('LOWER(hak_akses.HAKAKSES) = "petugas"')
                ->where('pengguna.STATUS', 1)
                ->pluck('ln')
                ->all();

            $validSet = array_fill_keys($validNames, true);
            $missing  = array_values(array_diff($excelPetugas, array_keys($validSet)));

            if (! empty($missing)) {
                $msgs = [];
                foreach ($missing as $m) {
                    $baris  = $excelPetugasLines[$m] ?? '?';
                    $msgs[] = "Baris Ke - {$baris}: Petugas \"{$m}\" tidak aktif / tidak terdaftar sebagai petugas.";
                }

                throw ValidationException::withMessages([
                    'row_errors' => $msgs,
                ])->errorBag('import');
            }
        }

        // =====================================================
        // 🔥 MAP NAMA PETUGAS → PENGGUNA_ID (TAMBAHAN UTAMA)
        // =====================================================
        $petugasMap = Pengguna::query()
            ->selectRaw('LOWER(pengguna.NAMA) AS ln, pengguna.ID')
            ->join('hak_akses', 'hak_akses.ID', '=', 'pengguna.HAKAKSES_ID')
            ->whereRaw('LOWER(hak_akses.HAKAKSES) = "petugas"')
            ->where('pengguna.STATUS', 1)
            ->pluck('ID', 'ln')
            ->toArray();

        // ============================
        //  PARSING & INSERT DETAIL
        // ============================
        $map = [
            'nama_petugas'      => 'PETUGAS_SDT',
            'petugas'           => 'PETUGAS_SDT',
            'petugas_sdt'       => 'PETUGAS_SDT',

            'nop'               => 'NOP',
            'tahun'             => 'TAHUN',
            'tahun_pajak'       => 'TAHUN',

            'alamat_op'         => 'ALAMAT_OP',
            'blok_kav_no_op'    => 'BLOK_KAV_NO_OP',
            'rt_op'             => 'RT_OP',
            'rw_op'             => 'RW_OP',
            'kel_op'            => 'KEL_OP',
            'kec_op'            => 'KEC_OP',

            'nama_wp'           => 'NAMA_WP',
            'alamat_wp'         => 'ALAMAT_WP',
            'blok_kav_no_wp'    => 'BLOK_KAV_NO_WP',
            'rt_wp'             => 'RT_WP',
            'rw_wp'             => 'RW_WP',
            'kel_wp'            => 'KEL_WP',
            'kota_wp'           => 'KOTA_WP',

            'jatuh_tempo'       => 'JATUH_TEMPO',

            'terhutang'         => 'TERHUTANG',
            'pengurangan'       => 'PENGURANGAN',
            'pbb_harus_dibayar' => 'PBB_HARUS_DIBAYAR',
        ];

        $rowErrors  = [];
        $seen       = [];
        $out        = [];
        $actualCols = Schema::getColumnListing('dt_sdt');

        foreach ($rows as $idx => $row) {

            $line = $idx + 2;

            if (
                empty(trim($row['nop'] ?? '')) &&
                empty(trim($row['nama_petugas'] ?? '')) &&
                empty(trim($row['tahun'] ?? '')) &&
                empty(trim($row['alamat_wp'] ?? '')) &&
                empty(trim($row['alamat_op'] ?? ''))
            ) {
                continue;
            }

            $r = [];
            foreach ($map as $excelKey => $dbCol) {
                if ($row->has($excelKey)) {
                    $r[$dbCol] = trim((string) ($row[$excelKey] ?? ''));
                }
            }

            $petugas = $r['PETUGAS_SDT'] ?? '';
            $nopRaw  = trim($r['NOP'] ?? '');

            // VALIDASI FORMAT NOP
            if (! preg_match('/^\d{2}\.\d{2}\.\d{3}\.\d{3}\.\d{3}-\d{4}\.\d$/', $nopRaw)) {
                $rowErrors[] = "Baris Ke - {$line}: NOP berikut '{$nopRaw}' tidak valid. Format NOP harus seperti ini 14.71.020.002.014-1701.0";
                continue;
            }

            $nop   = $this->normalizeNop($nopRaw);
            $tahun = $r['TAHUN'] ?? '';

            if ($petugas === '' || $nop === '' || $tahun === '') {
                $rowErrors[] = "Baris Ke - {$line}: kolom (NAMA PETUGAS, NOP, TAHUN PAJAK) tidak boleh kosong.";
                continue;
            }

            // =====================================
            // 🔥 RESOLVE PENGGUNA_ID (INTI FIX)
            // =====================================
            $petugasLower = mb_strtolower(trim($petugas), 'UTF-8');
            $penggunaId   = $petugasMap[$petugasLower] ?? null;

            if (! $penggunaId) {
                $rowErrors[] = "Baris Ke - {$line}: Petugas '{$petugas}' tidak ditemukan.";
                continue;
            }

            $key = $nop . '#' . $tahun;
            if (isset($seen[$key])) {
                $rowErrors[] = "Baris Ke - {$line}: NOP+TAHUN duplikat dengan baris {$seen[$key]}.";
                continue;
            }
            $seen[$key] = $line;

            foreach (['TERHUTANG', 'PENGURANGAN', 'PBB_HARUS_DIBAYAR'] as $moneyCol) {
                if (array_key_exists($moneyCol, $r)) {
                    $r[$moneyCol] = $this->numish($r[$moneyCol]);
                }
            }

            if (! empty($r['JATUH_TEMPO'])) {
                $ymd = $this->parseDateYmdStrict($r['JATUH_TEMPO']);
                if (! $ymd) {
                    $rowErrors[] = "Baris Ke - {$line}: JATUH TEMPO '{$r['JATUH_TEMPO']}' tidak valid.";
                    continue;
                }
                $r['JATUH_TEMPO'] = $ymd;
            } else {
                $r['JATUH_TEMPO'] = null;
            }

            $filtered = array_intersect_key($r, array_flip($actualCols));

            $filtered['ID_SDT']      = $this->sdtId;
            $filtered['NOP']         = $nop;
            $filtered['PETUGAS_SDT'] = $penggunaId;
            $filtered['KD_UNIT']     = $this->kdUnit;

            $out[] = $filtered;
        }

        if (! empty($rowErrors)) {
            throw ValidationException::withMessages([
                'row_errors' => $rowErrors,
            ])->errorBag('import');
        }

        DB::transaction(function () use ($out) {
            foreach (array_chunk($out, 1000) as $chunk) {
                DtSdt::insert($chunk);
            }
        });
    }

    private function numish($v): ?string
    {
        if ($v === null) {
            return null;
        }

        $s = trim((string) $v);
        if ($s === '') {
            return null;
        }

        $s = str_replace([',', '.', ' '], '', $s);
        if (! preg_match('/^-?\d+$/', $s)) {
            return null;
        }

        return $s;
    }

    private function parseDateYmdStrict($val): ?string
    {
        if ($val === null || $val === '') {
            return null;
        }

        if (is_numeric($val)) {
            try {
                $ts = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($val);
                return gmdate('Y-m-d', $ts);
            } catch (\Throwable $e) {}
        }

        $in = trim((string) $val);
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $fmt) {
            $d = \DateTime::createFromFormat($fmt, $in);
            if ($d && $d->format($fmt) === $in) {
                return $d->format('Y-m-d');
            }
        }
        return null;
    }

    private function normalizeNop($v): string
    {
        $s = preg_replace('/\D+/', '', (string) $v);
        return $s ?? '';
    }
}
