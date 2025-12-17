<?php
namespace App\Support;

class LogUi
{
    public static function color(string $logName): string
    {
        return match ($logName) {
            'sdt'            => 'primary',
            'dt_sdt'         => 'info',
            'penyampaian'    => 'success',
            'pengguna'       => 'warning',
            'hak_akses'      => 'dark',
            'hakakses_modul' => 'secondary',
            'modul'          => 'indigo',
            default          => 'light',
        };
    }

    public static function icon(string $event): string
    {
        return match ($event) {
            'created' => 'bi-plus-circle',
            'updated' => 'bi-pencil-square',
            'deleted' => 'bi-trash',
            default   => 'bi-info-circle',
        };
    }

    public static function title(string $logName): string
    {
        return match ($logName) {
            'sdt'            => 'SDT',
            'dt_sdt'         => 'Detail SDT',
            'penyampaian'    => 'Penyampaian',
            'pengguna'       => 'Pengguna',
            'hak_akses'      => 'Hak Akses',
            'hakakses_modul' => 'Hak Akses Modul',
            'modul'          => 'Modul',
            default          => strtoupper($logName),
        };
    }
}
