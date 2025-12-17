<?php
namespace App\Http\Controllers;

use App\Models\DtSdt;
use App\Models\Sdt;
use App\Models\StatusPenyampaian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class PetugasSdtController extends Controller
{
    /* =========================================================================
       INDEX
    ========================================================================= */
    public function index(Request $r): View
    {
        $master = Sdt::orderBy('ID')->get(['ID', 'NAMA_SDT', 'TGL_MULAI', 'TGL_SELESAI']);
        return view('petugas.sdt-index', compact('master'));
    }

    /* =========================================================================
       DETAIL PER SDT
    ========================================================================= */
    public function detail(Request $req, int $id): View | RedirectResponse
    {
        $sdt = Sdt::find($id);
        if (! $sdt) {
            return redirect()
                ->route('petugas.sdt.index')
                ->with('error', "Data SDT dengan ID {$id} tidak ditemukan.");
        }

        $query = DtSdt::where('ID_SDT', $id);

        if ($req->filled('nop')) {
            $query->where('NOP', 'like', "%{$req->nop}%");
        }

        if ($req->filled('tahun')) {
            $query->where('TAHUN', $req->tahun);
        }

        if ($req->filled('nama')) {
            $query->where('NAMA_WP', 'like', "%{$req->nama}%");
        }

        $rows = $query->orderBy('ID')->paginate(20)->withQueryString();

        // ================== DATA KO ==================
        $dataKO = DtSdt::where('ID_SDT', $id)
            ->where('ALAMAT_OP', 'LIKE', 'KO%')
            ->select('ALAMAT_OP')
            ->distinct()
            ->orderBy('ALAMAT_OP')
            ->get();

        $total = $dataKO->count();

        $tersampaikan = StatusPenyampaian::whereIn(
            'ID_DT_SDT',
            DtSdt::where('ID_SDT', $id)
                ->whereIn('ALAMAT_OP', $dataKO->pluck('ALAMAT_OP'))
                ->pluck('ID')
        )
            ->where('STATUS_PENYAMPAIAN', 1)
            ->count();

        $summary = [
            'total'        => $total,
            'tersampaikan' => $tersampaikan,
            'belum'        => $total - $tersampaikan,
            'progress'     => $total ? round(($tersampaikan / $total) * 100, 2) : 0,
        ];

        $totalBiaya = DtSdt::where('ID_SDT', $id)
            ->whereIn('ALAMAT_OP', $dataKO->pluck('ALAMAT_OP'))
            ->sum('PBB_HARUS_DIBAYAR');

        // ================== DATA NOP ==================
        $dataNOP = DtSdt::where('ID_SDT', $id)
            ->whereNotNull('NOP')
            ->where('NOP', '!=', '')
            ->select(DB::raw('TRIM(NOP) AS NOP'))
            ->distinct()
            ->orderBy('NOP')
            ->get();

        return view('petugas.sdt-detail', compact(
            'sdt', 'rows', 'summary', 'dataKO', 'totalBiaya', 'dataNOP'
        ));
    }

    /* =========================================================================
       SHOW A ROW
    ========================================================================= */
    public function showPage(Request $r, int $id): View | RedirectResponse
    {
        $row = DtSdt::find($id);
        if (! $row) {
            return redirect()->route('petugas.sdt.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        $sp = StatusPenyampaian::where('ID_DT_SDT', $row->ID)
            ->latest('id')
            ->with('petugas')
            ->first();

        $photos = [];
        $raw    = (string) ($sp->EVIDENCE ?? $row->EVIDENCE ?? '');

        if ($raw) {
            foreach (preg_split('/[|,]+/', $raw) as $p) {
                $p = trim($p);
                if ($p && Storage::disk('public')->exists($p)) {
                    $photos[] = Storage::url($p);
                }
            }
        }

        $lastTwoPetugas = StatusPenyampaian::where('NOP_BENAR', $row->NOP)
            ->join('dt_sdt', 'dt_sdt.ID', '=', 'status_penyampaian.ID_DT_SDT')
            ->orderByDesc('dt_sdt.TAHUN')
            ->orderByDesc('status_penyampaian.TGL_PENYAMPAIAN')
            ->select('status_penyampaian.*')
            ->take(2)
            ->with('petugas')
            ->get();

        return view('petugas.sdt-show', [
            'row'            => $row,
            'photos'         => $photos,
            'konfirmasi'     => $sp,
            'lastTwoPetugas' => $lastTwoPetugas,
            'return'         => $r->query('return'),
        ]);
    }

    /* =========================================================================
       EDIT
    ========================================================================= */
    public function edit(Request $r, int $id): View | RedirectResponse
    {
        $row = DtSdt::find($id);
        if (! $row) {
            return redirect()->route('petugas.sdt.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        $lastUpdate = $row->latestStatus->updated_at ?? $row->updated_at ?? null;
        $expired    = $lastUpdate ? now()->diffInHours($lastUpdate) >= 6 : false;

        return view('petugas.sdt-edit', [
            'row'       => $row,
            'master'    => Sdt::orderByDesc('ID')->get(['ID', 'NAMA_SDT']),
            'expired'   => $expired,
            'returnUrl' => route('petugas.sdt.detail', $row->ID_SDT),
        ]);
    }

    /* =========================================================================
       UPDATE (PENYAMPAIAN)
    ========================================================================= */
    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'STATUS_PENYAMPAIAN' => 'nullable|string',
            'STATUS_OP'          => 'nullable|string',
            'STATUS_WP'          => 'nullable|string',
            'NOP_BENAR'          => 'nullable|string|max:64',
            'NAMA_PENERIMA'      => 'nullable|string|max:150',
            'HP_PENERIMA'        => 'nullable|string|max:20',
            'KETERANGAN_PETUGAS' => 'nullable|string',
            'FOTO_BASE64'        => 'nullable|string',
            'LATITUDE'           => 'nullable|numeric',
            'LONGITUDE'          => 'nullable|numeric',
        ]);

        $row = DtSdt::find($id);
        if (! $row) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        /* ===================== NORMALISASI STATUS ===================== */

        $statusPenyampaian = in_array(strtoupper($data['STATUS_PENYAMPAIAN'] ?? ''), [
            '1', 'Y', 'YA', 'OK', 'TRUE', 'T', '✓', 'TERSAMPAIKAN',
        ]) ? 1 : 0;

        $statusOP = match (strtoupper($data['STATUS_OP'] ?? '')) {
            'DITEMUKAN'       => 1,
            'TIDAK DITEMUKAN' => 2,
            default           => 0,
        };

        $statusWP = match (strtoupper($data['STATUS_WP'] ?? '')) {
            'DITEMUKAN'       => 1,
            'TIDAK DITEMUKAN' => 2,
            default           => 0,
        };

        /* ===================== SIMPAN FOTO ===================== */

        $path = '-';

        if (! empty($data['FOTO_BASE64']) && str_starts_with($data['FOTO_BASE64'], 'data:image')) {
            [$meta, $content] = explode(',', $data['FOTO_BASE64'], 2);

            $ext = str_contains($meta, 'png')
                ? 'png'
                : (str_contains($meta, 'webp') ? 'webp' : 'jpg');

            $folder = 'evidence/' . date('Y/m');
            Storage::disk('public')->makeDirectory($folder);

            $path = $folder . '/' . $row->ID . '_' . time() . '.' . $ext;
            Storage::disk('public')->put($path, base64_decode($content));
        }

        /* ===================== KOORDINAT ===================== */

        $koordinat = (
            $request->filled('LATITUDE') && $request->filled('LONGITUDE')
        )
            ? number_format($request->LATITUDE, 6) . ',' . number_format($request->LONGITUDE, 6)
            : '0,0';

        /* ===================== SIMPAN STATUS PENYAMPAIAN ===================== */

        $exists = StatusPenyampaian::where('ID_DT_SDT', $row->ID)->exists();

        $status = StatusPenyampaian::updateOrCreate(
            ['ID_DT_SDT' => $row->ID],
            [
                'ID_SDT'             => $row->ID_SDT,
                'STATUS_PENYAMPAIAN' => $statusPenyampaian,
                'STATUS_OP'          => $statusOP,
                'STATUS_WP'          => $statusWP,
                'NOP_BENAR'          => $data['NOP_BENAR'] ?? '',
                'KETERANGAN_PETUGAS' => $data['KETERANGAN_PETUGAS'] ?? '',
                'EVIDENCE'           => $path,
                'KOORDINAT_OP'       => $koordinat,
                'NAMA_PENERIMA'      => $data['NAMA_PENERIMA'] ?? '',
                'HP_PENERIMA'        => $data['HP_PENERIMA'] ?? '',
                'TGL_PENYAMPAIAN'    => now(),
            ]
        );


        return redirect()
            ->route('petugas.sdt.detail', $row->ID_SDT)
            ->with('success', 'Data penyampaian berhasil disimpan.');
    }
    /* =========================================================================
       STORE STATUS PENYAMPAIAN (INPUT BARU)
    ========================================================================= */
    public function storeStatusPenyampaian(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_sdt'     => 'required|integer',
            'id_dt_sdt'  => 'required|integer',
            'status'     => 'required|integer',
            'keterangan' => 'nullable|string',
            'koordinat'  => 'nullable|string',
            'foto'       => 'nullable|image|max:4096',
        ]);

        $namaFile = '-';

        if ($request->hasFile('foto')) {
            $folder = 'uploads/evidence';
            Storage::disk('public')->makeDirectory($folder);

            $file     = $request->file('foto');
            $namaFile = "{$folder}/" . time() . '_' . $file->getClientOriginalName();
            Storage::disk('public')->putFileAs($folder, $file, basename($namaFile));
        }

        // 🔥 AUTO LOG: event = created
        StatusPenyampaian::create([
            'ID_SDT'             => $data['id_sdt'],
            'ID_DT_SDT'          => $data['id_dt_sdt'],
            'STATUS_PENYAMPAIAN' => $data['status'],
            'KETERANGAN'         => $data['keterangan'] ?? '',
            'KOORDINAT_OP'       => $data['koordinat'] ?? '0,0',
            'EVIDENCE'           => $namaFile,
            'TGL_PENYAMPAIAN'    => now(),
        ]);

        return back()->with('success', 'Status penyampaian berhasil ditambahkan.');
    }
}
