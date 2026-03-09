<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'table_name',
        'operation',
        'record_id',
        'user_type',
        'user_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Log a database operation
     */
    public static function log($table, $operation, $recordId, $oldValues = null, $newValues = null)
    {
        $user = null;
        $userType = null;
        $userId = null;

        // Try to get user from different guards
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $userType = 'admin';
            $userId = $user->id;
        } elseif (Auth::check()) {
            $user = Auth::user();
            $userType = 'customer';
            $userId = $user->id;
        }

        return static::create([
            'table_name' => $table,
            'operation' => $operation,
            'record_id' => $recordId,
            'user_type' => $userType,
            'user_id' => $userId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get the user who performed this action
     */
    public function user()
    {
        if ($this->user_type === 'admin') {
            return $this->belongsTo(Admin::class, 'user_id');
        } elseif ($this->user_type === 'customer') {
            return $this->belongsTo(Customer::class, 'user_id');
        }

        return null;
    }

    /**
     * Relationship for admin users (for querying)
     */
    public function adminUser()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Relationship for customer users (for querying)
     */
    public function customerUser()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}
