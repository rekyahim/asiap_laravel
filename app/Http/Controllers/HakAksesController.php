<?php
namespace App\Http\Controllers;

use App\Models\HakAkses;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HakAksesController extends Controller
{
    // GET /admin/hak-akses
    public function index(Request $r)
    {
        $q = $r->query('q');

        $items = HakAkses::query()
            ->where('STATUS', 1)
            ->when($q, fn($x) => $x->where('HAKAKSES', 'like', "%{$q}%"))
            ->orderBy('ID')
            ->paginate(15)
            ->withQueryString();

        return view('admin.hakakses', compact('items'));
    }

    // POST /admin/hak-akses
    public function store(Request $r)
    {
        $data = $r->validate([
            'HAKAKSES' => ['required', 'string', 'max:100'],
            'TGLPOST'  => ['nullable', 'date'],
            'STATUS'   => ['nullable'],
        ]);

        $data['STATUS']  = $r->boolean('STATUS', true);
        $data['TGLPOST'] = $data['TGLPOST'] ?? Carbon::now();

        $item = HakAkses::create($data);

        /* ================= ACTIVITY LOG ================= */
        $user = Pengguna::find(session('auth_uid'));

        activity('hak_akses')
            ->event('created')
            ->performedOn($item)
            ->causedBy($user)
            ->withProperties([
                'hak_akses' => [
                    'id'     => $item->ID,
                    'nama'   => $item->HAKAKSES,
                    'status' => $item->STATUS,
                ],
            ])
            ->log("Hak Akses \"{$item->HAKAKSES}\" ditambahkan");

        return redirect()
    ->route('admin.hakakses.modul.edit', $item->getKey())
    ->with([
        'swal' => [
            'title'    => 'Berhasil di tambah',
            'text'     => 'Hak akses berhasil ditambahkan. Silakan atur modulnya.',
            'icon'     => 'success',
            'position' => 'center',
            'timer'    => 2000,
        ],
    ]);
    }

    // GET /admin/hak-akses/{id}/edit
    public function edit($id, Request $r)
    {
        $item = HakAkses::findOrFail($id);

        $q     = $r->query('q');
        $items = HakAkses::query()
            ->where('STATUS', 1)
            ->when($q, fn($x) => $x->where('HAKAKSES', 'like', "%{$q}%"))
            ->orderBy('ID')
            ->paginate(15)
            ->withQueryString();

        return view('admin.hakakses', compact('items', 'item'));
    }

    // PATCH /admin/hak-akses/{id}
    public function update(Request $r, $id)
    {
        $item = HakAkses::findOrFail($id);

        $old = [
            'id'     => $item->ID,
            'nama'   => $item->HAKAKSES,
            'status' => $item->STATUS,
        ];

        $data = $r->validate([
            'HAKAKSES' => ['required', 'string', 'max:100'],
            'TGLPOST'  => ['nullable', 'date'],
            'STATUS'   => ['nullable'],
        ]);

        $data['STATUS'] = $r->boolean('STATUS', $item->STATUS);

        if (! array_key_exists('TGLPOST', $data) || is_null($data['TGLPOST'])) {
            unset($data['TGLPOST']);
        }

        $item->update($data);

        $new = [
            'id'     => $item->ID,
            'nama'   => $item->HAKAKSES,
            'status' => $item->STATUS,
        ];

        /* ================= ACTIVITY LOG ================= */
        $user = Pengguna::find(session('auth_uid'));

        activity('hak_akses')
            ->event('updated')
            ->performedOn($item)
            ->causedBy($user)
            ->withProperties([
                'old' => $old,
                'new' => $new,
            ])
            ->log("Hak Akses \"{$item->HAKAKSES}\" diperbarui");

        return redirect()
    ->route('admin.hakakses.modul.edit', $item->getKey())
    ->with([
        'swal' => [
            'title'    => 'Berhasil di perbarui',
            'text'     => 'Hak akses berhasil diperbarui. Silakan lanjut atur modul.',
            'icon'     => 'success',
            'position' => 'center',
            'timer'    => 2000,
        ],
    ]);

    }

    // DELETE /admin/hak-akses/{id} (soft delete: STATUS=0)
    public function destroy(Request $r, $id)
    {
        $item = HakAkses::findOrFail($id);

        $old = [
            'id'     => $item->ID,
            'nama'   => $item->HAKAKSES,
            'status' => $item->STATUS,
        ];

        $item->STATUS = 0;
        $item->save();

        /* ================= ACTIVITY LOG ================= */
        $user = Pengguna::find(session('auth_uid'));

        activity('hak_akses')
            ->event('deleted')
            ->performedOn($item)
            ->causedBy($user)
            ->withProperties([
                'old' => $old,
            ])
            ->log("Hak Akses \"{$item->HAKAKSES}\" dinonaktifkan");

        $fromMap = $r->boolean('from_map') ||
        str_contains(url()->previous(), "/admin/hak-akses/{$id}/modul");

        if ($fromMap) {
            $next = HakAkses::where('STATUS', 1)->orderBy('ID')->first();
            return $next
    ? redirect()
        ->route('admin.hakakses.modul.edit', $next->getKey())
        ->with('swal', [
            'icon'     => 'success',
            'title'    => 'Hak akses dinonaktifkan',
            'position' => 'center', // bisa: top, top-end, bottom, dll
            'timer'    => 1500,
        ])
    : redirect()
        ->route('hakakses.index')
        ->with('swal', [
            'icon'     => 'success',
            'title'    => 'Hak akses dinonaktifkan',
            'position' => 'center',
            'timer'    => 1500,
        ]);

        }

        return redirect()
    ->route('hakakses.index')
    ->with([
        'swal' => [
            'title'    => 'Dinonaktifkan',
            'text'     => 'Hak akses berhasil dinonaktifkan.',
            'icon'     => 'success',
            'position' => 'center',
            'timer'    => 1800,
        ],
    ]);

    }

    // PATCH /admin/hak-akses/{id}/toggle
    public function toggleStatus($id)
    {
        $item = HakAkses::findOrFail($id);

        $oldStatus    = $item->STATUS;
        $item->STATUS = ! $item->STATUS;
        $item->save();

        /* ================= ACTIVITY LOG ================= */
        $user = Pengguna::find(session('auth_uid'));

        activity('hak_akses')
            ->event('updated')
            ->performedOn($item)
            ->causedBy($user)
            ->withProperties([
                'id'        => $item->ID,
                'nama'      => $item->HAKAKSES,
                'oldStatus' => $oldStatus,
                'newStatus' => $item->STATUS,
            ])
            ->log("Status Hak Akses \"{$item->HAKAKSES}\" diubah");

        return back()->with('success', 'Status diperbarui.');
    }
}
