<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class PasswordController extends Controller
{
    public function showChangeForm()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/', // huruf + angka
                'confirmed',
            ],
        ], [
            'password.regex' => 'Password harus mengandung huruf dan angka.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = \App\Models\Pengguna::find(session('auth_id'));
        if (!$user) return redirect()->route('login');

        $user->PASSWORD = Hash::make($request->password);
        $user->INITIAL_PASSWORD = null; // tandai sudah diganti
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah.');
    }
}
