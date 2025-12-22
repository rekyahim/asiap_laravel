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

        // Menggunakan whereHas jauh lebih aman & "Laravel way"
        $master = Sdt::query()
            ->orderBy('ID')
            ->whereHas('details', function ($query) use ($user_id) {
                // 'details' adalah nama fungsi relasi di model Sdt Anda
                // Filter: hanya ambil SDT yang punya detail dengan PETUGAS_SDT = user login
                $query->where('PETUGAS_SDT', $user_id);
            })
            ->get(['ID', 'NAMA_SDT', 'TGL_MULAI', 'TGL_SELESAI']);

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
            if ($status && $status->create_at) {
                // 1. Define the past date (e.g., from a database)
                $past_date_string = $status->create_at;

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
            ->where('STATUS_PENYAMPAIAN', 1)
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
            ->whereIn('ALAMAT_OP', $dataKO->pluck('ALAMAT_OP'))
            ->sum('PBB_HARUS_DIBAYAR');

        /* ============================================================
        RETURN KE VIEW
         ============================================================ */
        return view('petugas.sdt-detail', compact(
            'sdt',
            'rows',
            'summary',
            'dataKO',
            'totalBiaya',
            'dataNOP' // <-- tambahkan ini supaya dropdown NOP di modal tidak error
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
    $sdtId = $request->input('sdt_id');

    $request->validate([
        'sdt_id' => 'required|exists:sdt,ID',
        'action_type' => 'required|in:ko,nop',
        'list_nop' => 'required|string',
        'koordinat' => 'nullable|string|required_if:action_type,ko',
        // Jika NOP_BENAR hanya pilihan YA/TIDAK, gunakan validation ini:
        'nop_benar_baru' => 'nullable|string|required_if:action_type,nop|in:YA,TIDAK',
    ]);

    $actionType = $request->input('action_type');
    $rawNops = $request->input('list_nop');

    // Bersihkan NOP dari spasi atau baris kosong
    $listNop = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $rawNops)));

    if (empty($listNop)) {
        return back()->with('error', 'Daftar NOP tidak valid.');
    }

    try {
        DB::beginTransaction();

        // Ambil semua ID sekaligus untuk meminimalkan query
        $dtSdtIds = DtSdt::where('ID_SDT', $sdtId)
            ->whereIn('NOP', $listNop)
            ->pluck('ID');

        if ($dtSdtIds->isEmpty()) {
            throw new \Exception('Data NOP tidak ditemukan di sistem.');
        }

        $countUpdated = 0;
        $userId = auth()->user()->ID_PENGGUNA;
        $now = now();

        foreach ($dtSdtIds as $dtSdtId) {
            // Data yang akan diupdate/dibuat
            $updateData = [
                'ID_SDT' => $sdtId,
                'ID_PETUGAS' => $userId,
                'TGL_PENYAMPAIAN' => $now,
            ];

            if ($actionType === 'ko') {
                $updateData['KOORDINAT_OP'] = $request->input('koordinat');
            } else {
                // Di sini diasumsikan nop_benar_baru berisi status "YA" atau "TIDAK"
                $updateData['NOP_BENAR'] = $request->input('nop_benar_baru');
            }

            // Gunakan updateOrCreate agar data lama tidak hilang, hanya kolom tertentu yang tertimpa
            StatusPenyampaian::updateOrCreate(
                ['ID_DT_SDT' => $dtSdtId],
                $updateData
            );
            $countUpdated++;
        }

        DB::commit();

        $msgLabel = ($actionType === 'ko') ? "Koordinat" : "Status NOP Benar";
        return redirect()->route('petugas.sdt.detail', $sdtId)
            ->with('success', "Berhasil memperbarui {$msgLabel} pada {$countUpdated} data.");

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
    }
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

        $lastTwoPetugas = StatusPenyampaian::where('NOP_BENAR', $row->NOP)
            ->orWhere(function ($query) use ($row) {
                $query->where('NOP_BENAR', '')
                    ->whereIn('ID_DT_SDT', DtSdt::where('NOP', $row->NOP)->pluck('ID'));
            })
            ->with('petugas', 'dtSdt') // pastikan relasi dtSdt ada
            ->where('status_penyampaian.ID', '!=', $sp->ID ?? 0)
            ->get()
            ->sortByDesc(fn ($item) => $item->dtSdt->TAHUN ?? 0)
            ->sortByDesc('TGL_PENYAMPAIAN')
            ->take(2);


        return view('petugas.sdt-show', [
            'row' => $row,
            'photos' => $photos,
            'konfirmasi' => $sp,
            'lastTwoPetugas' => $lastTwoPetugas,
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
}
