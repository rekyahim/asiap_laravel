<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CanModul
{
    public function handle(Request $request, Closure $next, string $slug)
    {
        // ✅ Wajib login
        if (! session()->has('auth_uid')) {
            return redirect()->route('login')->with('warning', 'Silakan login dulu.');
        }

        $roleId = (int) session('auth_role');
        if (! $roleId) {
            abort(403, 'Role tidak dikenali.');
        }

        // ✅ Super Admin bypass semua modul
        $isSuperAdmin = DB::table('hak_akses')
            ->where('ID', $roleId)
            ->where('STATUS', 1)
            ->whereRaw("LOWER(HAKAKSES) LIKE '%super admin%'")
            ->exists();

        if ($isSuperAdmin) {
            return $next($request);
        }

        // ✅ Cek mapping hak akses ↔ modul
        $allowed = DB::table('hakakses_modul as hm')
            ->join('modul as m', 'm.ID', '=', 'hm.MODUL_ID')
            ->where('hm.HAKAKSES_ID', $roleId)
            ->where('hm.STATUS', 1)
            ->where('m.STATUS', 1)
            ->where(function ($q) use ($slug) {
                $q->where('m.LOKASI_MODUL', $slug)
                  ->orWhere('m.LOKASI_MODUL', 'like', $slug . '%');
            })
            ->exists();

        if (! $allowed) {
            throw new HttpException(403, 'Anda tidak punya akses ke modul: ' . $slug);
        }

        return $next($request);
    }
}
