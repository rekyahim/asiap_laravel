<?php
namespace App\Providers;

use App\Models\Pengguna;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pagination Bootstrap 5
        Paginator::useBootstrapFive();

        /**
         * ============================================
         * GLOBAL ACTIVITY LOG CAUSER (SESSION BASED)
         * ============================================
         */
        Activity::saving(function (Activity $activity) {

            // karena kamu pakai session manual
            $uid = session('auth_uid');

            if ($uid) {
                $activity->causer_id   = $uid;
                $activity->causer_type = Pengguna::class;
            }
        });
    }
}
