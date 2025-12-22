<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Log;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends BaseController
{
    /**
     * Create new booking (Public)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'tukang_cukur_id' => 'required|exists:tukang_cukurs,id',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Check if schedule is available
        $dayOfWeek = Carbon::parse($request->booking_date)->dayOfWeek;
        $schedule = Schedule::where('tukang_cukur_id', $request->tukang_cukur_id)
            ->forDay($dayOfWeek)
            ->available()
            ->first();

        if (!$schedule) {
            return $this->error('Tukang cukur tidak tersedia pada hari tersebut', 422);
        }

        // Check if time is within schedule
        $bookingTime = $request->booking_time;
        if ($bookingTime < $schedule->open_time || $bookingTime >= $schedule->close_time) {
            return $this->error('Waktu booking di luar jam operasional', 422);
        }

        // Check for existing booking at same time
        $existingBooking = Booking::where('tukang_cukur_id', $request->tukang_cukur_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->first();

        if ($existingBooking) {
            return $this->error('Waktu tersebut sudah dibooking', 422);
        }

        $booking = Booking::create($request->only([
            'customer_name',
            'customer_phone',
            'tukang_cukur_id',
            'service_id',
            'booking_date',
            'booking_time',
            'notes',
        ]));

        $booking->load(['tukangCukur', 'service']);

        // Log activity
        Log::record(
            Log::ACTION_BOOKING_CREATED,
            "Booking baru dibuat: {$booking->booking_code}",
            $booking
        );

        return $this->created([
            'booking_code' => $booking->booking_code,
            'queue_number' => $booking->queue_number,
            'customer_name' => $booking->customer_name,
            'booking_date' => $booking->booking_date->format('Y-m-d'),
            'booking_time' => $booking->booking_time,
            'status' => $booking->status,
            'service' => $booking->service->name,
            'tukang_cukur' => $booking->tukangCukur->name,
        ], 'Booking berhasil dibuat');
    }

    /**
     * Check booking status (Public)
     */
    public function status(string $code): JsonResponse
    {
        $booking = Booking::with(['tukangCukur', 'service'])
            ->where('booking_code', $code)
            ->first();

        if (!$booking) {
            return $this->notFound('Booking tidak ditemukan');
        }

        // Calculate estimated wait time
        $waitingCount = Booking::where('booking_date', $booking->booking_date)
            ->where('queue_number', '<', $booking->queue_number)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->count();

        $estimatedWaitMinutes = $waitingCount * ($booking->service->duration_minutes ?? 30);

        return $this->success([
            'booking_code' => $booking->booking_code,
            'customer_name' => $booking->customer_name,
            'queue_number' => $booking->queue_number,
            'status' => $booking->status,
            'booking_date' => $booking->booking_date->format('Y-m-d'),
            'booking_time' => $booking->booking_time,
            'service' => $booking->service->name,
            'tukang_cukur' => $booking->tukangCukur->name,
            'waiting_count' => $waitingCount,
            'estimated_wait_minutes' => $estimatedWaitMinutes,
        ], 'Status booking berhasil diambil');
    }

    /**
     * Cancel booking (Public)
     */
    public function cancel(Request $request, string $code): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $booking = Booking::where('booking_code', $code)
            ->where('customer_phone', $request->customer_phone)
            ->first();

        if (!$booking) {
            return $this->notFound('Booking tidak ditemukan atau nomor HP tidak cocok');
        }

        if (!$booking->canBeCancelled()) {
            return $this->error('Booking tidak dapat dibatalkan', 422);
        }

        $oldStatus = $booking->status;
        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        // Log activity
        Log::record(
            Log::ACTION_STATUS_UPDATED,
            "Booking {$booking->booking_code} dibatalkan oleh customer",
            $booking,
            ['status' => $oldStatus],
            ['status' => Booking::STATUS_CANCELLED]
        );

        return $this->success([
            'booking_code' => $booking->booking_code,
            'status' => $booking->status,
        ], 'Booking berhasil dibatalkan');
    }
}
