<?php

namespace App\Services;

use RuntimeException;

class AsiapApiService
{
    private string $baseUrl;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->baseUrl  = rtrim(env('ASIAP_BASE_URL', 'https://core-api.bapenda.pekanbaru.go.id/api/asiap_app/'), '/');
        $this->username = env('ASIAP_BASIC_USER', 'asiap_app');
        $this->password = env('ASIAP_BASIC_PASS', 'euTpKORnaObqO8Jw');
    }

    /**
     * 🔹 Generic POST form-data request
     */
    private function post(string $endpoint, array $fields): array
    {
        $url = "{$this->baseUrl}/" . ltrim($endpoint, '/');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $fields, // form-data
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . base64_encode("{$this->username}:{$this->password}"),
            ],
        ]);

        $raw = curl_exec($ch);
        if ($raw === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException("cURL error: {$err}");
        }

        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException("HTTP {$status} dari API {$endpoint}");
        }

        $json = json_decode($raw, true);
        if (!is_array($json)) {
            throw new RuntimeException("Response JSON tidak valid dari {$endpoint}");
        }

        if (!($json['success'] ?? false)) {
            $msg = $json['message'] ?? 'API gagal tanpa pesan';
            throw new RuntimeException("{$endpoint} gagal: {$msg}");
        }

        return $json['data'] ?? [];
    }

    /**
     * 🔸 Ambil daftar NOP berdasarkan prefix
     */
    public function getNop(string $prefix): array
    {
        $rows = $this->post('nop', ['nop' => $prefix]);
        return collect($rows)
            ->map(fn($r) => [
                'id'   => $r['value'] ?? $r['NOP'] ?? $r['nop'] ?? null,
                'text' => $r['label']  ?? $r['value'] ?? $r['NOP'] ?? null,
            ])
            ->filter(fn($i) => filled($i['id']) && filled($i['text']))
            ->unique('id')
            ->take(5)
            ->values()
            ->all();
    }

    /**
     * 🔸 Ambil daftar tahun SPPT untuk NOP tertentu
     */
    public function getTahun(string $nop): array
    {
        $rows = $this->post('tahunsppt', ['nop' => $nop]);
        return collect($rows)
            ->map(fn($r) => [
                'id'   => $r['value'] ?? $r['TAHUN'] ?? $r['tahun'] ?? null,
                'text' => $r['label']  ?? $r['value'] ?? $r['TAHUN'] ?? null,
            ])
            ->filter(fn($i) => filled($i['id']) && filled($i['text']))
            ->unique('id')
            ->sortDesc()
            ->values()
            ->all();
    }

    /**
     * 🔸 Ambil detail SPPT (NOP + Tahun)
     */
    public function getDetail(string $nop, string $tahun): array
    {
        $data = $this->post('detailsppt', [
            'nop'   => $label,
            'tahun' => $tahun,
        ]);

        return is_array($data) ? $data : [];
    }
}
