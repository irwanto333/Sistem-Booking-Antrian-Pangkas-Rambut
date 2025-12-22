<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Booking;
use App\Models\Log;
use Illuminate\Http\JsonResponse;

class QueueController extends BaseController
{
    /**
     * Get current queue
     */
    public function index(): JsonResponse
    {
        $bookings = Booking::with(['tukangCukur', 'service'])
            ->today()
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_IN_PROGRESS,
            ])
            ->orderBy('queue_number')
            ->get();

        $currentInProgress = $bookings->where('status', Booking::STATUS_IN_PROGRESS)->first();

        return $this->success([
            'date' => now()->toDateString(),
            'current_serving' => $currentInProgress ? [
                'queue_number' => $currentInProgress->queue_number,
                'booking_code' => $currentInProgress->booking_code,
                'customer_name' => $currentInProgress->customer_name,
                'service' => $currentInProgress->service->name,
                'tukang_cukur' => $currentInProgress->tukangCukur->name,
            ] : null,
            'waiting_list' => $bookings->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
            ])->values()->map(fn($b) => [
                'queue_number' => $b->queue_number,
                'booking_code' => $b->booking_code,
                'customer_name' => $b->customer_name,
                'booking_time' => $b->booking_time,
                'status' => $b->status,
                'service' => $b->service->name,
                'tukang_cukur' => $b->tukangCukur->name,
            ]),
            'total_waiting' => $bookings->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
            ])->count(),
        ], 'Data antrian berhasil diambil');
    }

    /**
     * Call next queue
     */
    public function next(): JsonResponse
    {
        // Complete current in progress
        $currentInProgress = Booking::today()
            ->where('status', Booking::STATUS_IN_PROGRESS)
            ->first();

        if ($currentInProgress) {
            $currentInProgress->update(['status' => Booking::STATUS_COMPLETED]);

            Log::record(
                Log::ACTION_STATUS_UPDATED,
                "Booking {$currentInProgress->booking_code} selesai dilayani",
                $currentInProgress,
                ['status' => Booking::STATUS_IN_PROGRESS],
                ['status' => Booking::STATUS_COMPLETED]
            );
        }

        // Get next in queue
        $next = Booking::today()
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->orderBy('queue_number')
            ->first();

        if (!$next) {
            return $this->success([
                'message' => 'Tidak ada antrian yang tersisa',
                'current_serving' => null,
            ], 'Tidak ada antrian berikutnya');
        }

        $next->update(['status' => Booking::STATUS_IN_PROGRESS]);

        Log::record(
            Log::ACTION_QUEUE_CALLED,
            "Antrian {$next->queue_number} dipanggil: {$next->booking_code}",
            $next
        );

        $next->load(['tukangCukur', 'service']);

        return $this->success([
            'message' => "Antrian nomor {$next->queue_number} dipanggil",
            'current_serving' => [
                'queue_number' => $next->queue_number,
                'booking_code' => $next->booking_code,
                'customer_name' => $next->customer_name,
                'service' => $next->service->name,
                'tukang_cukur' => $next->tukangCukur->name,
            ],
        ], 'Antrian berikutnya berhasil dipanggil');
    }

    /**
     * Reset daily queue
     */
    public function reset(): JsonResponse
    {
        $updated = Booking::today()
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_IN_PROGRESS,
            ])
            ->update(['status' => Booking::STATUS_CANCELLED]);

        Log::record(
            Log::ACTION_QUEUE_RESET,
            "Antrian hari ini direset. {$updated} booking dibatalkan."
        );

        return $this->success([
            'cancelled_count' => $updated,
        ], 'Antrian harian berhasil direset');
    }
}
