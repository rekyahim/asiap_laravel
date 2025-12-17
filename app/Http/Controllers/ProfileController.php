<?php
namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
// <-- penting (File helper)

class ProfileController extends Controller
{
    public function show()
    {
        $uid = session('auth_uid');

        if (! $uid) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Pengguna::find($uid);

        if (! $user) {
            return redirect('/')->with('error', 'Data pengguna tidak ditemukan.');
        }

        return view('profile.show', compact('user'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Pengguna::find(session('auth_uid'));

        if (! $user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // ==============================
        // 1. HAPUS FILE LAMA (JIKA ADA)
        // ==============================
        if (! empty($user->ID_FOTO)) {
            $oldPath = public_path('assets/images/profile/' . $user->ID_FOTO);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        // ==============================
        // 2. UPLOAD FILE BARU
        // ==============================
        $file = $request->file('foto');

        $filename = 'user_' . $user->ID . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/images/profile'), $filename);

        // simpan filename → ID_FOTO
        $user->ID_FOTO = $filename;
        $user->save();

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }
    public function deletePhoto(Request $request)
    {
        $user = \App\Models\Pengguna::find(session('auth_uid'));

        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (empty($user->ID_FOTO)) {
            return back()->with('warning', 'Tidak ada foto untuk dihapus.');
        }

        $path = public_path('assets/images/profile/' . $user->ID_FOTO);

        // hapus file fisik jika ada
        if (File::exists($path)) {
            try {
                File::delete($path);
            } catch (\Throwable $e) {
                // jika terjadi error saat delete, tetap lanjut untuk set kolom ke null
            }
        }

        // set kembali ke default (kosongkan kolom)
        $user->ID_FOTO = null;
        $user->save();

        return back()->with('success', 'Foto profil berhasil dihapus dan kembali ke foto default.');
    }

}
