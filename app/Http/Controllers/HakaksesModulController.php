<?php
namespace App\Http\Controllers;

use App\Models\HakAkses;
use App\Models\Modul;
use App\Models\Pengguna;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class HakaksesModulController extends Controller
{
    /** ===============================
     *  Halaman Mapping Hak Akses ↔ Modul
     *  =============================== */
    public function editModules(HakAkses $hak)
    {
        $roles = HakAkses::where('STATUS', 1)
            ->with(['moduls' => fn($q) => $q->where('modul.STATUS', 1)])
            ->orderBy('HAKAKSES')
            ->get(['ID', 'HAKAKSES', 'STATUS', 'TGLPOST']);

        $moduls = Modul::where('STATUS', 1)
            ->orderBy('NAMA_MODUL')
            ->get(['ID', 'NAMA_MODUL', 'LOKASI_MODUL']);

        foreach ($roles as $r) {
            $r->OWNED_IDS = DB::table('hakakses_modul as hm')
                ->join('modul as m', 'm.ID', '=', 'hm.MODUL_ID')
                ->where('hm.HAKAKSES_ID', $r->ID)
                ->where('hm.STATUS', 1)
                ->where('m.STATUS', 1)
                ->pluck('hm.MODUL_ID')
                ->all();

            if (strtolower($r->HAKAKSES) === 'super admin') {
                $r->OWNED_IDS = Modul::pluck('ID')->all();
            }
        }

        DB::table('hakakses_modul as hm')
            ->leftJoin('modul as m', 'm.ID', '=', 'hm.MODUL_ID')
            ->whereNull('m.ID')
            ->delete();

        return view('admin.hakakses-modul', compact('hak', 'roles', 'moduls'));
    }

    /** ===============================
     *  Simpan Mapping Hak Akses ↔ Modul
     *  =============================== */
    public function update(Request $request, HakAkses $hak)
    {
        $request->validate([
            'modul_ids.*' => ['integer', 'exists:modul,ID'],
        ]);

        $idsBaru = collect(Arr::wrap($request->input('modul_ids')))
            ->filter()
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        DB::beginTransaction();
        try {
            /* ================= DATA LAMA ================= */
            $lamaIds = DB::table('hakakses_modul')
                ->where('HAKAKSES_ID', $hak->ID)
                ->where('STATUS', 1)
                ->pluck('MODUL_ID')
                ->all();

            /* ================= HITUNG PERUBAHAN ================= */
            $addedIds   = array_values(array_diff($idsBaru, $lamaIds));
            $removedIds = array_values(array_diff($lamaIds, $idsBaru));

            /* ================= UPDATE DB ================= */
            DB::table('hakakses_modul')
                ->where('HAKAKSES_ID', $hak->ID)
                ->where('STATUS', 1)
                ->update(['STATUS' => 0]);

            $now  = now();
            $rows = [];

            foreach ($idsBaru as $mid) {
                $rows[] = [
                    'HAKAKSES_ID' => $hak->ID,
                    'MODUL_ID'    => $mid,
                    'STATUS'      => 1,
                    'TGLPOST'     => $now,
                ];
            }

            if (! empty($rows)) {
                DB::table('hakakses_modul')->insert($rows);
            }

            DB::commit();

            /* ================= ACTIVITY LOG ================= */
            $user = Pengguna::find(session('auth_uid'));

            $added   = Modul::whereIn('ID', $addedIds)->get(['ID', 'NAMA_MODUL']);
            $removed = Modul::whereIn('ID', $removedIds)->get(['ID', 'NAMA_MODUL']);

            activity('hakakses_modul')
                ->event('updated')
                ->performedOn($hak)
                ->causedBy($user)
                ->withProperties([
                    'hak_akses' => [
                        'id'   => $hak->ID,
                        'nama' => $hak->HAKAKSES,
                    ],
                    'modul'     => [
                        'added'   => $added->map(fn($m) => ['id' => $m->ID, 'nama' => $m->NAMA_MODUL])->values(),
                        'removed' => $removed->map(fn($m) => ['id' => $m->ID, 'nama' => $m->NAMA_MODUL])->values(),
                    ],
                ])
                ->log("Mapping modul untuk Hak Akses \"{$hak->HAKAKSES}\" diperbarui");

            return redirect()
                ->route('admin.hakakses.modul.edit', ['hak' => $hak->ID])
                ->with('success', '✅ Akses modul berhasil diperbarui. Riwayat lama disimpan.');
        } catch (QueryException $e) {
            DB::rollBack();

            $msg = $e->getMessage();

            if (str_contains($msg, 'Duplicate') || $e->getCode() === '23000') {
                return redirect()
                    ->route('admin.hakakses.modul.edit', ['hak' => $hak->ID])
                    ->with('error', '⚠️ Duplikasi kombinasi (HAKAKSES_ID, MODUL_ID).');
            }

            return redirect()
                ->route('admin.hakakses.modul.edit', ['hak' => $hak->ID])
                ->with('error', '❌ Terjadi kesalahan: ' . $msg);
        }
    }
}
