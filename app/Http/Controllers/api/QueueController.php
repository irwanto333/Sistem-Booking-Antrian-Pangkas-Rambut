<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\JsonResponse;

class QueueController extends BaseController
{
    /**
     * Get today's queue (Public)
     */
    public function today(): JsonResponse
    {
        $bookings = Booking::with(['tukangCukur', 'service'])
            ->today()
            ->inQueue()
            ->orderBy('queue_number')
            ->get()
            ->map(function ($booking) {
                return [
                    'queue_number' => $booking->queue_number,
                    'booking_code' => $booking->booking_code,
                    'customer_name' => $booking->customer_name,
                    'booking_time' => $booking->booking_time,
                    'status' => $booking->status,
                    'service' => $booking->service->name,
                    'tukang_cukur' => $booking->tukangCukur->name,
                    'duration_minutes' => $booking->service->duration_minutes,
                ];
            });

        $currentQueue = Booking::today()
            ->where('status', Booking::STATUS_IN_PROGRESS)
            ->first();

        return $this->success([
            'date' => now()->toDateString(),
            'current_queue' => $currentQueue ? $currentQueue->queue_number : null,
            'total_waiting' => $bookings->where('status', Booking::STATUS_PENDING)->count() +
                              $bookings->where('status', Booking::STATUS_CONFIRMED)->count(),
            'queue_list' => $bookings,
        ], 'Antrian hari ini berhasil diambil');
    }
}
