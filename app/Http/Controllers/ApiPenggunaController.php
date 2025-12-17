<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiPenggunaController extends Controller
{
    // GET /koor/api/pengguna-search?q=adi&page=1
    public function search(Request $r)
    {
        $q       = trim((string) $r->query('q', ''));
        $page    = max(1, (int) $r->query('page', 1));
        $perPage = 10;

        $base = DB::table('pengguna')
            ->join('hak_akses', 'hak_akses.ID', '=', 'pengguna.HAKAKSES_ID')
            ->whereRaw('LOWER(hak_akses.HAKAKSES) = "petugas"')
            ->where('pengguna.STATUS', 1) // hanya tampilkan petugas aktif
            ->select(['pengguna.ID', 'pengguna.NAMA', 'pengguna.NIP']);

        if ($q !== '') {
            // prefix search: "ADI%" (lebih ringan)
            $base->where('pengguna.NAMA', 'like', $q . '%');
        }

        $total = (clone $base)->count();
        $rows  = $base->orderBy('pengguna.NAMA')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'items' => $rows->map(function ($r) {
                return [
                    'id'   => $r->ID,
                    'text' => "({$r->NIP}) - {$r->NAMA}",
                    'nip'  => $r->NIP,
                    'nama' => $r->NAMA,
                ];
            })->values(),
            'hasMore' => ($page * $perPage) < $total,
        ]);
    }

    // GET /koor/api/pengguna/{id}
    public function show($id)
    {
        $row = DB::table('pengguna')
            ->join('hak_akses', 'hak_akses.ID', '=', 'pengguna.HAKAKSES_ID')
            ->whereRaw('LOWER(hak_akses.HAKAKSES) = "petugas"')
            ->where('pengguna.ID', $id)
            ->select(['pengguna.ID', 'pengguna.NAMA'])
            ->first();

        return response()->json([
            'item' => $row ? [
                'id'   => $row->ID,
                'text' => "({$row->NIP}) - {$row->NAMA}",
                'nip'  => $row->NIP,
                'nama' => $row->NAMA,
            ] : null,
        ]);
    }
}
