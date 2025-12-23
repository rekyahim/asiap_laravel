<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;

class AuthOnly
{
    public function handle(Request $request, Closure $next)
    {
        // Jika akses halaman login, abaikan middleware
        if ($request->routeIs('login', 'login.process')) {
            return $next($request);
        }

        // Cek session login
        if (!session()->has('auth_uid')) {
            return redirect()->route('login');
        }

        // Ambil user dari database
        $user = Pengguna::find(session('auth_uid'));

        if (!$user) {
            // Jika user tidak ditemukan → hapus session dan logout
            session()->forget('auth_uid');
            return redirect()->route('login');
        }

        // **Inject user ke Laravel Auth**
        if (!Auth::check()) {
    Auth::login($user);
}



        // Jika password default → wajib ubah password
        if ($user->INITIAL_PASSWORD === '123456') {
            if (!$request->routeIs('first.change.password', 'first.change.password.update')) {
                return redirect()->route('first.change.password');
            }
        }

        return $next($request);
    }
}
