<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    /**
     * Display the audit logs page
     */
    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();

        // Only managers can view audit logs
        if (!$user || $user->role !== 'Manager') {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access.');
        }

        $query = AuditLog::orderBy('created_at', 'desc');

        // Filter by table
        if ($request->filled('table')) {
            $query->where('table_name', $request->table);
        }

        // Filter by operation
        if ($request->filled('operation')) {
            $query->where('operation', $request->operation);
        }

        // Filter by user type
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by user name or record ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('record_id', 'like', "%{$search}%")
                  ->orWhere(function ($subQuery) use ($search) {
                      // For admin users
                      $subQuery->where('user_type', 'admin')
                               ->whereHas('adminUser', function ($adminQuery) use ($search) {
                                   $adminQuery->where('name', 'like', "%{$search}%")
                                             ->orWhere('email', 'like', "%{$search}%");
                               })
                               // For customer users
                               ->orWhere(function ($customerSubQuery) use ($search) {
                                   $customerSubQuery->where('user_type', 'customer')
                                                   ->whereHas('customerUser', function ($customerQuery) use ($search) {
                                                       $customerQuery->where('name', 'like', "%{$search}%")
                                                                   ->orWhere('email', 'like', "%{$search}%");
                                                   });
                               });
                  });
            });
        }

        $auditLogs = $query->paginate(20);

        // Load users manually to avoid the eager loading issue
        foreach ($auditLogs as $log) {
            if ($log->user_type === 'admin') {
                $log->user = \App\Models\Admin::find($log->user_id);
            } elseif ($log->user_type === 'customer') {
                $log->user = \App\Models\Customer::find($log->user_id);
            } else {
                $log->user = null;
            }
        }

        // Get filter options
        $tables = AuditLog::distinct()->pluck('table_name')->sort();
        $operations = ['create', 'update', 'delete', 'restore', 'force_delete'];
        $userTypes = AuditLog::distinct()->pluck('user_type')->filter()->sort();

        return view('admin.manager.audit.index', compact(
            'auditLogs',
            'tables',
            'operations',
            'userTypes'
        ));
    }

    /**
     * Show detailed view of a specific audit log entry
     */
    public function show($id)
    {
        $user = Auth::guard('admin')->user();

        if (!$user || $user->role !== 'Manager') {
            return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access.');
        }

        $auditLog = AuditLog::findOrFail($id);

        // Load user manually
        if ($auditLog->user_type === 'admin') {
            $auditLog->user = \App\Models\Admin::find($auditLog->user_id);
        } elseif ($auditLog->user_type === 'customer') {
            $auditLog->user = \App\Models\Customer::find($auditLog->user_id);
        } else {
            $auditLog->user = null;
        }

        return view('admin.manager.audit.show', compact('auditLog'));
    }

    /**
     * Get audit statistics
     */
    public function statistics()
    {
        $user = Auth::guard('admin')->user();

        if (!$user || $user->role !== 'Manager') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'operations' => AuditLog::selectRaw('operation, COUNT(*) as count')
                ->groupBy('operation')
                ->pluck('count', 'operation'),
            'tables' => AuditLog::selectRaw('table_name, COUNT(*) as count')
                ->groupBy('table_name')
                ->orderBy('count', 'desc')
                ->take(10)
                ->pluck('count', 'table_name'),
            'recent_activity' => AuditLog::orderBy('created_at', 'desc')
                ->take(5)
                ->get()
        ];

        // Load users manually for recent activity
        foreach ($stats['recent_activity'] as $log) {
            if ($log->user_type === 'admin') {
                $log->user = \App\Models\Admin::find($log->user_id);
            } elseif ($log->user_type === 'customer') {
                $log->user = \App\Models\Customer::find($log->user_id);
            } else {
                $log->user = null;
            }
        }

        return response()->json($stats);
    }
}
