<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Booking;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends BaseController
{
    /**
     * Get all bookings
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with(['tukangCukur', 'service']);

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tukang cukur
        if ($request->has('tukang_cukur_id')) {
            $query->where('tukang_cukur_id', $request->tukang_cukur_id);
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('booking_time')
            ->paginate($request->input('per_page', 15));

        return $this->success($bookings, 'Daftar booking berhasil diambil');
    }

    /**
     * Get booking detail
     */
    public function show(int $id): JsonResponse
    {
        $booking = Booking::with(['tukangCukur', 'service'])->find($id);

        if (!$booking) {
            return $this->notFound('Booking tidak ditemukan');
        }

        return $this->success($booking, 'Detail booking berhasil diambil');
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return $this->notFound('Booking tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', Booking::STATUSES),
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $oldStatus = $booking->status;
        $newStatus = $request->status;

        $booking->update(['status' => $newStatus]);

        // Log activity
        Log::record(
            Log::ACTION_STATUS_UPDATED,
            "Status booking {$booking->booking_code} diubah dari {$oldStatus} ke {$newStatus}",
            $booking,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );

        return $this->success($booking->fresh()->load(['tukangCukur', 'service']), 'Status booking berhasil diupdate');
    }

    /**
     * Delete booking
     */
    public function destroy(int $id): JsonResponse
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return $this->notFound('Booking tidak ditemukan');
        }

        $oldValues = $booking->toArray();

        // Log activity
        Log::record(
            Log::ACTION_DELETE,
            "Booking dihapus: {$booking->booking_code}",
            $booking,
            $oldValues
        );

        $booking->delete();

        return $this->success(null, 'Booking berhasil dihapus');
    }
}
