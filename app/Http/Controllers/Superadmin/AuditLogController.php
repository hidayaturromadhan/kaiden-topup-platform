<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Menampilkan daftar Audit Log (Append-Only Viewer)
     * RULES.md Section 4.C: Hanya Superadmin yang dapat melihat, dilarang ada fitur edit/delete!
     */
    public function index(Request $request)
    {
        $actorId = $request->query('actor_id');
        $action = $request->query('action');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = AuditLog::with('actor');

        if ($actorId) {
            $query->where('actor_id', $actorId);
        }

        if ($action) {
            $query->where('action', 'LIKE', "%{$action}%");
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $logs = $query->latest('id')->paginate(20)->withQueryString();
        $actors = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'superadmin']);
        })->get();

        $distinctActions = AuditLog::distinct()->pluck('action');

        return view('superadmin.audit_logs.index', compact('logs', 'actors', 'distinctActions', 'actorId', 'action', 'startDate', 'endDate'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('actor');
        return view('superadmin.audit_logs.show', compact('auditLog'));
    }
}

