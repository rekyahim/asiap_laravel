<?php
namespace App\Exports;

use App\Models\DtSdt;
use App\Models\StatusPenyampaian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SdtExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithColumnWidths
{
    protected $sdtId;

    public function __construct($sdtId)
    {
        $this->sdtId = $sdtId;
    }

    public function collection()
    {
        $rows = DtSdt::where('ID_SDT', $this->sdtId)->orderBy('ID')->get();

        $formatNop = function ($nop) {
            $nop = preg_replace('/\D+/', '', $nop);
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

            $sp = StatusPenyampaian::where('ID_DT_SDT', $d->ID)
                ->latest('id')
                ->first();

            // ================================
            // Safe value (NULL friendly)
            // ================================
            $statusPenyampaian = match (optional($sp)->STATUS_PENYAMPAIAN) {
                1       => 'Tersampaikan',
                0       => 'Belum Tersampaikan',
                default => '-'
            };

            $statusOP = match (optional($sp)->STATUS_OP) {
                1       => 'Benar',
                0       => 'Tidak Benar',
                default => '-'
            };

            $statusWP = match (optional($sp)->STATUS_WP) {
                1       => 'Ditemui',
                0       => 'Tidak Ditemui',
                default => '-'
            };

            $data[] = [
                $d->PETUGAS_SDT,
                "" . $formatNop($d->NOP),
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
                optional($sp)->NOP_BENAR ?? '-',
                $statusWP,
                optional($sp)->KETERANGAN_PETUGAS ?? '-',
                optional($sp)->EVIDENCE ?? '-',
                optional($sp)->KOORDINAT_OP ?? '-',
                optional(optional($sp)->TGL_PENYAMPAIAN)->format('Y-m-d H:i:s'),
                optional($sp)->NAMA_PENERIMA ?? '-',
                optional($sp)->HP_PENERIMA ?? '-',
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
    } /* Styling header */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

/* Lebar kolom */
    public function columnWidths(): array
    {
        return [
            'A'  => 18, // Nama Petugas
            'B'  => 25, // NOP
            'C'  => 12, // Tahun
            'D'  => 25, // Alamat OP
            'E'  => 18,
            'F'  => 8,
            'G'  => 8,
            'H'  => 12,
            'I'  => 12,

            'J'  => 20, // Nama WP
            'K'  => 25,
            'L'  => 18,
            'M'  => 8,
            'N'  => 8,
            'O'  => 12,
            'P'  => 12,

            'Q'  => 14, // Jatuh tempo
            'R'  => 15,
            'S'  => 15,
            'T'  => 18,

            'U'  => 20, // Status Penyampaian
            'V'  => 15,
            'W'  => 20,
            'X'  => 20,
            'Y'  => 25,
            'Z'  => 20,
            'AA' => 18,
            'AB' => 18,
            'AC' => 18,
            'AD' => 18,
        ];
    }

/* Border + Freeze + Background */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                                                    // Hitung jumlah baris (header + data)
                $lastRow = $sheet->getHighestRow(); // dinamis

                // Freeze baris header
                $sheet->freezePane('A2');

                // Warna header
                $sheet->getStyle("A1:AB1")->applyFromArray([
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

                // Border seluruh tabel
                $sheet->getStyle("A1:AB{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Auto wrap text untuk kolom panjang
                $sheet->getStyle("A1:AD{$lastRow}")->getAlignment()->setWrapText(true);
            },
        ];
    }
}
