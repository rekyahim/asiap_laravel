<?php
namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogActivityController extends Controller
{
    /* =====================================================
     *  INDEX – LIST LOG
     * ===================================================== */
    public function index(Request $r)
    {
        $userId   = $r->input('user');
        $event    = $r->input('event');
        $logName  = $r->input('log');
        $dateFrom = $r->input('date_from');
        $dateTo   = $r->input('date_to');
        $search   = $r->input('search');

        $logs = Activity::with('causer')
            ->when($userId, fn($q) => $q->where('causer_id', $userId))
            ->when($event, fn($q) => $q->where('event', $event))
            ->when($logName, fn($q) => $q->where('log_name', $logName))
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('description', 'like', "%{$search}%")
                        ->orWhere('subject_type', 'like', "%{$search}%")
                        ->orWhere('properties->old', 'like', "%{$search}%")
                        ->orWhere('properties->new', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // 🔹 Dropdown Log Name
        $logNames = Activity::select('log_name')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');

        // 🔹 Dropdown User (Pengguna)
        $users = Pengguna::orderBy('NAMA')
            ->get()
            ->map(fn($u) => (object) [
                'id'   => $u->ID,
                'name' => $u->NAMA,
            ]);

        return view('admin.log.index', compact(
            'logs',
            'users',
            'logNames'
        ));
    }

    /* =====================================================
     *  SHOW – DETAIL LOG (MODAL)
     * ===================================================== */
    public function show($id)
    {
        $log = Activity::with('causer')->findOrFail($id);

        return response()->json([
            'id'          => $log->id,
            'log_name'    => $log->log_name,
            'event'       => $log->event,
            'description' => $log->description,
            'created_at'  => $log->created_at->format('d F Y \p\u\k\u\l H.i'),

            'causer'      => $log->causer ? [
                'NAMA'      => $log->causer->NAMA,
                'NAMA_UNIT' => $log->causer->NAMA_UNIT,
                'HAKAKSES'  => $log->causer->HAKAKSES,
            ] : null,

            'properties'  => $log->properties,
        ]);
    }
}
