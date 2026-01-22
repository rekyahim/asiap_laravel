<?php

namespace App\Http\Controllers;

use App\Models\Sdt;
use App\Models\DtSdt;
use App\Models\Pengguna;
use App\Imports\DtSdtImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\AsiapApiService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

class SdtController extends Controller
{
    private AsiapApiService $asiap;

    public function __construct(AsiapApiService $asiap)
    {
        $this->asiap = $asiap;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user   = \App\Models\Pengguna::find(session('auth_uid'));
            $kdUnit = $user->KD_UNIT ?? null;

            // Query dasar
            $query = Sdt::query()
                ->select(['sdt.*']) // Select all agar accessor model bekerja
                ->withCount('details');

            // Filter Unit (Sama seperti logika lama)
            if ($kdUnit !== null && $kdUnit != 1) {
                $query->where('KD_UNIT', $kdUnit);
            }

            return DataTables::of($query)
                ->addIndexColumn() // Untuk nomor urut (DT_RowIndex)
                ->editColumn('TGL_MULAI', function ($row) {
                    return $row->TGL_MULAI ? $row->TGL_MULAI->format('Y-m-d') : '-';
                })
                ->editColumn('TGL_SELESAI', function ($row) {
                    return $row->TGL_SELESAI ? $row->TGL_SELESAI->format('Y-m-d') : '-';
                })
                ->addColumn('action', function ($row) {
                    // Logika tombol Delete (Disabled jika sudah disampaikan)
                    // Asumsi: 'sudah_disampaikan' adalah accessor di Model Sdt
                    $disabled = $row->sudah_disampaikan ? 'disabled' : '';

                    // Kita bangun HTML tombolnya di sini agar view bersih
                    $btnManual = '<button type="button" class="btn btn-success btn-icon btn-add-manual"
                                    data-id="' . $row->ID . '" data-nama="' . $row->NAMA_SDT . '"
                                    data-bs-toggle="modal" data-bs-target="#modalPetugasManual"
                                    title="Tambah Petugas Manual">
                                    <i class="bi bi-person-plus"></i>
                                  </button>';

                    $btnEdit = '<button type="button" class="btn btn-primary btn-icon btn-edit"
                                    data-id="' . $row->ID . '" data-nama="' . $row->NAMA_SDT . '"
                                    data-bs-toggle="modal" data-bs-target="#modalEdit"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>';

                    $urlDetail = route('sdt.detail', $row->ID);
                    $btnDetail = '<button type="button" class="btn btn-secondary btn-icon btn-detail"
                                    data-url="' . $urlDetail . '" data-bs-toggle="modal"
                                    data-bs-target="#modalDetail" title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                  </button>';

                    $urlDelete = route('sdt.destroy', $row->ID);
                    $btnDelete = '<button type="button" class="btn btn-danger btn-icon btn-delete"
                                    data-id="' . $row->ID . '" data-nama="' . $row->NAMA_SDT . '"
                                    data-url="' . $urlDelete . '" title="Hapus" ' . $disabled . '>
                                    <i class="bi bi-trash"></i>
                                  </button>';

                    return '<div class="aksi-btns d-flex align-items-center gap-2">' .
                        $btnManual . $btnEdit . $btnDetail . $btnDelete .
                        '</div>';
                })
                ->rawColumns(['action']) // Render HTML di kolom action
                ->make(true);
        }

        return view('koor.sdt-index');
    }

    public function create()
    {
        return view('koor.sdt-create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'NAMA_SDT'    => [
                'required',
                Rule::unique('sdt', 'NAMA_SDT')->where(fn ($q) => $q->where('STATUS', 1)),
            ],
            'TGL_MULAI'   => 'required|date',
            'TGL_SELESAI' => 'required|date',
            'detail_file' => 'required|file|max:10240|mimes:csv,xlsx,xls',
        ]);

        // ==============================
        // VALIDASI FILE (tidak diubah)
        // ==============================

        if ($r->hasFile('detail_file')) {
            $arrays = Excel::toArray(null, $r->file('detail_file'));
            $rows   = $arrays[0] ?? [];

            if (!count($rows)) {
                throw ValidationException::withMessages([
                    'row_errors' => ['File kosong atau rusak.'],
                ])->errorBag('import');
            }

            $headerRaw = array_map(fn ($h) => is_string($h) ? $h : (string) $h, array_shift($rows));
            $norm      = fn ($s) => strtoupper(trim(preg_replace('/\s+/', ' ', (string) $s)));

            $headToIdx = [];
            foreach ($headerRaw as $i => $h) {
                $headToIdx[$norm($h)] = $i;
            }

            $want       = ['NAMA PETUGAS', 'PETUGAS', 'PETUGAS_SDT'];
            $petugasIdx = null;

            foreach ($want as $w) {
                if (array_key_exists($norm($w), $headToIdx)) {
                    $petugasIdx = $headToIdx[$norm($w)];
                    break;
                }
            }

            if ($petugasIdx === null) {
                throw ValidationException::withMessages([
                    'row_errors' => ['Kolom NAMA PETUGAS tidak ditemukan.'],
                ])->errorBag('import');
            }

            // Validasi kapital & role tetap sama milikmu…
            // (tidak dipotong — ini full)

            $namesOriginal = [];
            $nameLines     = [];
            $notUpper      = [];

            foreach ($rows as $idx => $row) {
                $v = $row[$petugasIdx] ?? null;
                $v = is_string($v) ? trim($v) : (string) $v;

                if ($v !== '') {
                    $line              = $idx + 2;
                    $namesOriginal[$v] = true;
                    $nameLines[$v][]   = $line;

                    if ($v !== mb_strtoupper($v, 'UTF-8')) {
                        $notUpper[] = "Baris {$line}: '{$v}' wajib huruf kapital.";
                    }
                }
            }

            $names = array_keys($namesOriginal);

            if (!count($names)) {
                throw ValidationException::withMessages([
                    'row_errors' => ['Kolom NAMA PETUGAS kosong.'],
                ])->errorBag('import');
            }

            $lc         = fn ($s) => mb_strtolower($s, 'UTF-8');
            $lowerNames = array_map($lc, $names);

            $foundAny = DB::table('pengguna')
                ->selectRaw('LOWER(NAMA) AS ln')
                ->whereIn(DB::raw('LOWER(NAMA)'), $lowerNames)
                ->pluck('ln')->all();

            $foundPetugas = DB::table('pengguna')
                ->join('hak_akses', 'hak_akses.ID', '=', 'pengguna.HAKAKSES_ID')
                ->whereIn(DB::raw('LOWER(pengguna.NAMA)'), $lowerNames)
                ->whereRaw('LOWER(hak_akses.HAKAKSES)="petugas"')
                ->selectRaw('LOWER(pengguna.NAMA) AS ln')
                ->pluck('ln')->all();

            $missing   = [];
            $wrongRole = [];

            foreach ($lowerNames as $i => $ln) {
                if (!in_array($ln, $foundAny, true)) {
                    $missing[] = $names[$i];
                } elseif (!in_array($ln, $foundPetugas, true)) {
                    $wrongRole[] = $names[$i];
                }
            }

            $errors = [];
            if ($missing) {

                $errors['petugas_not_found'][] = implode(", ", $missing);
            }

            if ($wrongRole) {

                $errors['petugas_wrong_role'][] = implode(", ", $wrongRole);
            }

            if ($notUpper) {

                $errors['not_uppercase'] = $notUpper;
            }

            if ($errors) {

                throw ValidationException::withMessages($errors)->errorBag('import');
            }
        }

        // ==============================
        // SIMPAN SDT
        // ==============================

        try {
            DB::beginTransaction();

            $user = \App\Models\Pengguna::find(session('auth_uid'));

            $sdt = Sdt::create([
                'NAMA_SDT'    => $r->NAMA_SDT,
                'TGL_MULAI'   => $r->TGL_MULAI,
                'TGL_SELESAI' => $r->TGL_SELESAI,
                'KD_UNIT'     => $user?->KD_UNIT,
            ]);

            // ======================
            // LOG: CREATE SDT
            // ======================

            if ($r->hasFile('detail_file')) {
                Excel::import(new DtSdtImport($sdt->ID), $r->file('detail_file'));

                $totalRow = DtSdt::where('ID_SDT', $sdt->ID)->count();

                activity('sdt')
                    ->event('created')
                    ->performedOn($sdt)
                    ->causedBy($user)
                    ->withProperties([
                        'sdt'    => [
                            'id'      => $sdt->ID,
                            'nama'    => $sdt->NAMA_SDT,
                            'mulai'   => $sdt->TGL_MULAI,
                            'selesai' => $sdt->TGL_SELESAI,
                            'kd_unit' => $sdt->KD_UNIT,
                        ],
                        'import' => [
                            'file'      => $r->file('detail_file')->getClientOriginalName(),
                            'total_row' => $totalRow,
                        ],
                    ])
                    ->log("SDT \"{$sdt->NAMA_SDT}\" dibuat dan detail diimpor");
            }

            DB::commit();

            return redirect()
                ->route('sdt.index')
                ->with('ok', 'SDT berhasil ditambahkan' . ($r->hasFile('detail_file') ? ' & file terimpor.' : '.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withErrors(['row_errors' => [$e->getMessage()]], 'import')
                ->withInput();
        }
    }

    public function show($id)
    {

        $user = \App\Models\Pengguna::find(session('auth_uid'));
        abort_if(!$user, 403);

        $kdUnit    = $user->KD_UNIT;
        $isAdmin   = $user->HAKAKSES_ID == 1;
        $isBapenda = $kdUnit == 1;

        $sdt = Sdt::query()
            ->when(!($isAdmin || $isBapenda), fn ($q) => $q->where('KD_UNIT', $kdUnit))
            ->where('ID', $id)
            ->firstOrFail();

        $sdt->TGL_MULAI   = optional($sdt->TGL_MULAI)->format('Y-m-d');
        $sdt->TGL_SELESAI = optional($sdt->TGL_SELESAI)->format('Y-m-d');

        $rows = DtSdt::where('dt_sdt.ID_SDT', $sdt->ID)
            ->join('pengguna', 'pengguna.ID', '=', 'dt_sdt.PETUGAS_SDT')
            ->leftjoin('status_penyampaian', 'status_penyampaian.ID_DT_SDT', '=', 'dt_sdt.ID')
            ->where('dt_sdt.STATUS', 1)
            ->orderBy('dt_sdt.ID', 'asc')
            ->select('dt_sdt.*', 'pengguna.*', 'dt_sdt.ID as dtsdt_id', 'status_penyampaian.STATUS_PENYAMPAIAN as sts_penyampaian')
            ->get();
        $petugas = $rows->pluck('PETUGAS_SDT')->filter()->unique()->values();

        // Tambahkan status penyampaian
        $rows = $rows->map(function ($r) {
            $latest = \App\Models\StatusPenyampaian::where('ID_DT_SDT', $r->ID)
                ->latest('id')
                ->first();

            $r->status_penyampaian = $latest->STATUS_PENYAMPAIAN ?? null;
            return $r;
        });
        $totalPbb          = DtSdt::where('ID_SDT', $sdt->ID)->sum('PBB_HARUS_DIBAYAR');
        $totalPbbFormatted = 'Rp ' . number_format($totalPbb, 0, ',', '.');

        return view('koor.sdt-show', [
            'sdt'      => $sdt,
            'rows'     => $rows,
            'petugas'  => $petugas,
            'totalPbb' => $totalPbbFormatted,
        ]);
    }

    public function detail($id)
    {
        $user = \App\Models\Pengguna::find(session('auth_uid'));
        abort_if(!$user, 403);

        $kdUnit    = $user->KD_UNIT;
        $isAdmin   = $user->HAKAKSES_ID == 1;
        $isBapenda = $kdUnit == 1;

        // Ambil SDT sesuai hak akses
        $sdt = Sdt::query()
            ->when(!($isAdmin || $isBapenda), fn ($q) => $q->where('KD_UNIT', $kdUnit))
            ->where('ID', $id)
            ->firstOrFail();

        // Ambil detail
        $details = DtSdt::query()
            ->where('dt_sdt.ID_SDT', $sdt->ID)
            ->where('dt_sdt.STATUS', 1)
            ->leftJoin('pengguna', 'pengguna.ID', '=', 'dt_sdt.PETUGAS_SDT')
            ->orderBy('dt_sdt.ID')
            ->select([
                'dt_sdt.*',
                DB::raw('pengguna.NAMA as petugas_nama'),
            ])
            ->get();

        $petugas = $details
            ->filter(fn ($d) => filled($d->petugas_nama))
            ->map(fn ($d) => [
                'id'   => $d->PETUGAS_SDT, // ini ID
                'nama' => $d->petugas_nama,
            ])
            ->unique('id')
            ->values();

        return response()->json([
            'id'      => $sdt->ID,
            'nama'    => $sdt->NAMA_SDT,
            'mulai'   => optional($sdt->TGL_MULAI)->format('Y-m-d'),
            'selesai' => optional($sdt->TGL_SELESAI)->format('Y-m-d'),

            // daftar petugas unik
            'petugas' => $petugas,
            'rows'    => $details->map(function ($d) {

                // Ambil status penyampaian terbaru (sekali saja)
                $sp = \App\Models\StatusPenyampaian::where('ID_DT_SDT', $d->ID)
                    ->latest('id')
                    ->first();

                $statusPenyampaian = $sp
                    ? ($sp->STATUS_PENYAMPAIAN == 1 ? 'Tersampaikan' : 'Belum Tersampaikan')
                    : '-';

                $locked = $sp
                    ? ($sp->STATUS_PENYAMPAIAN == 1 || ($sp->NOP_BENAR && $sp->NOP_BENAR !== $d->NOP))
                    : false;

                return [
                    'id'                 => $d->ID,
                    'id_sdt'             => $d->ID_SDT,
                    'pengguna_id'        => $d->PENGGUNA_ID,
                    'petugas_nama'       => $d->petugas_nama,

                    // OP
                    'nop'                => preg_replace('/\D+/', '', (string) $d->NOP),
                    'tahun'              => $d->TAHUN,
                    'alamat_op'          => $d->ALAMAT_OP,
                    'blok_kav_no_op'     => $d->BLOK_KAV_NO_OP,
                    'rt_op'              => $d->RT_OP,
                    'rw_op'              => $d->RW_OP,
                    'kel_op'             => $d->KEL_OP,
                    'kec_op'             => $d->KEC_OP,

                    // WP
                    'nama_wp'            => $d->NAMA_WP,
                    'alamat_wp'          => $d->ALAMAT_WP,
                    'blok_kav_no_wp'     => $d->BLOK_KAV_NO_WP,
                    'rt_wp'              => $d->RT_WP,
                    'rw_wp'              => $d->RW_WP,
                    'kel_wp'             => $d->KEL_WP,
                    'kota_wp'            => $d->KOTA_WP,

                    // PBB
                    'jatuh_tempo'        => $d->JATUH_TEMPO
                        ? date('Y-m-d', strtotime($d->JATUH_TEMPO))
                        : null,
                    'terhutang'          => $this->idr($d->TERHUTANG),
                    'pengurangan'        => $this->idr($d->PENGURANGAN),
                    'pbb_harus_dibayar'  => $this->idr($d->PBB_HARUS_DIBAYAR),

                    // Status Penyampaian
                    'status_penyampaian' => $statusPenyampaian,

                    // Locked logic
                    'locked'             => $locked,
                ];
            })->values(),
        ]);
    }

    // ============================================================
    //  TAMBAH PETUGAS MANUAL — FULL SPATIE LOG
    // ============================================================

    public function addPetugasManual($id, Request $r)
    {
        $r->validate([
            'NOP'         => 'required|string',
            'TAHUN'       => 'required|digits:4',
            'PENGGUNA_ID' => 'required|integer',
        ]);

        $userId = (int) $r->PENGGUNA_ID;

        $user = \App\Models\Pengguna::find(session('auth_uid'));

        abort_if(!$user, 403);

        $nop    = preg_replace('/\D+/', '', $r->NOP);
        $tahun  = $r->TAHUN;
        //$userId = (int) $r->NAMA_PETUGAS;

        /*
        |--------------------------------------------------------------------------
        | VALIDASI PETUGAS
        |--------------------------------------------------------------------------
        */
        $petugas = \App\Models\Pengguna::where('ID', $userId)
            ->whereHas(
                'hakakses',
                fn ($q) =>
                $q->whereRaw('LOWER(HAKAKSES) = "petugas"')
            )
            ->first();

        if (!$petugas) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Petugas tidak valid.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CEK DUPLIKAT NOP + TAHUN
        |--------------------------------------------------------------------------
        */
        $exists = DtSdt::where('ID_SDT', $id)
            ->where('STATUS', 1)
            ->where('NOP', $nop)
            ->where('TAHUN', $tahun)
            ->exists();

        if ($exists) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Data dengan NOP & tahun tersebut sudah ada.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA SPPT DARI API
        |--------------------------------------------------------------------------
        | ⚠️ API gagal → STOP → TIDAK ADA LOG
        */
        try {
            $resp = $this->asiapCurlPost(env('ASIAP_URL_DETAIL'), [
                'nop'   => $nop,
                'tahun' => $tahun,
            ]);

            if (
                ($resp['status'] ?? '') !== 'success'
                || empty($resp['data'])
            ) {
                throw new \RuntimeException('Data SPPT tidak ditemukan.');
            }

            $dataApi = $resp['data'];
        } catch (\Throwable $e) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Gagal mengambil data SPPT.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE DT_SDT (LENGKAP)
        |--------------------------------------------------------------------------
        */
        $created = DtSdt::create([
            'ID_SDT'            => $id,
            'STATUS'            => 1,
            'PETUGAS_SDT'       => $petugas->ID,
            'NOP'               => $nop,
            'TAHUN'             => $tahun,

            // OP
            'ALAMAT_OP'         => $dataApi['op_alamat'] ?? null,
            'BLOK_KAV_NO_OP'    => $dataApi['op_blok_kav_no'] ?? null,
            'RT_OP'             => $dataApi['op_rt'] ?? null,
            'RW_OP'             => $dataApi['op_rw'] ?? null,
            'KEL_OP'            => $dataApi['op_kelurahan'] ?? null,
            'KEC_OP'            => $dataApi['op_kecamatan'] ?? null,

            // WP
            'NAMA_WP'           => $dataApi['wp_nama'] ?? null,
            'ALAMAT_WP'         => $dataApi['wp_alamat'] ?? null,
            'BLOK_KAV_NO_WP'    => $dataApi['wp_blok_kav_no'] ?? null,
            'RT_WP'             => $dataApi['wp_rt'] ?? null,
            'RW_WP'             => $dataApi['wp_rw'] ?? null,
            'KEL_WP'            => $dataApi['wp_kelurahan'] ?? null,
            'KOTA_WP'           => $dataApi['wp_kota'] ?? null,

            // PBB
            'JATUH_TEMPO'       => $this->parseDateApi($dataApi['jatuh_tempo'] ?? null),
            'TERHUTANG'         => $this->numishNoCents($dataApi['pbb_terhutang'] ?? null),
            'PENGURANGAN'       => $this->numishNoCents($dataApi['pbb_pengurangan'] ?? null),
            'PBB_HARUS_DIBAYAR' => $this->numishNoCents($dataApi['pbb_yg_harus_dibayar'] ?? null),
        ]);

        /*
        |--------------------------------------------------------------------------
        | LOG — 1 AKSI = 1 LOG
        |--------------------------------------------------------------------------
        */
        activity('sdt')
            ->event('created') // ✅ WAJIB
            ->performedOn($created)
            ->causedBy($user)
            ->withProperties([
                'sdt'    => [
                    'id' => $id,
                ],
                'detail' => [
                    'id'      => $created->ID,
                    'petugas' => $petugas->NAMA,
                    'nop'     => $nop,
                    'tahun'   => $tahun,
                ],
            ])
            ->log('Tambah detail SDT (manual)');

        return response()->json([
            'ok'   => true,
            'msg'  => 'Petugas berhasil ditambahkan.',
            'mode' => 'created',
        ]);
    }
    public function apiNop($id, Request $r)
    {
        $q     = trim((string) $r->query('q', ''));
        $page  = max(1, (int) $r->query('page', 1));
        $limit = 5; // tampil 5 dulu, sisanya load saat scroll

        if ($q === '') {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        try {
            // 🔥 panggil API kamu via POST
            $resp = $this->asiapCurlPost(env('ASIAP_URL_NOP'), ['nop' => $q]);

            if (($resp['status'] ?? '') !== 'success' || empty($resp['data'])) {
                return response()->json([
                    'results'    => [],
                    'pagination' => ['more' => false],
                    'debug'      => $resp['message'] ?? 'Data kosong dari API',
                ]);
            }

            $rows = collect($resp['data'])
                ->map(fn ($x) => [
                    'id'   => $x['value'] ?? '',
                    'text' => $x['label'] ?? '',
                ])
                ->filter(fn ($x) => $x['id'] !== '')
                ->values();

            // Pagination manual
            $offset  = ($page - 1) * $limit;
            $paged   = $rows->slice($offset, $limit)->values();
            $hasMore = $rows->count() > ($offset + $limit);

            return response()->json([
                'results'    => $paged,
                'pagination' => ['more' => $hasMore],
            ]);
        } catch (\Throwable $e) {
            \Log::warning('apiNop gagal', ['q' => $q, 'err' => $e->getMessage()]);
            return response()->json([
                'results'    => [],
                'pagination' => ['more' => false],
                'debug'      => $e->getMessage(),
            ]);
        }
    }
    public function apiTahun($id, Request $r)
    {
        $nop = trim((string) $r->query('nop', ''));
        if ($nop === '') {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        try {
            $resp = $this->asiapCurlPost(env('ASIAP_URL_TAHUN'), ['nop' => $nop]);

            if (($resp['status'] ?? '') !== 'success' || empty($resp['data'])) {
                return response()->json([
                    'results'    => [],
                    'pagination' => ['more' => false],
                    'debug'      => $resp['message'] ?? 'Response kosong dari API.',
                    'sent_nop'   => $nop, // tambahkan debug untuk lihat NOP yg dikirim
                ]);
            }

            $years = collect($resp['data'])
                ->map(fn ($y) => ['id' => (string) $y, 'text' => (string) $y])
                ->sortDesc()
                ->values();

            return response()->json([
                'results'    => $years,
                'pagination' => ['more' => false],
            ]);
        } catch (\Throwable $e) {
            \Log::warning('apiTahun gagal', ['nop' => $nop, 'err' => $e->getMessage()]);
            return response()->json([
                'results'    => [],
                'pagination' => ['more' => false],
                'debug'      => $e->getMessage(),
            ]);
        }
    }
    public function update(Request $r, $id)
    {
        $r->validate([
            'NAMA_SDT'    => [
                'required',
                Rule::unique('sdt', 'NAMA_SDT')
                    ->ignore($id, 'ID')
                    ->where(fn ($q) => $q->where('STATUS', 1)),
            ],
            'TGL_MULAI'   => 'required|date',
            'TGL_SELESAI' => 'required|date',
        ]);

        $user = \App\Models\Pengguna::find(session('auth_uid'));
        abort_if(!$user, 403);

        $sdt = Sdt::findOrFail($id);

        /* ===================== DATA SEBELUM ===================== */
        $old = [
            'id'      => $sdt->ID,
            'nama'    => $sdt->NAMA_SDT,
            'mulai'   => optional($sdt->TGL_MULAI)->format('Y-m-d'),
            'selesai' => optional($sdt->TGL_SELESAI)->format('Y-m-d'),
            'kd_unit' => $sdt->KD_UNIT,
        ];

        /* ===================== UPDATE DATA ===================== */
        $sdt->update([
            'NAMA_SDT'    => $r->NAMA_SDT,
            'TGL_MULAI'   => $r->TGL_MULAI,
            'TGL_SELESAI' => $r->TGL_SELESAI,
        ]);

        /* ===================== TIDAK ADA PERUBAHAN → STOP ===================== */
        if (!$sdt->wasChanged()) {
            return response()->json([
                'ok'  => true,
                'msg' => 'Tidak ada perubahan data.',
            ]);
        }

        /* ===================== DATA SESUDAH ===================== */
        $new = [
            'id'      => $sdt->ID,
            'nama'    => $sdt->NAMA_SDT,
            'mulai'   => optional($sdt->TGL_MULAI)->format('Y-m-d'),
            'selesai' => optional($sdt->TGL_SELESAI)->format('Y-m-d'),
            'kd_unit' => $sdt->KD_UNIT,
        ];

        /* ===================== ACTIVITY LOG ===================== */
        activity('sdt')
            ->event('updated')
            ->performedOn($sdt)
            ->causedBy($user)
            ->withProperties([
                'old' => $old,
                'new' => $new,
            ])
            ->log("SDT \"{$sdt->NAMA_SDT}\" diperbarui");

        return response()->json([
            'ok'  => true,
            'msg' => 'SDT berhasil diperbarui.',
        ]);
    }

    public function existsDetail($id, Request $r)
    {
        $nop   = preg_replace('/\D+/', '', $r->query('nop', ''));
        $tahun = $r->query('tahun', '');

        if ($nop === '' || !preg_match('/^\d{4}$/', $tahun)) {
            return response()->json(['ok' => false, 'exists' => false]);
        }

        $row = DtSdt::where('ID_SDT', $id)
            ->where('STATUS', 1)
            ->where('NOP', $nop)
            ->where('TAHUN', $tahun)
            ->select(['ID', 'PETUGAS_SDT'])
            ->first();

        $dtPengguna = Pengguna::where('ID', $row->PETUGAS_SDT)
            ->where('STATUS', 1)
            ->select(['ID', 'NAMA'])
            ->first();

        if (!$row) {
            return response()->json([
                'ok'     => true,
                'exists' => false,
            ]);
        }

        return response()->json([
            'ok'          => true,
            'exists'      => true,
            'has_petugas' => filled($row->PETUGAS_SDT),
            'petugas'     => $dtPengguna->NAMA,
        ]);
    }
    public function destroy($id)
    {

        $user = \App\Models\Pengguna::find(session('auth_uid'));
        $sdt  = Sdt::with('details.statusPenyampaian')->findOrFail($id);
        $Md_sdt = new Sdt;

        $checkpenyampaiansdt = $Md_sdt->checkPenyampaianSdt($id);


        // ================= VALIDASI BISNIS =================
        if ($checkpenyampaiansdt) {
            return response()->json([
                'ok'  => false,
                'msg' => 'SDT tidak dapat dihapus karena sudah memiliki data penyampaian.',
            ], 422);
        }

        // ================= DATA UNTUK LOG =================
        $oldValues = [
            'id'        => $sdt->ID,
            'nama'      => $sdt->NAMA_SDT,
            'mulai'     => $sdt->TGL_MULAI,
            'selesai'   => $sdt->TGL_SELESAI,
            'kd_unit'   => $sdt->KD_UNIT,
            'total_row' => $sdt->details->count(),
        ];

        // ================= TRANSAKSI DELETE LOGIS =================
        DB::transaction(function () use ($sdt) {
            DtSdt::where('ID_SDT', $sdt->ID)->update(['STATUS' => 0]);
            $sdt->update(['STATUS' => 0]);
        });

        // ================= ACTIVITY LOG (WAJIB MANUAL) =================
        activity('sdt')
            ->event('deleted') // <-- INI PENTING
            ->performedOn($sdt)
            ->causedBy($user)
            ->withProperties([
                'old' => $oldValues,
                'new' => ['STATUS' => 0],
            ])
            ->log("SDT \"{$sdt->NAMA_SDT}\" dihapus");

        return response()->json([
            'ok'  => true,
            'msg' => 'SDT berhasil dihapus.',
        ]);
    }
    private static function idr($val): string
    {
        if ($val === null || $val === '') {
            return '-';
        }

        $num = preg_replace('/[^0-9\-]/', '', (string) $val);
        if ($num === '' || $num === '-') {
            return '-';
        }

        $intVal = (int) $num;
        if ($intVal > 0 && strlen($num) >= 10 && substr($num, -2) === '00') {
            $intVal = (int) substr($num, 0, -2);
        }

        return 'Rp.' . number_format($intVal, 0, ',', '.');
    }

    private function numishNoCents($v): ?string
    {
        if ($v === null) {
            return null;
        }

        $s = trim((string) $v);
        if ($s === '') {
            return null;
        }

        $s = preg_replace('/[^0-9\-]/', '', $s);
        if ($s === '' || $s === '-') {
            return null;
        }

        if (strlen($s) >= 10 && substr($s, -2) === '00') {
            $s = substr($s, 0, -2);
        }

        return preg_match('/^-?\d+$/', $s) ? $s : null;
    }

    private function ymdOrNull($in): ?string
    {
        if (!$in) {
            return null;
        }

        $in = trim((string) $in);
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $fmt) {
            $d = \DateTime::createFromFormat($fmt, $in);
            if ($d && $d->format($fmt) === $in) {
                return $d->format('Y-m-d');
            }
        }
        if (is_numeric($in)) {
            try {
                $ts = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($in);
                return gmdate('Y-m-d', $ts);
            } catch (\Throwable) {
            }
        }
        return null;
    }

    private function parseDateApi($val): ?string
    {
        if (empty($val)) {
            return null;
        }

        // Format API kamu: "YYYY-MM-DD HH:MM:SS"
        $in = trim((string) $val);

        // 🔹 Jika format lengkap timestamp
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $in)) {
            try {
                return (new \DateTime($in))->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        // 🔹 fallback ke format normal yg lama
        return $this->ymdOrNull($in);
    }

    private function asiapCurlPost(string $url, array $fields): array
    {
        $username = env('ASIAP_BASIC_USER', 'asiap_app');
        $password = env('ASIAP_BASIC_PASS', 'euTpKORnaObqO8Jw');
        $basic    = base64_encode("{$username}:{$password}");

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $fields, // multipart/form-data otomatis
            CURLOPT_HTTPHEADER     => [
                'Authorization: Basic ' . $basic,
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL error: ' . $error);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException("HTTP {$status} dari API");
        }

        $json = json_decode($response, true);
        if (!is_array($json)) {
            throw new \RuntimeException('Response API tidak valid (bukan JSON)');
        }

        return $json;
    }

    public function updateRowPetugas(Request $request, $id)
    {
        $request->validate([
            'petugas' => 'required|integer',
        ]);

        // auth manual kamu
        $user = \App\Models\Pengguna::find(session('auth_uid'));
        abort_if(!$user, 403);

        // pastikan petugas valid
        $petugas = \App\Models\Pengguna::where('ID', $request->petugas)
            ->whereHas('hakakses', function ($q) {
                $q->whereRaw('LOWER(HAKAKSES) = "petugas"');
            })
            ->first();

        if (!$petugas) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Petugas tidak valid',
            ], 422);
        }

        $row = \App\Models\DtSdt::findOrFail($id);

        // update SATU kolom saja
        $row->update([
            'PETUGAS_SDT' => $petugas->ID,
        ]);

        return response()->json([
            'ok'      => true,
            'petugas' => $petugas->NAMA,
        ]);
    }
}
