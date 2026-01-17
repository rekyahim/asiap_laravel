<?php

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ApiPenggunaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\HakaksesModulController;
use App\Http\Controllers\ImportSdtController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PetugasSdtController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\SdtController;
use App\Http\Middleware\AuthOnly;

/*
|--------------------------------------------------------------------------
| MIDDLEWARE
|--------------------------------------------------------------------------
*/
use App\Http\Middleware\CanModul;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CORE
|--------------------------------------------------------------------------
*/
use Spatie\Activitylog\Models\Activity;

/*
|--------------------------------------------------------------------------
| AUTH (Tanpa Middleware)
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| FORCE CHANGE PASSWORD
|--------------------------------------------------------------------------
*/
Route::middleware(AuthOnly::class)->group(function () {

    Route::get('/force-change-password', [PenggunaController::class, 'forceChangePasswordForm'])
        ->name('first.change.password');

    Route::post('/force-change-password', [PenggunaController::class, 'forceChangePasswordUpdate'])
        ->name('first.change.password.update');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD & PROTECTED AREA
|--------------------------------------------------------------------------
*/
Route::middleware(AuthOnly::class)->group(function () {

    // Dashboard
    Route::get('/', fn () => view('dashboard'))->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | MY PROFILE
    |--------------------------------------------------------------------------
    */
    Route::get('/my-profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::post('/my-profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::post('/profile/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::post('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.deletePhoto');

    Route::patch('/my-profile/change-password', [PenggunaController::class, 'changePassword'])->name('pengguna.changePassword');
    Route::patch('/my-profile/update', [PenggunaController::class, 'updateProfile'])->name('pengguna.updateProfile');

    Route::get('/profile/change-password', [PenggunaController::class, 'changePasswordForm'])
        ->name('profile.change.password');

    Route::post('/profile/change-password', [PenggunaController::class, 'changePasswordUpdate'])
        ->name('profile.change.password.update');

    /*
    |--------------------------------------------------------------------------
    | ============================ ADMIN ============================
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {

        Route::get('/log', [LogActivityController::class, 'index'])
            ->name('admin.log.index');

        // Endpoint JSON untuk modal detail
        Route::get('/log/{id}', function ($id) {
            return Activity::findOrFail($id);
        })->name('admin.log.show');
        /*
        |--------------------------------------------------------------------------
        | MODUL
        |--------------------------------------------------------------------------
        */
        Route::middleware(CanModul::class . ':kelola_modul')->group(function () {

            Route::get('/modul', [ModulController::class, 'index'])->name('modul.index');
            Route::post('/modul', [ModulController::class, 'store'])->name('modul.store');

            Route::get('/modul/{id}/edit', [ModulController::class, 'edit'])->whereNumber('id')->name('modul.edit');
            Route::patch('/modul/{id}', [ModulController::class, 'update'])->whereNumber('id')->name('modul.update');

            Route::delete('/modul/{id}', [ModulController::class, 'destroy'])->whereNumber('id')->name('modul.destroy');
            Route::patch('/modul/{id}/toggle', [ModulController::class, 'toggleStatus'])
                ->whereNumber('id')->name('modul.toggle');
        });

        /*
        |--------------------------------------------------------------------------
        | HAK AKSES
        |--------------------------------------------------------------------------
        */
        Route::middleware(CanModul::class . ':kelola_hakakses')->group(function () {

            Route::get('/hak-akses', [HakAksesController::class, 'index'])->name('hakakses.index');
            Route::post('/hak-akses', [HakAksesController::class, 'store'])->name('hakakses.store');

            Route::get('/hak-akses/{id}/edit', [HakAksesController::class, 'edit'])->whereNumber('id')->name('hakakses.edit');
            Route::patch('/hak-akses/{id}', [HakAksesController::class, 'update'])->whereNumber('id')->name('hakakses.update');

            Route::delete('/hak-akses/{id}', [HakAksesController::class, 'destroy'])->whereNumber('id')->name('hakakses.destroy');
            Route::patch('/hak-akses/{id}/toggle', [HakAksesController::class, 'toggleStatus'])
                ->whereNumber('id')->name('hakakses.toggle');
        });

        /*
        |--------------------------------------------------------------------------
        | HAK AKSES ↔ MODUL
        |--------------------------------------------------------------------------
        */
        Route::middleware(CanModul::class . ':kelola_aksesmodul')->group(function () {

            Route::get('/hak-akses/modul', function () {
                $first = \App\Models\HakAkses::where('status', 1)->orderBy('id')->value('id');
                return $first
                    ? redirect()->route('admin.hakakses.modul.edit', ['hak' => $first])
                    : redirect()->route('hakakses.index');
            })->name('admin.hakakses.modul.index');

            Route::get('/hak-akses/{hak}/modul', [HakaksesModulController::class, 'editModules'])
                ->whereNumber('hak')->name('admin.hakakses.modul.edit');

            Route::patch('/hak-akses/{hak}/modul', [HakaksesModulController::class, 'update'])
                ->whereNumber('hak')->name('admin.hakakses.modul.update');
        });

        /*
        |--------------------------------------------------------------------------
        | PENGGUNA
        |--------------------------------------------------------------------------
        */
        Route::middleware(CanModul::class . ':kelola_pengguna')->group(function () {

            Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
            Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');

            Route::patch('/pengguna/{id}/hakakses', [PenggunaController::class, 'updateHakAkses'])
                ->whereNumber('id')->name('pengguna.hakakses.update');

            Route::patch('/pengguna/{id}', [PenggunaController::class, 'update'])
                ->whereNumber('id')->name('pengguna.update');

            Route::patch('/pengguna/{id}/hapus', [PenggunaController::class, 'destroy'])
                ->whereNumber('id')->name('pengguna.destroy');

            Route::patch('/pengguna/{id}/reset-password', [PenggunaController::class, 'resetPassword'])
                ->whereNumber('id')->name('pengguna.reset');

            Route::post('/pengguna/{id}/activate', [PenggunaController::class, 'activate'])
                ->whereNumber('id')->name('pengguna.activate');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ============================ KOORDINATOR ============================
    |--------------------------------------------------------------------------
    */
    Route::prefix('koor')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | SDT
        |--------------------------------------------------------------------------
        */
        Route::prefix('sdt')->name('sdt.')->group(function () {

            Route::post(
                'row/{id}/update-petugas',
                [\App\Http\Controllers\SdtController::class, 'updateRowPetugas']
            )->name('sdt.row.update-petugas');

            Route::get('/', [SdtController::class, 'index'])->name('index');
            Route::get('/create', [SdtController::class, 'create'])->name('create');
            Route::post('/', [SdtController::class, 'store'])->name('store');

            Route::patch('{id}', [SdtController::class, 'update'])->whereNumber('id')->name('update');

            Route::get('{id}', [SdtController::class, 'show'])->whereNumber('id')->name('show');
            Route::get('{id}/detail', [SdtController::class, 'detail'])->whereNumber('id')->name('detail');

            Route::delete('{id}', [SdtController::class, 'destroy'])->whereNumber('id')->name('destroy');

            Route::post('{id}/import-detail', [SdtController::class, 'importDetailExcel'])
                ->whereNumber('id')->name('importDetail');

            Route::post('{id}/petugas-manual', [SdtController::class, 'addPetugasManual'])
                ->whereNumber('id')->name('petugasManual');

            Route::get('{id}/api/nop', [SdtController::class, 'apiNop'])
                ->whereNumber('id')->name('api.nop');

            Route::get('{id}/api/tahun', [SdtController::class, 'apiTahun'])
                ->whereNumber('id')->name('api.tahun');

            Route::get('{id}/exists', [SdtController::class, 'existsDetail'])
                ->whereNumber('id')->name('exists');

            Route::get('{id}/nops', [SdtController::class, 'listNops'])
                ->whereNumber('id')->name('nops');

            Route::get('{id}/export', [RiwayatController::class, 'exportSdt'])
                ->whereNumber('id')->name('export');
        });

        /*
        |--------------------------------------------------------------------------
        | LEGACY ROUTES (TIDAK DIUBAH)
        |--------------------------------------------------------------------------
        */
        Route::get('/koor/sdt/{id}/detail', fn ($id) => redirect()->route('sdt.detail', $id))
            ->whereNumber('id');

        Route::delete('/koor/sdt/{id}', [SdtController::class, 'destroy'])
            ->name('sdt.destroy.legacy')->whereNumber('id');

        Route::get(
            '/koor/sdt/{id}/edit',
            fn ($id) =>
            redirect()->route('sdt.index', ['openEdit' => $id])
        )->name('sdt.edit')->whereNumber('id');

        /*
        |--------------------------------------------------------------------------
        | IMPORT SDT LAMA
        |--------------------------------------------------------------------------
        */
        Route::get('/import-sdt', [ImportSdtController::class, 'form'])->name('sdt.import.form');
        Route::post('/import-sdt', [ImportSdtController::class, 'store'])->name('sdt.import.store');

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT PETUGAS
        |--------------------------------------------------------------------------
        */
        Route::get('/riwayat-petugas', [RiwayatController::class, 'petugas'])
            ->name('riwayat.petugas');

        Route::get('/riwayat/{id}/detail', [RiwayatController::class, 'detailRow'])
            ->whereNumber('id')->name('riwayat.detail');

        /*
        |--------------------------------------------------------------------------
        | API PENGGUNA
        |--------------------------------------------------------------------------
        */
        Route::get('/api/pengguna-search', [ApiPenggunaController::class, 'search'])
            ->name('api.pengguna.search');

        Route::get('/api/pengguna/{id}', [ApiPenggunaController::class, 'show'])
            ->name('api.pengguna.show');
    });

    /*
    |--------------------------------------------------------------------------
    | PETUGAS SDT
    |--------------------------------------------------------------------------
    */
});

Route::prefix('petugas/sdt')->name('petugas.sdt.')->group(function () {

    Route::get('/', [PetugasSdtController::class, 'index'])->name('index');

    Route::get('{id}/detail', [PetugasSdtController::class, 'detail'])
        ->whereNumber('id')->name('detail');
    Route::get('detail/{id}', [PetugasSdtController::class, 'detail'])
        ->whereNumber('id')->name('detail');
    Route::get('row/{id}/show', [PetugasSdtController::class, 'showPage'])
        ->whereNumber('id')->name('show');

    Route::get('row/{id}/edit', [PetugasSdtController::class, 'edit'])
        ->whereNumber('id')->name('edit');

    Route::post('row/{id}/update', [PetugasSdtController::class, 'update'])
        ->whereNumber('id')->name('update');

    Route::post('row/{id}/status/store', [PetugasSdtController::class, 'storeStatusPenyampaian'])
        ->whereNumber('id')->name('status.store');

    Route::post('massupdate/ko/update', [PetugasSdtController::class, 'massUpdateKO'])
        ->name('massupdate.ko.update');

    Route::post('massupdate/nop/update', [PetugasSdtController::class, 'massUpdateNOP'])
        ->name('massupdate.nop.update');

    Route::get('api/nop/search', [PetugasSdtController::class, 'searchNOP'])
        ->name('api.nop');
    Route::get('api/nop/detail', [PetugasSdtController::class, 'getDetailNOP'])
        ->name('api.nop.detail');
});
