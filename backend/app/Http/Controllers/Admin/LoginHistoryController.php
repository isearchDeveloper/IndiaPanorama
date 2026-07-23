<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = LoginHistory::with('user')->latest('logged_in_at');

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('logged_in_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('logged_in_at', '<=', $request->date_to);
        }

        $status = $request->status;
        if ($status === 'online') {
            $query->online();
        } elseif ($status === 'success' || $status === 'failed') {
            $query->where('status', $status);
        }

        $onlineCount = LoginHistory::online()->count();
        $logs        = $query->paginate(25)->withQueryString();

        return view('admin.login-history.index', compact('logs', 'onlineCount'));
    }

    public function export(Request $request)
    {
        $query = LoginHistory::latest('logged_in_at');

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }
        if (in_array($request->status, ['success', 'failed'], true)) {
            $query->where('status', $request->status);
        }
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('logged_in_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('logged_in_at', '<=', $request->date_to);
        }

        $logs = $query->limit(5000)->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="login-history-' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'Email', 'Status', 'Device Type', 'Device', 'OS', 'Browser', 'Location', 'IP', 'Login Time', 'Logout Time', 'Last Activity', 'Failure Reason']);
            foreach ($logs as $i => $log) {
                fputcsv($out, [
                    $i + 1,
                    $log->email,
                    $log->status,
                    $log->device_type ?? '',
                    $log->device_name ?? '',
                    $log->os_name ?? '',
                    trim(($log->browser_name ?? '') . ' ' . ($log->browser_version ?? '')),
                    $log->location,
                    $log->ip_address,
                    $log->logged_in_at?->format('d M Y H:i:s'),
                    $log->logout_at?->format('d M Y H:i:s') ?? '',
                    $log->last_activity_at?->format('d M Y H:i:s') ?? '',
                    $log->failure_reason ?? '',
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}
