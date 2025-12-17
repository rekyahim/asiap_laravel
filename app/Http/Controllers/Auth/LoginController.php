<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (session()->has('auth_uid')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $r)
    {
        $cred = $r->validate([
            'USERNAME' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = strtolower(trim($cred['USERNAME']));

        /** @var Pengguna|null $user */
        $user = Pengguna::where('USERNAME', $username)->first();

        // ==============================
        // 🔴 Username / password salah
        // ==============================
        if (!$user || !Hash::check($cred['password'], $user->PASSWORD)) {
            return back()
                ->withInput(['USERNAME' => $username])
                ->with('error', 'Username atau password tidak cocok.');
        }

        // ==============================
        // 🔴 Akun nonaktif
        // ==============================
        if ((int)$user->STATUS !== 1) {
            return back()
                ->withInput(['USERNAME' => $username])
                ->with('error', 'Akun Anda nonaktif.');
        }

        // =====================================================
        //  🔹 CASE 1 — Password masih default "123456"
        // =====================================================
        if ($user->INITIAL_PASSWORD === '123456') {

            // Simpan session minimal agar bisa buka halaman ubah password
            session([
                'auth_uid'   => (int)$user->ID,
                'auth_name'  => $user->NAMA,
                'auth_uname' => $user->USERNAME,
                'auth_role'  => (int)($user->HAKAKSES_ID ?? 0),

                // Penanda bahwa user WAJIB ganti password
                'force_change_pwd' => true,
            ]);

            return redirect()->route('first.change.password');
        }

        // =====================================================
        //  🔹 CASE 2 — Password sudah diganti → Login normal
        // =====================================================
        session([
            'auth_uid'   => (int)$user->ID,
            'auth_name'  => $user->NAMA,
            'auth_uname' => $user->USERNAME,
            'auth_role'  => (int)($user->HAKAKSES_ID ?? 0),
        ]);

        // Redirect ke modul pertama
        $firstSlug = DB::table('hakakses_modul AS hm')
            ->join('modul AS m', 'm.ID', '=', 'hm.MODUL_ID')
            ->where('hm.HAKAKSES_ID', (int)$user->HAKAKSES_ID)
            ->where('hm.STATUS', 1)
            ->where('m.STATUS', 1)
            ->orderBy('m.NAMA_MODUL')
            ->value('m.LOKASI_MODUL');

        if ($firstSlug && \Route::has($firstSlug)) {

            if ($firstSlug === 'kelola_aksesmodul') {
                $firstHak = DB::table('hak_akses')
                    ->where('STATUS', 1)
                    ->orderBy('ID')
                    ->value('ID');

                if ($firstHak) {
                    return redirect()->route('kelola_aksesmodul', $firstHak);
                }
            }

            return redirect()->route($firstSlug);
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $r)
    {
        $r->session()->forget([
            'auth_uid',
            'auth_name',
            'auth_uname',
            'auth_role',
            'force_change_pwd'
        ]);

        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda sudah logout.');
    }
}
