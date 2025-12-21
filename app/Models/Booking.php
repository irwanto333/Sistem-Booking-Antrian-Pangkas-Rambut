<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'customer_name',
        'customer_phone',
        'tukang_cukur_id',
        'service_id',
        'booking_date',
        'booking_time',
        'status',
        'queue_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'queue_number' => 'integer',
        ];
    }

    // Constants for status
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    // Boot method for auto-generating booking code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = self::generateBookingCode();
            }
            if (empty($booking->queue_number)) {
                $booking->queue_number = self::generateQueueNumber($booking->booking_date);
            }
        });
    }

    // Generate unique booking code
    public static function generateBookingCode(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('BRB-%s-%03d', $date, $count);
    }

    // Generate queue number for the day
    public static function generateQueueNumber($date): int
    {
        return self::whereDate('booking_date', $date)
            ->whereNotIn('status', [self::STATUS_CANCELLED])
            ->count() + 1;
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', now()->toDateString());
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeInQueue($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    // Relationships
    public function tukangCukur()
    {
        return $this->belongsTo(TukangCukur::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Check if booking can be cancelled
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }
}
