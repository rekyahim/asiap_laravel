<?php
namespace App\Http\Controllers;

use App\Models\HakAkses;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    private array $unitMap = [
        1  => 'BAPENDA',
        2  => 'PD I',
        3  => 'DALJAK',
        4  => 'UPT I',
        5  => 'UPT II',
        6  => 'UPT III',
        7  => 'UPT IV',
        8  => 'UPT V',
        9  => 'SEKRETARIAT',
        10 => 'PD II',
        11 => 'P3D',
    ];

    /* =========================
       🔹 INDEX
    ========================= */
    public function index(Request $r)
    {
        $q = trim($r->query('q', ''));

        $users = Pengguna::query()
            ->when($q, fn($x) => $x->where(function ($w) use ($q) {
                $w->where('USERNAME', 'like', "%{$q}%")
                    ->orWhere('NAMA', 'like', "%{$q}%")
                    ->orWhere('NIP', 'like', "%{$q}%");
            }))
            ->orderBy('ID')
            ->paginate(25);

        $roles = HakAkses::where('STATUS', 1)
            ->orderBy('HAKAKSES')
            ->get(['ID', 'HAKAKSES']);

        $units = $this->unitMap;

        return view('admin.pengguna', compact('users', 'roles', 'q', 'units'));
    }

    /* =========================
       🔹 CREATE
    ========================= */
    public function store(Request $r)
    {
        $data = $r->validate([
            'USERNAME'    => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_.-]+$/', Rule::unique('pengguna', 'USERNAME')],
            'NAMA'        => ['required', 'string', 'max:255'],
            'JABATAN'     => ['required', Rule::in(['PNS', 'NON PNS'])],
            'NIP'         => ['nullable', 'string', 'max:50'],
            'KD_UNIT'     => ['nullable', 'integer', 'between:1,11'],
            'HAKAKSES_ID' => ['nullable', 'integer', 'exists:hak_akses,ID'],
        ]);

        $role     = isset($data['HAKAKSES_ID']) ? HakAkses::find($data['HAKAKSES_ID']) : null;
        $unitName = $data['KD_UNIT'] ? ($this->unitMap[$data['KD_UNIT']] ?? null) : null;

        $user = Pengguna::create([
            'USERNAME'         => strtolower(trim($data['USERNAME'])),
            'NAMA'             => strtoupper(trim($data['NAMA'])),
            'JABATAN'          => $data['JABATAN'],
            'NIP'              => $data['NIP'] ?? null,
            'KD_UNIT'          => $data['KD_UNIT'] ?? null,
            'NAMA_UNIT'        => $unitName,
            'HAKAKSES_ID'      => $data['HAKAKSES_ID'] ?? null,
            'HAKAKSES'         => $role?->HAKAKSES,
            'STATUS'           => 1,
            'TGLPOST'          => now(),
            'INITIAL_PASSWORD' => '123456',
            'PASSWORD'         => bcrypt('123456'),
        ]);
        // 🔥 LOG otomatis: created

        return back()->with([
            'success'      => 'Pengguna dibuat.',
            'created_user' => [
                'USERNAME'         => $user->USERNAME,
                'INITIAL_PASSWORD' => '123456',
                'ID'               => $user->ID,
            ],
        ]);
    } /* =========================
       🔹 UPDATE
    ========================= */
    public function update(Request $r, $id)
    {
        $user = Pengguna::findOrFail($id);

        $data = $r->validate([
            'USERNAME' => [
                'required', 'string', 'max:100',
                'regex:/^[a-z0-9_.-]+$/',
                Rule::unique('pengguna', 'USERNAME')->ignore($user->ID, 'ID'),
            ],
            'NAMA'     => ['required', 'string', 'max:255'],
            'JABATAN'  => ['required', Rule::in(['PNS', 'NON PNS'])],
            'NIP'      => ['nullable', 'string', 'max:50'],
            'KD_UNIT'  => ['nullable', 'integer', 'between:1,11'],
        ]);

        $unitName = $data['KD_UNIT'] ? ($this->unitMap[$data['KD_UNIT']] ?? null) : null;

        $user->update([
            'USERNAME'  => strtolower(trim($data['USERNAME'])),
            'NAMA'      => strtoupper(trim($data['NAMA'])),
            'JABATAN'   => $data['JABATAN'],
            'NIP'       => $data['NIP'] ?? null,
            'KD_UNIT'   => $data['KD_UNIT'] ?? null,
            'NAMA_UNIT' => $unitName,
        ]);
        // 🔥 LOG otomatis: updated (old → new jelas)

        return back()->with('success', 'Data pengguna diperbarui.');
    }
    /* =========================
       🔹 DELETE (Soft Delete)
    ========================= */
    public function destroy($id)
    {
        $user         = Pengguna::findOrFail($id);
        $user->STATUS = 0;
        $user->save();
        // 🔥 LOG otomatis → event = deleted

        return back()->with('success', 'Pengguna dinonaktifkan.');
    }
    /* =========================
       🔹 UPDATE HAK AKSES
    ========================= */
    public function updateHakAkses(Request $r, $id)
    {
        $data = $r->validate([
            'hakakses_id' => ['nullable', 'integer', Rule::exists('hak_akses', 'ID')->where('STATUS', 1)],
        ]);

        $user = Pengguna::findOrFail($id);

        $role = $data['hakakses_id']
            ? HakAkses::find($data['hakakses_id'])
            : null;

        // jika sama → stop (no log)
        if ((int) $user->HAKAKSES_ID === (int) ($role?->ID)) {
            return back()->with('success', 'Hak akses tidak berubah.');
        }

        $user->update([
            'HAKAKSES_ID' => $role?->ID,
            'HAKAKSES'    => $role?->HAKAKSES,
        ]);
        // 🔥 LOG otomatis:
        // old: HAKAKSES_ID + HAKAKSES
        // new: HAKAKSES_ID + HAKAKSES

        return back()->with('success', 'Hak akses pengguna diperbarui.');
    }
    /* =========================
       🔹 RESET PASSWORD
    ========================= */
    public function resetPassword($id)
    {
        $u                   = Pengguna::findOrFail($id);
        $u->PASSWORD         = bcrypt('123456');
        $u->INITIAL_PASSWORD = '123456';
        $u->save();

        return back()->with('success', "Password pengguna {$u->USERNAME} berhasil direset ke 123456.");
    }

    /* =========================
       🔹 UPDATE PROFILE SENDIRI
    ========================= */
    public function updateProfile(Request $r)
    {
        $r->validate([
            'NAMA' => 'required|string|max:255',
        ]);

        $user = Pengguna::find(session('auth_uid'));

        $user->NAMA = strtoupper(trim($r->NAMA));
        $user->save();

        session(['auth_name' => $user->NAMA]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /* =========================
       🔹 GANTI PASSWORD SENDIRI
    ========================= */
    public function changePassword(Request $r)
    {
        $r->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Pengguna::find(session('auth_uid'));

        if (! Hash::check($r->old_password, $user->PASSWORD)) {
            return back()->with('error', 'Password lama salah.');
        }

        $user->PASSWORD         = bcrypt($r->new_password);
        $user->INITIAL_PASSWORD = null;
        $user->save();

        return back()->with('success', 'Password berhasil diganti.');
    }

    /* ================================
       🔹 FORM WAJIB GANTI PASSWORD
    ================================ */
    public function forceChangePasswordForm()
    {
        $user = Pengguna::find(session('auth_uid'));

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->INITIAL_PASSWORD !== '123456') {
            return redirect()->route('dashboard');
        }

        return view('auth.force-change-password', compact('user'));
    }

    /* ================================
       🔹 UPDATE PASSWORD WAJIB
    ================================ */
    public function forceChangePasswordUpdate(Request $r)
    {
        $r->validate([
            'new_password'              => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        $user = Pengguna::find(session('auth_uid'));

        if (! $user) {
            return redirect()->route('login');
        }

        $user->PASSWORD         = bcrypt($r->new_password);
        $user->INITIAL_PASSWORD = null;
        $user->save();

        // logout otomatis setelah ganti password
        session()->forget(['auth_uid', 'auth_name', 'auth_uname', 'auth_role']);
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }

    /* =========================
       🔹 SHOW PROFILE VIEW (opsional)
    ========================= */
    public function showProfile()
    {
        $user = Pengguna::find(session('auth_uid'));
        return view('admin.profile', compact('user'));
    }

    public function changePasswordForm()
    {
        return view('profile.change-password');
    }

    public function changePasswordUpdate(Request $r)
    {
        $r->validate([
            'old_password'              => 'required|string',
            'new_password'              => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        // ambil user dari session (karena lo pakai session manual)
        $user = Pengguna::find(session('auth_uid'));

        if (! $user) {
            // kalau user hilang di session, minta login ulang
            return redirect()->route('login')->with('error', 'Silakan login ulang.');
        }

        // debugging (hapus/komentari setelah beres):
        // dd('input', $r->old_password, 'db_hash', $user->PASSWORD, 'check', Hash::check($r->old_password, $user->PASSWORD));

        if (! Hash::check($r->old_password, $user->PASSWORD)) {
            return back()->with('error', 'Password lama salah.');
        }

        // update password
        $user->PASSWORD         = bcrypt($r->new_password);
        $user->INITIAL_PASSWORD = null;
        $user->save();

        // pilihan: logout user setelah ganti password paksa (untuk safety) atau tetap di halaman
        // Jika ingin logout dan arahkan ke login:
        // session()->forget(['auth_uid','auth_name','auth_uname','auth_role','force_change_pwd']);
        // return redirect()->route('login')->with('success','Password berhasil diubah. Silakan login kembali.');

        // Jika ingin tetap login dan stay:
        return redirect()->route('profile.show')->with('success', 'Password berhasil diubah.');
    }

    /* =========================
   🔹 ACTIVATE USER (Restore)
========================= */
    public function activate($id)
    {
        $u = Pengguna::findOrFail($id);

        $u->STATUS = 1;
        $u->save();

        return back()->with('success', 'Pengguna diaktifkan kembali.');
    }

}
