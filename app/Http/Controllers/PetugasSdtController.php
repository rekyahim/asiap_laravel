<?php

namespace App\Http\Controllers;

use App\Models\DtSdt;
use App\Models\Sdt;
use App\Models\StatusPenyampaian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetugasSdtController extends Controller
{

    /* =========================================================================
        INDEX
        ========================================================================= */
    public function index(Request $r): View
    {
        $user_id = session('auth_uid');

        $master = DB::table('sdt')
            ->select([
                'sdt.ID',
                'sdt.NAMA_SDT',
                'sdt.TGL_MULAI',
                'sdt.TGL_SELESAI',
                'sdt.STATUS_SDT',
                'sdt.STATUS',

                // 1. SUBQUERY: Hitung Total NOP (Khusus User Ini & STATUS Detail = 1)
                DB::raw("(
            SELECT COUNT(id)
            FROM dt_sdt
            WHERE dt_sdt.ID_SDT = sdt.ID
            AND dt_sdt.PETUGAS_SDT = '$user_id'
            AND dt_sdt.STATUS = 1   -- Filter Tambahan
        ) as JUMLAH_NOP"),

                // 2. SUBQUERY: Hitung Progress (Khusus User Ini & STATUS Detail = 1)
                DB::raw("(
            SELECT COUNT(a.id)
            FROM dt_sdt a
            JOIN status_penyampaian b ON b.ID_DT_SDT = a.ID
            WHERE a.ID_SDT = sdt.ID
            AND a.PETUGAS_SDT = '$user_id'
            AND a.STATUS = 1       -- Filter Tambahan
        ) as SUDAH_DIPROSES")
            ])
            // 3. MAIN FILTER: Pastikan Master SDT Aktif (STATUS = 1)
            ->where('sdt.STATUS', 1)

            // 4. EXISTS FILTER: User punya tugas aktif di SDT ini
            ->whereExists(function ($query) use ($user_id) {
                $query->select(DB::raw(1))
                    ->from('dt_sdt')
                    ->whereColumn('dt_sdt.ID_SDT', 'sdt.ID')
                    ->where('dt_sdt.PETUGAS_SDT', $user_id)
                    ->where('dt_sdt.STATUS', 1); // Filter Tambahan
            })
            ->orderBy('sdt.ID', 'DESC')
            ->get();

        // --- Logic Matematika Persentase (Sama seperti sebelumnya) ---
        $master->transform(function ($item) {
            if ($item->JUMLAH_NOP > 0) {
                $item->PROGRESS = round(($item->SUDAH_DIPROSES / $item->JUMLAH_NOP) * 100, 1);
            } else {
                $item->PROGRESS = 0;
            }

            $item->TGL_MULAI_FMT = $item->TGL_MULAI ? date('d M Y', strtotime($item->TGL_MULAI)) : '-';
            $item->TGL_SELESAI_FMT = $item->TGL_SELESAI ? date('d M Y', strtotime($item->TGL_SELESAI)) : '-';

            return $item;
        });

        return view('petugas.sdt-index', compact('master'));
    }


    /* =========================================================================
    DETAIL PER SDT
    ========================================================================= */
    public function detail(Request $req, int $id): View|RedirectResponse
    {
        $user_id = session('auth_uid');
        $sdt = Sdt::find($id);

        if (!$sdt) {
            return redirect()
                ->route('petugas.sdt.index')
                ->with('error', "Data SDT dengan ID {$id} tidak ditemukan.");
        }

        /* ============================================================
        QUERY TABEL (MENGIKUTI FILTER)
        MENGGUNAKAN with('latestStatus') UNTUK PERFORMA
        ============================================================ */
        //perbaiki

        $query = DtSdt::where('ID_SDT', $id)->where('PETUGAS_SDT', $user_id)->with('latestStatus', 'sdt');

        if ($req->filled('search')) {
            $search = $req->search;
            $query->where(function($q) use ($search) {
                $q->where('NOP', 'like', "%{$search}%")
                ->orWhere('NAMA_WP', 'like', "%{$search}%");
            });
        }


        if ($req->filled('nop')) {
            $query->where('NOP', 'like', "%{$req->nop}%");
        }
        if ($req->filled('tahun')) {
            $query->where('TAHUN', $req->tahun);
        }
        if ($req->filled('nama')) {
            $query->where('NAMA_WP', 'like', "%{$req->nama}%");
        }

        // harus pakai ID Petugas
        $rows = $query->orderBy('ID')->paginate(20)->withQueryString();



        // ==== HITUNG EXPIRED UNTUK TOMBOL UPDATE ====
        foreach ($rows as $row) {
            $status = $row->latestStatus;
            $sdt = $row->sdt;
            $row->expired = '2'; //belum
            if ($status && $status->created_at) {

                // 1. Define the past date (e.g., from a database)
                $past_date_string = $status->created_at;

                // 2. Convert dates to timestamps
                $past_timestamp = strtotime($past_date_string);
                $now_timestamp = time(); // or strtotime('now')

                // 3. Calculate the difference in seconds
                $difference_seconds = $now_timestamp - $past_timestamp;

                // 4. Define the 6-hour threshold in seconds (6 hours * 60 minutes * 60 seconds)
                $six_hours_in_seconds = 6 * 60 * 60; // 21600 seconds

                // 5. Compare the difference
                if ($difference_seconds > $six_hours_in_seconds) {
                    $row->expired = '1'; //udh expired
                }
            }

            if ($sdt && $sdt->TGL_SELESAI) {
                // 1. Define the past date (e.g., from a database)
                $past_date_string = $sdt->TGL_SELESAI;

                // 2. Convert dates to timestamps
                $past_timestamp = strtotime($past_date_string);
                $now_timestamp = time(); // or strtotime('now')

                // 3. Calculate the difference in seconds
                $difference_seconds = $now_timestamp - $past_timestamp;

                // 4. Define the 6-hour threshold in seconds (6 hours * 60 minutes * 60 seconds)
                $six_hours_in_seconds = 6 * 60 * 60; // 21600 seconds

                // 5. Compare the difference
                if ($difference_seconds > $six_hours_in_seconds) {
                    $row->expired = '1'; //udh expired
                }
            }
        }

        //dd($rows);
        /* ============================================================
        DATA KO (TIDAK TERPENGARUH FILTER)
        ============================================================ */
        $dataKO = DtSdt::where('ID_SDT', $id)
            ->where('ALAMAT_OP', 'LIKE', 'KO%')
            ->where('PETUGAS_SDT', $user_id)
            ->select('ALAMAT_OP')
            ->distinct()
            ->orderBy('ALAMAT_OP')
            ->get();

        /* ============================================================
        DATA NOP (UNTUK MODAL PILIH NOP)
        ============================================================ */
        $dataNOP = DtSdt::where('ID_SDT', $id)
            ->where('PETUGAS_SDT', $user_id)
            ->whereNotNull('NOP')
            ->where('NOP', '!=', '')
            ->distinct('NOP')
            ->orderBy('NOP')
            ->get(['NOP']);

        /* ============================================================
        TOTAL NOP & TERSAMPAIKAN (UNTUK SUMMARY KPI)
            ============================================================ */
        $totalNOP = DtSdt::where('ID_SDT', $id)
            ->where('PETUGAS_SDT', $user_id)
            ->whereNotNull('NOP')
            ->where('NOP', '!=', '')
            ->distinct('NOP')
            ->count();

        $tersampaikan = StatusPenyampaian::whereIn(
            'ID_DT_SDT',
            DtSdt::where('ID_SDT', $id)->where('PETUGAS_SDT', $user_id)->pluck('ID')
        )

            ->distinct('ID_DT_SDT')
            ->count();

        /* ============================================================
        SUMMARY (KPI)
            ============================================================ */
        $summary = [
            'total' => $totalNOP,
            'tersampaikan' => $tersampaikan,
            'belum' => $totalNOP - $tersampaikan,
            'progress' => $totalNOP ? round(($tersampaikan / $totalNOP) * 100, 2) : 0,
        ];

        /* ============================================================
        TOTAL BIAYA
            ============================================================ */
        $totalBiaya = DtSdt::where('ID_SDT', $id)
            ->where('PETUGAS_SDT', $user_id)
            // ->whereIn('ALAMAT_OP', $dataKO->pluck('ALAMAT_OP'))
            ->sum('PBB_HARUS_DIBAYAR');

        /* ============================================================
        RETURN KE VIEW
         ============================================================ */

         $ID_SDT = $id;
        return view('petugas.sdt-detail', compact(
            'sdt',
            'rows',
            'summary',
            'dataKO',
            'totalBiaya',
            'dataNOP', // <-- tambahkan ini supaya dropdown NOP di modal tidak error
            'ID_SDT'
        ));
    }


    /* =========================================================================
        MASS UPDATE - BARU DITAMBAHKAN
        (Menggantikan metode yang hilang/salah ketik)
        ========================================================================= */
    /**
     * Handle mass update for NOP_BENAR or KOORDINAT_OP based on a list of NOPs.
     */
    public function komplekMassUpdate(Request $request): RedirectResponse
    {

        $request->validate([
            'sdt_id' => 'required|exists:sdt,ID',
            'action_type' => 'required|in:ko,nop,status_ko,status_nop',
            'list_nop' => 'required|string',

            // khusus mode lama
            'koordinat' => 'nullable|required_if:action_type,ko|string',
            'nop_benar_baru' => 'nullable|required_if:action_type,nop|in:YA,TIDAK',

            // khusus status
            'STATUS_PENYAMPAIAN' => 'nullable|required_if:action_type,status_ko,status_nop',
        ]);

        $sdtId      = $request->sdt_id;
        $actionType = $request->action_type;
        $userId     = auth()->user()->ID_PENGGUNA;
        $now        = now();

        // Pecah list NOP (textarea / multiline)
        $listNop = array_filter(array_map(
            'trim',
            preg_split('/\r\n|\r|\n/', $request->list_nop)
        ));

        if (empty($listNop)) {
            return back()->with('error', 'Daftar NOP tidak valid.');
        }

        try {
            DB::beginTransaction();

            // Ambil semua DT_SDT ID berdasarkan NOP
            $dtSdtIds = DtSdt::where('ID_SDT', $sdtId)
                ->whereIn('NOP', $listNop)
                ->pluck('ID');

            if ($dtSdtIds->isEmpty()) {
                throw new \Exception('Data NOP tidak ditemukan di sistem.');
            }

            $countUpdated = 0;

            foreach ($dtSdtIds as $dtSdtId) {

                // Data dasar (selalu ada)
                $updateData = [
                    'ID_SDT'          => $sdtId,
                    'ID_PETUGAS'      => $userId,
                    'TGL_PENYAMPAIAN' => $now,
                ];

                /* ===============================
               MODE UPDATE
            =============================== */
                if ($actionType === 'ko') {
                    // Update Koordinat OP
                    $updateData['KOORDINAT_OP'] = $request->koordinat;
                } elseif ($actionType === 'nop') {
                    // Update NOP Benar
                    $updateData['NOP_BENAR'] = $request->nop_benar_baru;
                } elseif (in_array($actionType, ['status_ko', 'status_nop'])) {
                    // Update Status Penyampaian
                    $updateData['STATUS_PENYAMPAIAN'] = $request->STATUS_PENYAMPAIAN;
                }

                // Simpan / update status penyampaian
                StatusPenyampaian::updateOrCreate(
                    ['ID_DT_SDT' => $dtSdtId],
                    $updateData
                );

                $countUpdated++;
            }

            DB::commit();

            $msgLabel = match ($actionType) {
                'ko' => 'Koordinat OP',
                'nop' => 'Status NOP Benar',
                'status_ko' => 'Status Penyampaian (KO)',
                'status_nop' => 'Status Penyampaian (NOP)',
            };

            return redirect()
                ->route('petugas.sdt.detail', $sdtId)
                ->with('success', "Berhasil memperbarui {$msgLabel} pada {$countUpdated} data.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    // FUNCTION KO \\

    public function updateStatusByKO(Request $request): RedirectResponse
    {
        $request->validate([
            'ID_SDT' => 'required|exists:sdt,ID',
            'KO' => 'required|string',
            'STATUS_PENYAMPAIAN' => 'required'
        ]);

        $userId = auth()->user()->ID_PENGGUNA;
        $now = now();

        DB::beginTransaction();
        try {

            // Ambil semua dt_sdt dengan KO yang sama
            $rows = DtSdt::where('ID_SDT', $request->ID_SDT)
                ->where('ALAMAT_OP', $request->KO)
                ->where('PETUGAS_SDT', $userId)
                ->get();

            if ($rows->isEmpty()) {
                throw new \Exception('Data KO tidak ditemukan.');
            }

            foreach ($rows as $row) {

                StatusPenyampaian::updateOrCreate(
                    [
                        'ID_DT_SDT' => $row->ID
                    ],
                    [
                        'ID_SDT'             => $row->ID_SDT,
                        'ID_PETUGAS'         => $userId,
                        'STATUS_PENYAMPAIAN' => $request->STATUS_PENYAMPAIAN,
                        'TGL_PENYAMPAIAN'    => $now,
                    ]
                );
            }

            DB::commit();

            return back()->with(
                'success',
                'Status penyampaian berhasil diperbarui untuk seluruh KO.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    //KO
   public function massUpdateKO(Request $request): RedirectResponse
{
    $userId = session('auth_uid');
    $now    = now();

    DB::beginTransaction();

    try {
        // Ambil semua DT_SDT dengan KO yang sama dan milik user
        $rows = DtSdt::where('ID_SDT', $request->ID_SDT)
            ->where('ALAMAT_OP', 'LIKE', $request->KO . '%') // semua KO yang dimulai dengan input
            ->where('PETUGAS_SDT', $userId)
            ->pluck('ID');

        if ($rows->isEmpty()) {
            return back()->with('error', 'Data KO tidak ditemukan.');
        }

        // Default status jika kosong
        $status = $request->STATUS_PENYAMPAIAN ?? 'Belum';

        $payload = [];

        foreach ($rows as $dtId) {
            $existing = StatusPenyampaian::where('ID_DT_SDT', $dtId)->first();

            if ($existing) {
                // Hitung selisih jam
                $createdDiff = $existing->created_at->diffInHours($now);
                $updatedDiff = $existing->updated_at->diffInHours($now);

                // Lewat 6 jam, skip update
                if ($createdDiff >= 6 || $updatedDiff >= 6) {
                    continue;
                }

                // Update record existing
                $payload[] = [
                    'ID_DT_SDT'          => $dtId,
                    'ID_SDT'             => $request->ID_SDT,
                    'ID_PETUGAS'         => $userId,
                    'STATUS_PENYAMPAIAN' => $status,
                    'TGL_PENYAMPAIAN'    => $now,
                    'created_at'         => $existing->created_at, // tetap sama
                    'updated_at'         => $now, // diupdate setiap kali update
                ];
            } else {
                // Insert baru
                $payload[] = [
                    'ID_DT_SDT'          => $dtId,
                    'ID_SDT'             => $request->ID_SDT,
                    'ID_PETUGAS'         => $userId,
                    'STATUS_PENYAMPAIAN' => $status,
                    'TGL_PENYAMPAIAN'    => $now,
                    'created_at'         => $now, // pertama kali insert
                    'updated_at'         => $now, // pertama kali insert
                ];
            }
        }

        // Upsert batch
        if (!empty($payload)) {
            StatusPenyampaian::upsert(
                $payload,
                ['ID_DT_SDT'], // UNIQUE KEY
                ['ID_PETUGAS', 'STATUS_PENYAMPAIAN', 'TGL_PENYAMPAIAN', 'updated_at']
            );
        }

        DB::commit();

        return back()->with(
            'success',
            'Mass KO berhasil (' . count($payload) . ' data diperbarui/ditambahkan)'
        );

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error($e->getMessage());
        return back()->with('error', $e->getMessage());
    }
}


    // FUNCTION NOP \\

    public function updateStatusByNOP(Request $request): RedirectResponse
    {
        $request->validate([
            'ID_SDT' => 'required|exists:sdt,ID',
            'NOP' => 'required|string',
            'STATUS_PENYAMPAIAN' => 'required'
        ]);

        $userId = auth()->user()->ID_PENGGUNA;
        $now = now();

        DB::beginTransaction();
        try {

            // Ambil semua dt_sdt dengan NOP yang sama
            $rows = DtSdt::where('ID_SDT', $request->ID_SDT)
                ->where('NOP', $request->NOP)
                ->where('PETUGAS_SDT', $userId)
                ->get();

            if ($rows->isEmpty()) {
                throw new \Exception('Data NOP tidak ditemukan.');
            }

            foreach ($rows as $row) {

                StatusPenyampaian::updateOrCreate(
                    [
                        'ID_DT_SDT' => $row->ID
                    ],
                    [
                        'ID_SDT'             => $row->ID_SDT,
                        'ID_PETUGAS'         => $userId,
                        'STATUS_PENYAMPAIAN' => $request->STATUS_PENYAMPAIAN,
                        'TGL_PENYAMPAIAN'    => $now,
                    ]
                );
            }

            DB::commit();

            return back()->with(
                'success',
                'Status penyampaian berhasil diperbarui untuk seluruh NOP.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function getDetailNOP(Request $request)
    {
        $request->validate([
            'sdt_id' => 'required|exists:sdt,ID',
            'nop' => 'required|string'
        ]);

        $sdtId = $request->sdt_id;
        $nop   = $request->nop;

        // Data utama (1 baris)
        $utama = DtSdt::where('ID_SDT', $sdtId)
            ->where('NOP', $nop)
            ->first();

        if (!$utama) {
            return response()->json(['message' => 'NOP tidak ditemukan'], 404);
        }

        // Semua tahun
        $tahun = DtSdt::where('ID_SDT', $sdtId)
            ->where('NOP', $nop)
            ->orderBy('TAHUN')
            ->pluck('TAHUN')
            ->unique()
            ->values();

        return response()->json([
            'nop'       => $utama->NOP,
            'nama_wp'   => $utama->NAMA_WP,
            'alamat_op' => $utama->ALAMAT_OP,
            'tahun'     => $tahun
        ]);
    }



    /* =========================================================================
        SHOW A ROW
        ========================================================================= */
    public function showPage(Request $r, int $id): View|RedirectResponse
    {


        $row = DtSdt::find($id);
        if (!$row) {
            return redirect()->route('petugas.sdt.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        $sp = StatusPenyampaian::where('ID_DT_SDT', $row->ID)
            ->latest('id')
            ->with('petugas')
            ->first();

        $photos = [];
        $raw = (string)($sp->EVIDENCE ?? $row->EVIDENCE ?? '');

        if ($raw) {
            foreach (preg_split('/[|,]+/', $raw) as $p) {
                $p = trim($p);
                if ($p && Storage::disk('public')->exists($p)) {
                    $photos[] = Storage::url($p);
                }
            }
        }

        $dtsdt_history = DB::table('dt_sdt')
            ->join('status_penyampaian', 'dt_sdt.ID', '=', 'status_penyampaian.ID_DT_SDT')
            ->join('pengguna', 'dt_sdt.PETUGAS_SDT', '=', 'pengguna.ID')
            ->where('dt_sdt.NOP', $row->NOP)
            ->where('dt_sdt.ID', '!=', $row->ID)
            ->where('dt_sdt.ID', '<', $row->ID)
            ->orderByDesc('dt_sdt.ID')
            ->get();




        return view('petugas.sdt-show', [
            'row' => $row,
            'photos' => $photos,
            'konfirmasi' => $sp,
            'historydt' => $dtsdt_history,
            'return' => $r->query('return'),
        ]);
    }


    /* =========================================================================
   EDIT (FINAL – CEK 6 JAM DARI UPDATED_AT TERAKHIR)
   ========================================================================= */
    public function edit(Request $r, int $id)
    {
        $row = DtSdt::find($id);

        if (!$row) {
            return redirect()
                ->route('petugas.sdt.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        // Ambil status terakhir
        $status = StatusPenyampaian::where('ID_DT_SDT', $row->ID)
            ->orderByDesc('updated_at')
            ->first();

        $expired = false;
        if ($status && $status->updated_at) {
            $expired = now()->diffInHours($status->updated_at) >= 6;
        }

        return view('petugas.sdt-edit', [
            'row'     => $row,
            'status' => $status,
            'master'  => Sdt::orderByDesc('ID')->get(['ID', 'NAMA_SDT']),
            'expired' => $expired,
        ]);
    }

    /* =========================================================================
   UPDATE (FINAL – 6 JAM TIDAK MERUBAH WAKTU)
   ========================================================================= */
    public function update(Request $request, int $id)
    {

        $data = $request->validate([
            'STATUS_PENYAMPAIAN' => 'nullable|string',
            'STATUS_OP'          => 'nullable|string',
            'STATUS_WP'          => 'nullable|string',
            'NOP_BENAR'          => 'nullable|string|max:64',
            'NAMA_PENERIMA'      => 'nullable|string|max:150',
            'HP_PENERIMA'        => 'nullable|string|max:20',
            'KETERANGAN'         => 'nullable|string',
            'FOTO_BASE64'        => 'nullable|string',
            'KOORDINAT_OP'       => 'nullable|string',
        ]);

        $row = DtSdt::find($id);
        if (!$row) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        $status = StatusPenyampaian::where('ID_DT_SDT', $row->ID)->first();

        // ❌ BLOK UPDATE jika lewat 6 jam dari created_at pertama
        if ($status && now()->diffInHours($status->created_at) >= 6) {
            return back()->with('error', 'Data sudah terkunci (lebih dari 6 jam).');
        }

        $statusPenyampaian = in_array(strtoupper($data['STATUS_PENYAMPAIAN'] ?? ''), [
            '1', 'Y', 'YA', 'OK', 'TRUE', 'T', '✓', 'TERSAMPAIKAN'
        ]) ? 1 : 0;

        if ($data['STATUS_WP']  == 'Belum Diproses Petugas') {
            $statusWP = 1;
        } else if ($data['STATUS_WP']  == 'Ditemukan') {
            $statusWP = 2;
        } else if ($data['STATUS_WP']  == 'Tidak Ditemukan') {
            $statusWP = 3;
        } else if ($data['STATUS_WP']  == 'Luar Kota') {
            $statusWP = 4;
        }
        if ($data['STATUS_OP'] == 'Belum Diproses Petugas') {
            $statusOP = 1;
        } else if ($data['STATUS_OP'] == 'Ditemukan') {
            $statusOP = 2;
        } else if ($data['STATUS_OP'] == 'Tidak Ditemukan') {
            $statusOP = 3;
        } else if ($data['STATUS_OP'] == 'Sudah Dijual') {
            $statusOP = 4;
        }


        $path = $status->EVIDENCE ?? '-';
        if (!empty($data['FOTO_BASE64']) && str_starts_with($data['FOTO_BASE64'], 'data:image')) {
            [$meta, $content] = explode(',', $data['FOTO_BASE64'], 2);
            $ext = str_contains($meta, 'png') ? 'png'
                : (str_contains($meta, 'webp') ? 'webp' : 'jpg');
            $folder = 'evidence/' . date('Y/m');
            Storage::disk('public')->makeDirectory($folder);
            $path = $folder . '/' . $row->ID . '_' . time() . '.' . $ext;
            Storage::disk('public')->put($path, base64_decode($content));
        }

        if ($status) {
            // 🔒 MATIKAN timestamps supaya updated_at tidak berubah
            $status->timestamps = false;
            $status->update([
                'STATUS_PENYAMPAIAN' => $statusPenyampaian,
                'STATUS_OP'          => $statusOP,
                'STATUS_WP'          => $statusWP,
                'NOP_BENAR'          => $data['NOP_BENAR'] ?? $row->NOP,
                'KETERANGAN_PETUGAS' => $data['KETERANGAN'] ?? '',
                'EVIDENCE'           => $path,
                'KOORDINAT_OP'       => $data['KOORDINAT_OP'] ?? '0,0',
                'NAMA_PENERIMA'      => $data['NAMA_PENERIMA'] ?? '',
                'HP_PENERIMA'        => $data['HP_PENERIMA'] ?? '',
            ]);
        } else {
            StatusPenyampaian::create([
                'ID_DT_SDT'          => $row->ID,
                'ID_SDT'             => $row->ID_SDT,
                'ID_PETUGAS'         => auth()->user()->ID_PENGGUNA,
                'STATUS_PENYAMPAIAN' => $statusPenyampaian,
                'STATUS_OP'          => $statusOP,
                'STATUS_WP'          => $statusWP,
                'NOP_BENAR'          => $data['NOP_BENAR'] ?? $row->NOP,
                'KETERANGAN_PETUGAS' => $data['KETERANGAN'] ?? '',
                'EVIDENCE'           => $path,
                'KOORDINAT_OP'       => $data['KOORDINAT_OP'] ?? '0,0',
                'NAMA_PENERIMA'      => $data['NAMA_PENERIMA'] ?? '',
                'HP_PENERIMA'        => $data['HP_PENERIMA'] ?? '',
                'TGL_PENYAMPAIAN'    => now(),
            ]);
        }

        //$row->update(['STATUS' => $statusPenyampaian]);

        return redirect()
            ->route('petugas.sdt.detail', $row->ID_SDT)
            ->with('success', 'Data berhasil disimpan.');
    }

    public function massUpdateNOP(Request $request)
{
    // Validasi input
    $request->validate([
        'NOP' => 'required|string',
        'STATUS' => 'required|string',
        'NOP_BENAR' => 'required|string',
        'NAMA_PENERIMA' => 'nullable|string|max:100',
        'HP_PENERIMA' => 'nullable|string|max:20',
        'STATUS_OP' => 'nullable|string',
        'STATUS_WP' => 'nullable|string',
        'FOTO_BASE64_NOP' => 'nullable|string',
        'LATITUDE_NOP' => 'nullable|numeric',
        'LONGITUDE_NOP' => 'nullable|numeric',
    ]);

    $sdtId = $request->input('sdt_id'); // Pastikan sdt_id dikirim dari form
    $nopTerpilih = $request->input('NOP');

    // Ambil semua record dt_sdt yang sesuai ID_SDT dan NOP
    $update = DtSdt::where('ID_SDT', $sdtId)
        ->where('NOP', $nopTerpilih)
        ->update([
            'STATUS_PENYAMPAIAN' => $request->input('STATUS'),
            'NOP_BENAR' => $request->input('NOP_BENAR'),
            'NAMA_PENERIMA' => $request->input('NAMA_PENERIMA'),
            'HP_PENERIMA' => $request->input('HP_PENERIMA'),
            'STATUS_OP' => $request->input('STATUS_OP'),
            'STATUS_WP' => $request->input('STATUS_WP'),
            'FOTO_BASE64_NOP' => $request->input('FOTO_BASE64_NOP'),
            'LATITUDE_NOP' => $request->input('LATITUDE_NOP'),
            'LONGITUDE_NOP' => $request->input('LONGITUDE_NOP'),
            'UPDATED_AT' => now(), // update timestamp
        ]);

    if ($update) {
        return back()->with('success', 'Data NOP berhasil diperbarui untuk semua record yang sama.');
    } else {
        return back()->with('error', 'Tidak ada record yang diperbarui. Pastikan NOP dan SDT sudah benar.');
    }
}

}
