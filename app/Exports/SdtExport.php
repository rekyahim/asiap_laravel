<?php
namespace App\Exports;

use App\Models\DtSdt;
use App\Models\StatusPenyampaian;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SdtExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithColumnWidths
{
    protected int $sdtId;

    public function __construct(int $sdtId)
    {
        $this->sdtId = $sdtId;
    }

    public function collection()
    {
        $rows = DtSdt::query()
            ->leftJoin('pengguna', 'pengguna.ID', '=', 'dt_sdt.PETUGAS_SDT')
            ->where('dt_sdt.ID_SDT', $this->sdtId)
            ->orderBy('dt_sdt.ID')
            ->select([
                'dt_sdt.*',
                DB::raw('pengguna.NAMA AS petugas_nama'),
            ])
            ->get();

        $formatNop = function ($nop) {
            $nop = preg_replace('/\D+/', '', (string) $nop);
            if (strlen($nop) !== 18) {
                return $nop;
            }

            return substr($nop, 0, 2) . '.' .
            substr($nop, 2, 2) . '.' .
            substr($nop, 4, 3) . '.' .
            substr($nop, 7, 3) . '.' .
            substr($nop, 10, 3) . '-' .
            substr($nop, 13, 4) . '.0';
        };

        $data = [];

        foreach ($rows as $d) {

            /** ambil status terakhir */
            $sp = StatusPenyampaian::where('ID_DT_SDT', $d->ID)
                ->orderByDesc('id')
                ->first();

            /* ===============================
             * STATUS PENYAMPAIAN
             * =============================== */
            if ($sp) {
                $statusPenyampaian = match ((int) $sp->STATUS_PENYAMPAIAN) {
                    1       => 'Tersampaikan',
                    0       => 'Tidak Tersampaikan',
                    default => '-',
                };

                $statusOP = match ((int) $sp->STATUS_OP) {
                    1       => 'Belum Diproses Petugas',
                    2       => 'Ditemukan',
                    3       => 'Tidak Ditemukan',
                    4       => 'Sudah Dijual',
                    default => '-',
                };

                $statusWP = match ((int) $sp->STATUS_WP) {
                    1       => 'Belum Diproses Petugas',
                    2       => 'Ditemukan',
                    3       => 'Tidak Ditemukan',
                    4       => 'Luar Kota',
                    default => '-',
                };

                $tglPenyampaian = $sp->TGL_PENYAMPAIAN
                    ? date('Y-m-d H:i:s', strtotime($sp->TGL_PENYAMPAIAN))
                    : '-';
            } else {
                $statusPenyampaian = '-';
                $statusOP          = '-';
                $statusWP          = '-';
                $tglPenyampaian    = '-';
            }

            $data[] = [
                $d->petugas_nama ?? '-', // NAMA PETUGAS
                $formatNop($d->NOP),     // NOP
                $d->TAHUN,

                $d->ALAMAT_OP,
                $d->BLOK_KAV_NO_OP,
                $d->RT_OP,
                $d->RW_OP,
                $d->KEL_OP,
                $d->KEC_OP,

                $d->NAMA_WP,
                $d->ALAMAT_WP,
                $d->BLOK_KAV_NO_WP,
                $d->RT_WP,
                $d->RW_WP,
                $d->KEL_WP,
                $d->KOTA_WP,

                optional($d->JATUH_TEMPO)->format('Y-m-d'),
                $d->TERHUTANG,
                $d->PENGURANGAN,
                $d->PBB_HARUS_DIBAYAR,

                $statusPenyampaian,
                $statusOP,
                $sp->NOP_BENAR ?? '-',
                $statusWP,
                $sp->KETERANGAN_PETUGAS ?? '-',
                $sp->EVIDENCE ?? '-',
                $sp->KOORDINAT_OP ?? '-',
                $tglPenyampaian,
                $sp->NAMA_PENERIMA ?? '-',
                $sp->HP_PENERIMA ?? '-',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'NAMA PETUGAS',
            'NOP',
            'TAHUN PAJAK',
            'ALAMAT OP',
            'BLOK KAV NO OP',
            'RT OP',
            'RW OP',
            'KEL OP',
            'KEC OP',
            'NAMA WP',
            'ALAMAT WP',
            'BLOK KAV NO WP',
            'RT WP',
            'RW WP',
            'KEL WP',
            'KOTA WP',
            'JATUH TEMPO',
            'TERHUTANG',
            'PENGURANGAN',
            'PBB HARUS DIBAYAR',
            'STATUS PENYAMPAIAN',
            'STATUS OP',
            'NOP BENAR',
            'STATUS WP',
            'KETERANGAN PETUGAS',
            'EVIDENCE',
            'KOORDINAT OP',
            'TANGGAL PENYAMPAIAN',
            'NAMA PENERIMA',
            'HP PENERIMA',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A'  => 18, 'B'  => 25, 'C'  => 12, 'D'  => 25, 'E' => 18,
            'F'  => 8, 'G'   => 8, 'H'   => 12, 'I'  => 12,
            'J'  => 20, 'K'  => 25, 'L'  => 18, 'M'  => 8, 'N'  => 8,
            'O'  => 12, 'P'  => 12,
            'Q'  => 14, 'R'  => 15, 'S'  => 15, 'T'  => 18,
            'U'  => 20, 'V'  => 20, 'W'  => 20, 'X'  => 20,
            'Y'  => 25, 'Z'  => 20, 'AA' => 18, 'AB' => 18,
            'AC' => 18, 'AD' => 18,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->freezePane('A2');

                $sheet->getStyle("A1:AD1")->applyFromArray([
                    'fill'      => [
                        'fillType' => 'solid',
                        'color'    => ['rgb' => 'E5E5E5'],
                    ],
                    'font'      => ['bold' => true],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical'   => 'center',
                    ],
                ]);

                $sheet->getStyle("A1:AD{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A1:AD{$lastRow}")
                    ->getAlignment()
                    ->setWrapText(true);
            },
        ];
    }
}
