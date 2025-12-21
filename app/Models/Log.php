<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'loggable_type',
        'loggable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    // Action constants
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_BOOKING_CREATED = 'booking_created';
    const ACTION_STATUS_UPDATED = 'status_updated';
    const ACTION_QUEUE_CALLED = 'queue_called';
    const ACTION_QUEUE_RESET = 'queue_reset';

    // Relationships
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByAdmin($query, int $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper to create log
    public static function record(
        string $action,
        ?string $description = null,
        ?Model $loggable = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'admin_id' => auth('admin')->id(),
            'action' => $action,
            'loggable_type' => $loggable ? get_class($loggable) : null,
            'loggable_id' => $loggable?->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
