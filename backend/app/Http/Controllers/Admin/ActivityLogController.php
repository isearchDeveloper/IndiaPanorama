<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    private static array $actions = ['login', 'logout', 'created', 'updated', 'deleted', 'viewed', 'status-changed', 'permission-changed'];

    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('user_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs    = $query->paginate(30)->withQueryString();
        $modules = ActivityLog::distinct()->orderBy('module')->pluck('module');
        $actions = self::$actions;

        return view('admin.activity-logs.index', compact('logs', 'modules', 'actions'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('user_name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('module')) $query->where('module', $request->module);
        if ($request->filled('action')) $query->where('action', $request->action);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->limit(10000)->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity-logs-' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'Admin', 'Role', 'Action', 'Module', 'Description', 'Device', 'OS', 'Browser', 'Country', 'IP', 'Time']);
            foreach ($logs as $i => $log) {
                fputcsv($out, [
                    $i + 1,
                    $log->user_name,
                    $log->role ?? '',
                    $log->action,
                    $log->module,
                    $log->description,
                    $log->device_type ?? '',
                    $log->os_name ?? '',
                    $log->browser_name ?? '',
                    $log->country ?? '',
                    $log->ip_address,
                    $log->created_at?->format('d M Y H:i:s'),
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
