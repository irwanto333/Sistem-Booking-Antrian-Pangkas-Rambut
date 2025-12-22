<?php

namespace App\Http\Controllers\Api;

use App\Models\Schedule;
use App\Models\TukangCukur;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends BaseController
{
    /**
     * Get available schedules (Public)
     */
    public function available(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'nullable|date|after_or_equal:today',
            'tukang_cukur_id' => 'nullable|exists:tukang_cukurs,id',
        ]);

        $date = $request->input('date', now()->toDateString());
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $query = Schedule::with('tukangCukur')
            ->available()
            ->forDay($dayOfWeek)
            ->whereHas('tukangCukur', function ($q) {
                $q->active();
            });

        if ($request->has('tukang_cukur_id')) {
            $query->where('tukang_cukur_id', $request->tukang_cukur_id);
        }

        $schedules = $query->get()->map(function ($schedule) use ($date) {
            return [
                'id' => $schedule->id,
                'date' => $date,
                'day_name' => $schedule->day_name,
                'open_time' => $schedule->open_time,
                'close_time' => $schedule->close_time,
                'tukang_cukur' => $schedule->tukangCukur,
            ];
        });

        return $this->success($schedules, 'Jadwal tersedia berhasil diambil');
    }
}
