<?php
namespace App\Http\Controllers;

use App\Models\Modul;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModulController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q', ''));
        $show = $request->boolean('show');

        $moduls = Modul::query()
            ->when(! $show, fn($x) => $x->where('status', 1))
            ->when($q, fn($x) => $x->where(function ($w) use ($q) {
                $w->where('nama_modul', 'like', "%{$q}%")
                    ->orWhere('lokasi_modul', 'like', "%{$q}%");
            }))
            ->orderByDesc('ID') // konsisten pakai ID
            ->paginate(15)
            ->withQueryString();

        return view('admin.modul', compact('moduls'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'nama_modul'   => ['required', 'string', 'max:100'],
            'lokasi_modul' => ['required', 'string', 'max:150', 'unique:modul,lokasi_modul'],
            'tglpost'      => ['nullable', 'date'],
        ]);

        Modul::create([
            'nama_modul'   => trim($data['nama_modul']),
            'lokasi_modul' => trim($data['lokasi_modul']),
            'tglpost'      => $data['tglpost'] ?? now(),
            'status'       => 1,
        ]);
        // 🔥 LOG otomatis: event = created (dari model)

        return back()->with('swal', [
    'title' => 'Modul berhasil dibuat',
    'icon'  => 'success',
    'position' => 'center',
    'timer' => 1500,
]);
    }

public function update(Request $r, $id)
{
    $modul = Modul::findOrFail($id);

    $data = $r->validate([
        'nama_modul' => ['required', 'string', 'max:100'],
        'lokasi_modul' => [
            'required', 'string', 'max:150',
            Rule::unique('modul', 'lokasi_modul')->ignore($modul->id, 'id'),
        ],
        'tglpost' => ['nullable', 'date'],
    ]);

    $modul->update([
        'nama_modul'   => trim($data['nama_modul']),
        'lokasi_modul' => trim($data['lokasi_modul']),
        'tglpost'      => $data['tglpost'] ?? $modul->tglpost,
    ]);

    return back()->with('swal', [
    'title' => 'Modul berhasil diperbarui',
    'icon'  => 'success',
    'position' => 'center',
    'timer' => 1500,
]);
}


    public function destroy($id)
    {
        $modul = Modul::findOrFail($id);

        // 🔥 Logical delete → auto jadi event = deleted (via tapActivity di model)
        $modul->status = 0;
        $modul->save();

        return back()->with('swal', [
    'title' => 'Modul berhasil dinonaktifkan',
    'icon'  => 'success',
    'position' => 'center',
    'timer' => 1500,
]);
    }
}
