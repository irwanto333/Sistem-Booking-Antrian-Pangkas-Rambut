<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Get dashboard data
     */
    public function index(): JsonResponse
    {
        $today = now()->toDateString();

        // Today's bookings
        $todayBookings = Booking::today()->count();
        $todayCompleted = Booking::today()->status(Booking::STATUS_COMPLETED)->count();
        $todayPending = Booking::today()->inQueue()->count();
        $todayCancelled = Booking::today()->status(Booking::STATUS_CANCELLED)->count();

        // Today's revenue
        $todayRevenue = Booking::today()
            ->status(Booking::STATUS_COMPLETED)
            ->with('service')
            ->get()
            ->sum(fn($booking) => $booking->service->price);

        // This week's data
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyBookings = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->status(Booking::STATUS_COMPLETED)
            ->count();
        $weeklyRevenue = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->status(Booking::STATUS_COMPLETED)
            ->with('service')
            ->get()
            ->sum(fn($booking) => $booking->service->price);

        // This month's data
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthlyBookings = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
            ->status(Booking::STATUS_COMPLETED)
            ->count();
        $monthlyRevenue = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
            ->status(Booking::STATUS_COMPLETED)
            ->with('service')
            ->get()
            ->sum(fn($booking) => $booking->service->price);

        // Current queue
        $currentQueue = Booking::today()
            ->where('status', Booking::STATUS_IN_PROGRESS)
            ->first();

        // Popular services
        $popularServices = Booking::selectRaw('service_id, COUNT(*) as total')
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('service')
            ->get()
            ->map(fn($item) => [
                'service' => $item->service->name,
                'total' => $item->total,
            ]);

        return $this->success([
            'today' => [
                'date' => $today,
                'total_bookings' => $todayBookings,
                'completed' => $todayCompleted,
                'pending' => $todayPending,
                'cancelled' => $todayCancelled,
                'revenue' => $todayRevenue,
                'current_queue' => $currentQueue ? $currentQueue->queue_number : null,
            ],
            'weekly' => [
                'total_bookings' => $weeklyBookings,
                'revenue' => $weeklyRevenue,
            ],
            'monthly' => [
                'total_bookings' => $monthlyBookings,
                'revenue' => $monthlyRevenue,
            ],
            'popular_services' => $popularServices,
        ], 'Data dashboard berhasil diambil');
    }
}
