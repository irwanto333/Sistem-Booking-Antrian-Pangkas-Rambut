<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends BaseController
{
    /**
     * Get daily report
     */
    public function daily(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());

        $bookings = Booking::with(['tukangCukur', 'service'])
            ->whereDate('booking_date', $date)
            ->get();

        $completed = $bookings->where('status', Booking::STATUS_COMPLETED);
        $revenue = $completed->sum(fn($b) => $b->service->price);

        // Group by service
        $byService = $completed->groupBy('service_id')->map(fn($items) => [
            'service' => $items->first()->service->name,
            'count' => $items->count(),
            'revenue' => $items->sum(fn($b) => $b->service->price),
        ])->values();

        // Group by tukang cukur
        $byTukangCukur = $completed->groupBy('tukang_cukur_id')->map(fn($items) => [
            'tukang_cukur' => $items->first()->tukangCukur->name,
            'count' => $items->count(),
            'revenue' => $items->sum(fn($b) => $b->service->price),
        ])->values();

        return $this->success([
            'date' => $date,
            'summary' => [
                'total_bookings' => $bookings->count(),
                'completed' => $completed->count(),
                'cancelled' => $bookings->where('status', Booking::STATUS_CANCELLED)->count(),
                'pending' => $bookings->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])->count(),
                'total_revenue' => $revenue,
            ],
            'by_service' => $byService,
            'by_tukang_cukur' => $byTukangCukur,
        ], 'Laporan harian berhasil diambil');
    }

    /**
     * Get weekly report
     */
    public function weekly(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());

        $bookings = Booking::with(['tukangCukur', 'service'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->get();

        $completed = $bookings->where('status', Booking::STATUS_COMPLETED);
        $revenue = $completed->sum(fn($b) => $b->service->price);

        // Daily breakdown
        $dailyBreakdown = [];
        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($currentDate <= $end) {
            $dateStr = $currentDate->toDateString();
            $dayBookings = $bookings->where('booking_date', $dateStr);
            $dayCompleted = $dayBookings->where('status', Booking::STATUS_COMPLETED);

            $dailyBreakdown[] = [
                'date' => $dateStr,
                'day_name' => $currentDate->isoFormat('dddd'),
                'total_bookings' => $dayBookings->count(),
                'completed' => $dayCompleted->count(),
                'revenue' => $dayCompleted->sum(fn($b) => $b->service->price),
            ];

            $currentDate->addDay();
        }

        return $this->success([
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => [
                'total_bookings' => $bookings->count(),
                'completed' => $completed->count(),
                'cancelled' => $bookings->where('status', Booking::STATUS_CANCELLED)->count(),
                'total_revenue' => $revenue,
                'average_daily_revenue' => $revenue / max(count($dailyBreakdown), 1),
            ],
            'daily_breakdown' => $dailyBreakdown,
        ], 'Laporan mingguan berhasil diambil');
    }

    /**
     * Get monthly report
     */
    public function monthly(Request $request): JsonResponse
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $bookings = Booking::with(['tukangCukur', 'service'])
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->get();

        $completed = $bookings->where('status', Booking::STATUS_COMPLETED);
        $revenue = $completed->sum(fn($b) => $b->service->price);

        // Weekly breakdown
        $weeklyBreakdown = [];
        $currentWeek = $startDate->copy();

        while ($currentWeek <= $endDate) {
            $weekStart = $currentWeek->copy()->startOfWeek();
            $weekEnd = $currentWeek->copy()->endOfWeek();

            // Clamp to month boundaries
            if ($weekStart < $startDate) $weekStart = $startDate->copy();
            if ($weekEnd > $endDate) $weekEnd = $endDate->copy();

            $weekBookings = $bookings->filter(fn($b) =>
                $b->booking_date >= $weekStart && $b->booking_date <= $weekEnd
            );
            $weekCompleted = $weekBookings->where('status', Booking::STATUS_COMPLETED);

            $weeklyBreakdown[] = [
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'total_bookings' => $weekBookings->count(),
                'completed' => $weekCompleted->count(),
                'revenue' => $weekCompleted->sum(fn($b) => $b->service->price),
            ];

            $currentWeek->addWeek();
        }

        // Top services
        $topServices = $completed->groupBy('service_id')
            ->map(fn($items) => [
                'service' => $items->first()->service->name,
                'count' => $items->count(),
                'revenue' => $items->sum(fn($b) => $b->service->price),
            ])
            ->sortByDesc('count')
            ->values()
            ->take(5);

        // Top tukang cukur
        $topTukangCukur = $completed->groupBy('tukang_cukur_id')
            ->map(fn($items) => [
                'tukang_cukur' => $items->first()->tukangCukur->name,
                'count' => $items->count(),
                'revenue' => $items->sum(fn($b) => $b->service->price),
            ])
            ->sortByDesc('count')
            ->values();

        return $this->success([
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => $startDate->isoFormat('MMMM'),
            ],
            'summary' => [
                'total_bookings' => $bookings->count(),
                'completed' => $completed->count(),
                'cancelled' => $bookings->where('status', Booking::STATUS_CANCELLED)->count(),
                'total_revenue' => $revenue,
                'average_daily_revenue' => $revenue / $endDate->day,
            ],
            'weekly_breakdown' => $weeklyBreakdown,
            'top_services' => $topServices,
            'top_tukang_cukur' => $topTukangCukur,
        ], 'Laporan bulanan berhasil diambil');
    }
}
