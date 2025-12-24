<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sdt;
use App\Models\DtSdt;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\StatusPenyampaian;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

        $query = DtSdt::where('ID_SDT', $id)
            ->where('PETUGAS_SDT', $user_id)->with('latestStatus', 'sdt');

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


            //JIKA sdt belum tanggal saat ini lebih kecil dari tanggal mulai di tabel sdt maka expired juga
            if ($sdt && $sdt->TGL_MULAI) {
                // 1. Parsing tanggal agar menjadi object Carbon
                $tglMulai = \Carbon\Carbon::parse($sdt->TGL_MULAI)->startOfDay();
                $today = \Carbon\Carbon::now()->startOfDay();

                // 2. Logika: Jika hari ini < tanggal mulai, maka expired
                if ($today->lt($tglMulai)) {
                    // lt() artinya Less Than (Lebih Kecil)
                    $row->expired = '1';
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
        // 1. Validasi awal (seperti sebelumnya)
        $validator = Validator::make($request->all(), [
            'KO' => 'required',
            'ID_SDT' => 'required',
            'STATUS' => 'required',
            'NOP_BENAR' => 'required',
            'STATUS_OP' => 'required',
            'STATUS_WP' => 'required',
            'LATITUDE_KO' => 'required',
            'LONGITUDE_KO' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Validasi Gagal: ' . $validator->errors()->first())->withInput();
        }

        $userId = session('auth_uid');
        $now = now(); // Jam saat ini
        $KETERANGAN_PETUGAS = $request->input('KETERANGAN_PETUGAS', '-');

        DB::beginTransaction();
        try {
            // 2. Ambil semua ID yang akan diproses
            $rows = DtSdt::where('ID_SDT', $request->ID_SDT)
                ->where('ALAMAT_OP', 'LIKE', $request->KO . '%')
                ->where('PETUGAS_SDT', $userId)
                ->pluck('ID');

            $sdt = Sdt::where('ID', $request->ID_SDT)
                ->select('TGL_MULAI', 'TGL_SELESAI')
                ->first();

            if ($sdt) {
                $today = Carbon::now(); // Mengambil tanggal & waktu saat ini

                // 2. Pastikan kolom di-parse ke Carbon agar bisa dibandingkan
                $mulai = Carbon::parse($sdt->TGL_MULAI)->startOfDay();
                $selesai = Carbon::parse($sdt->TGL_SELESAI)->endOfDay();

                // 3. Cek apakah hari ini berada di antara (between) mulai dan selesai
                if (!$today->between($mulai, $selesai)) {
                    return back()->with('error', "SDT sudah tidak aktif pada tanggal saat ini.");
                }
            }



            if ($rows->isEmpty()) {
                return back()->with('error', 'Data KO tidak ditemukan.');
            }

            // 3. 🛡️ PENGECEKAN EXPIRED (6 JAM)
            // Cari apakah ada data yang sudah di-input sebelumnya
            // DAN created_at nya sudah lebih lama dari 6 jam yang lalu.
            $sixHoursAgo = now()->subHours(6);

            $expiredData = StatusPenyampaian::whereIn('ID_DT_SDT', $rows)
                ->where('created_at', '<', $sixHoursAgo)
                ->first(); // Kita ambil satu saja untuk sampel pengecekan

            if ($expiredData) {
                // Hitung sudah berapa jam berlalu untuk pesan error yang lebih informatif
                $diffInHours = $expiredData->created_at->diffInHours($now);

                return back()->with('error', "Data KO {$request->KO} sudah tidak bisa diupdate massal karena sudah $diffInHours jam berlalu (Batas 6 jam). Silakan update satu-persatu.");
            }

            // 4. Proses Simpan Foto (Logic Storage)
            $imagePath = null;
            if ($request->filled('FOTO_BASE64_KO')) {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->FOTO_BASE64_KO));
                $fileName = Str::random(20) . '.jpg';
                $fullPath = "EVIDENCE/" . $now->format('Y/m') . "/" . $fileName;
                Storage::disk('public')->put($fullPath, $imageData);
                $imagePath = $fullPath;
            }

            // 5. Mapping Status Angka (1-4)
            $statusWP = match ($request->STATUS_WP) {
                'Belum Diproses Petugas' => 1,
                'Ditemukan' => 2,
                'Tidak Ditemukan' => 3,
                'Luar Kota' => 4,
                default => 0
            };

            $statusOP = match ($request->STATUS_OP) {
                'Belum Diproses Petugas' => 1,
                'Ditemukan' => 2,
                'Tidak Ditemukan' => 3,
                'Sudah Dijual' => 4,
                default => 0
            };

            $STATUS_PENYAMPAIAN = ($request->STATUS == 'TERSAMPAIKAN') ? 1 : 0;
            $koordinat = $request->LATITUDE_KO . ', ' . $request->LONGITUDE_KO;

            // 6. Susun Payload & Jalankan Upsert
            $payload = [];
            foreach ($rows as $dtId) {
                $payload[] = [
                    'ID_DT_SDT'          => $dtId,
                    'ID_PETUGAS'         => $userId,
                    'STATUS_PENYAMPAIAN' => $STATUS_PENYAMPAIAN,
                    'STATUS_OP'          => $statusOP,
                    'STATUS_WP'          => $statusWP,
                    'NOP_BENAR'          => $request->NOP_BENAR,
                    'EVIDENCE'           => $imagePath,
                    'KOORDINAT_OP'       => $koordinat,
                    'NAMA_PENERIMA'      => $request->NAMA_PENERIMA,
                    'HP_PENERIMA'        => $request->HP_PENERIMA,
                    'TGL_PENYAMPAIAN'    => $now,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                    'KETERANGAN_PETUGAS' => $KETERANGAN_PETUGAS,
                ];
            }

            StatusPenyampaian::upsert(
                $payload,
                ['ID_DT_SDT'],
                [
                    'ID_PETUGAS', 'STATUS_PENYAMPAIAN', 'STATUS_OP', 'STATUS_WP',
                    'NOP_BENAR', 'EVIDENCE', 'KOORDINAT_OP', 'NAMA_PENERIMA',
                    'HP_PENERIMA', 'TGL_PENYAMPAIAN', 'updated_at', 'KETERANGAN_PETUGAS'
                ]
            );

            DB::commit();
            return back()->with('success', "Berhasil update massal " . count($payload) . " data.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function massUpdateNOP(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'NOP'          => 'required',
            'ID_SDT'        => 'required',
            'STATUS'        => 'required',
            'NOP_BENAR'     => 'required',
            'STATUS_OP'     => 'required',
            'STATUS_WP'     => 'required',
            'LATITUDE_NOP'  => 'required',
            'LONGITUDE_NOP' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Validasi Gagal: ' . $validator->errors()->first())->withInput();
        }

        $userId = session('auth_uid');
        $now = now();
        $KETERANGAN_PETUGAS = $request->input('KETERANGAN_PETUGAS', '-');

        DB::beginTransaction();
        try {
            // 2. Ambil semua ID DT_SDT yang masuk dalam kriteria NOP
            $rows = DtSdt::where('ID_SDT', $request->ID_SDT)
                ->where('NOP', 'LIKE', $request->NOP . '%')
                ->where('PETUGAS_SDT', $userId)
                ->pluck('ID');


            if ($rows->isEmpty()) {
                return back()->with('error', 'Data NOP tidak ditemukan.');
            }

            $sdt = Sdt::where('ID', $request->ID_SDT)
                ->select('TGL_MULAI', 'TGL_SELESAI')
                ->first();

            if ($sdt) {
                $today = Carbon::now(); // Mengambil tanggal & waktu saat ini

                // 2. Pastikan kolom di-parse ke Carbon agar bisa dibandingkan
                $mulai = Carbon::parse($sdt->TGL_MULAI)->startOfDay();
                $selesai = Carbon::parse($sdt->TGL_SELESAI)->endOfDay();

                // 3. Cek apakah hari ini berada di antara (between) mulai dan selesai
                if (!$today->between($mulai, $selesai)) {
                    return back()->with('error', "SDT sudah tidak aktif pada tanggal saat ini.");
                }
            }

            // 3. Pengecekan Expired 6 Jam
            $sixHoursAgo = now()->subHours(6);
            $expiredData = StatusPenyampaian::whereIn('ID_DT_SDT', $rows)
                ->where('created_at', '<', $sixHoursAgo)
                ->first();

            if ($expiredData) {
                return back()->with('error', "Data sudah tidak bisa diupdate massal (Sudah lewat 6 jam).");
            }

            // 4. Simpan Foto
            $imagePath = null;
            if ($request->filled('FOTO_BASE64_NOP')) {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->FOTO_BASE64_NOP));
                $fileName = Str::random(20) . '.jpg';
                $imagePath = "EVIDENCE/" . $now->format('Y/m') . "/" . $fileName;
                Storage::disk('public')->put($imagePath, $imageData);
            }

            // 5. Mapping Data
            $statusPenyampaian = ($request->STATUS == 'TERSAMPAIKAN') ? 1 : 0;
            $koordinat = $request->LATITUDE_NOP . ', ' . $request->LONGITUDE_NOP;

            // 6. LOOPING updateOrCreate (Cara Alternatif selain Upsert)
            foreach ($rows as $dtId) {
                StatusPenyampaian::updateOrCreate(
                    // Parameter 1: Kolom kunci untuk mencari data (Harus ID_DT_SDT)
                    ['ID_DT_SDT' => $dtId],

                    // Parameter 2: Data yang akan disimpan/diperbarui
                    [
                        'ID_PETUGAS'         => $userId,
                        'ID_DT_SDT'             => $dtId, // Jika kolom ini ada
                        'STATUS_PENYAMPAIAN' => $statusPenyampaian,
                        'STATUS_OP'          => match ($request->STATUS_OP) {
                            'Belum Diproses Petugas' => 1,
                            'Ditemukan' => 2,
                            'Tidak Ditemukan' => 3,
                            'Sudah Dijual' => 4,
                            default => 0
                        },
                        'STATUS_WP'          => match ($request->STATUS_WP) {
                            'Belum Diproses Petugas' => 1,
                            'Ditemukan' => 2,
                            'Tidak Ditemukan' => 3,
                            'Luar Kota' => 4,
                            default => 0
                        },
                        'NOP_BENAR'          => $request->NOP_BENAR,
                        'EVIDENCE'           => $imagePath,
                        'KOORDINAT_OP'       => $koordinat,
                        'NAMA_PENERIMA'      => ($statusPenyampaian == 1) ? $request->NAMA_PENERIMA : null,
                        'HP_PENERIMA'        => ($statusPenyampaian == 1) ? $request->HP_PENERIMA : null,
                        'TGL_PENYAMPAIAN'    => $now,
                        'KETERANGAN_PETUGAS' => $KETERANGAN_PETUGAS,
                        'updated_at'         => $now,
                        // created_at tidak perlu diisi di sini karena updateOrCreate
                        // otomatis mengisinya HANYA saat data baru dibuat (INSERT).
                    ]
                );
            }

            DB::commit();
            return back()->with('success', "Berhasil memproses " . count($rows) . " data NOP.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            ->route('petugas.sdt.detail', $row->ID)
            ->with('success', 'Data berhasil disimpan.');
    }
}
