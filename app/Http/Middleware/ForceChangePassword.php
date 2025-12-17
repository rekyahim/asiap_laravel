<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use App\Models\Pengguna;
// use Illuminate\Support\Facades\Hash;

// class ForceChangePassword
// {
//     public function handle(Request $request, Closure $next)
//     {
//         // Cek apakah user sudah login (pakai session dari project kamu)
//         $userId = (int) session('auth_id');
//         if (!$userId) {
//             return $next($request);
//         }

//         // Rute yang boleh dilewati tanpa dicek
//         if ($request->routeIs(['password.change', 'password.update', 'logout'])) {
//             return $next($request);
//         }

//         // Jika password masih default 123456 → redirect ke ubah password
//         $user = Pengguna::find($userId);
//         if ($user && Hash::check('123456', (string) $user->PASSWORD)) {
//             session(['must_change_password' => true]);
//             return redirect()->route('password.change');
//         }

//         return $next($request);
//     }
// }
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthOnly
{
    public function handle(Request $request, Closure $next)
    {
        // Akses login jangan dihalangi
        if ($request->routeIs(['login', 'login.process'])) {
            return $next($request);
        }

        // Cek session login
        if (!session()->has('auth_uid')) {
            return redirect()->route('login');
        }

        $user = \App\Models\Pengguna::find(session('auth_uid'));

        // Jika user masih punya INITIAL_PASSWORD → wajib ganti password
        if ($user && $user->INITIAL_PASSWORD !== null) {
            if (!$request->routeIs(['first.change.password', 'first.change.password.update'])) {
                return redirect()->route('first.change.password');
            }
        }

        return $next($request);
    }
}

