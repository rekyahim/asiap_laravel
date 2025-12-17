<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- statistik global (aman untuk publik) ---
        $stats = [
            'modul_aktif'     => (int) DB::table('modul')->where('STATUS', 1)->count(),
            'hakakses_aktif'  => (int) DB::table('hak_akses')->where('STATUS', 1)->count(),
            'pengguna_aktif'  => (int) DB::table('pengguna')->where('STATUS', 1)->count(),
            'mapping_aktif'   => (int) DB::table('hakakses_modul')->where('STATUS', 1)->count(),
        ];

        // --- daftar shortcut modul untuk user yang login ---
        $shortcuts = [];
        if ($request->user()) {
            $hakId = (int) ($request->user()->HAKAKSES_ID ?? 0);

            $shortcuts = DB::table('hakakses_modul AS hm')
                ->join('modul AS m', 'm.ID', '=', 'hm.MODUL_ID')
                ->where('hm.HAKAKSES_ID', $hakId)
                ->where('hm.STATUS', 1)
                ->where('m.STATUS', 1)
                ->orderBy('m.NAMA_MODUL')
                ->get(['m.NAMA_MODUL', 'm.LOKASI_MODUL'])
                ->map(function ($row) {
                    return [
                        'label' => $row->NAMA_MODUL,
                        'slug'  => $row->LOKASI_MODUL, // sama dengan nama route index modul
                    ];
                })->toArray();
        }

        return view('dashboard', [
            'stats'     => $stats,
            'shortcuts' => $shortcuts,
        ]);
    }
}
