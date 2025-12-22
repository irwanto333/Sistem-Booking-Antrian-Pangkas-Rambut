<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Log;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends BaseController
{
    /**
     * Get all schedules
     */
    public function index(): JsonResponse
    {
        $schedules = Schedule::with('tukangCukur')
            ->orderBy('tukang_cukur_id')
            ->orderBy('day_of_week')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'tukang_cukur_id' => $schedule->tukang_cukur_id,
                    'tukang_cukur' => $schedule->tukangCukur,
                    'day_of_week' => $schedule->day_of_week,
                    'day_name' => $schedule->day_name,
                    'open_time' => $schedule->open_time,
                    'close_time' => $schedule->close_time,
                    'is_available' => $schedule->is_available,
                ];
            });

        return $this->success($schedules, 'Daftar jadwal berhasil diambil');
    }

    /**
     * Create new schedule
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tukang_cukur_id' => 'required|exists:tukang_cukurs,id',
            'day_of_week' => 'required|integer|between:0,6',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i|after:open_time',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Check if schedule already exists
        $existing = Schedule::where('tukang_cukur_id', $request->tukang_cukur_id)
            ->where('day_of_week', $request->day_of_week)
            ->first();

        if ($existing) {
            return $this->error('Jadwal untuk hari tersebut sudah ada', 422);
        }

        $schedule = Schedule::create($request->only([
            'tukang_cukur_id',
            'day_of_week',
            'open_time',
            'close_time',
            'is_available',
        ]));

        $schedule->load('tukangCukur');

        // Log activity
        Log::record(
            Log::ACTION_CREATE,
            "Jadwal baru dibuat untuk {$schedule->tukangCukur->name} - {$schedule->day_name}",
            $schedule,
            null,
            $schedule->toArray()
        );

        return $this->created([
            'id' => $schedule->id,
            'tukang_cukur_id' => $schedule->tukang_cukur_id,
            'tukang_cukur' => $schedule->tukangCukur,
            'day_of_week' => $schedule->day_of_week,
            'day_name' => $schedule->day_name,
            'open_time' => $schedule->open_time,
            'close_time' => $schedule->close_time,
            'is_available' => $schedule->is_available,
        ], 'Jadwal berhasil dibuat');
    }

    /**
     * Update schedule
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return $this->notFound('Jadwal tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'tukang_cukur_id' => 'sometimes|required|exists:tukang_cukurs,id',
            'day_of_week' => 'sometimes|required|integer|between:0,6',
            'open_time' => 'sometimes|required|date_format:H:i',
            'close_time' => 'sometimes|required|date_format:H:i',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Check for duplicate if changing tukang_cukur_id or day_of_week
        if ($request->has('tukang_cukur_id') || $request->has('day_of_week')) {
            $tukangCukurId = $request->input('tukang_cukur_id', $schedule->tukang_cukur_id);
            $dayOfWeek = $request->input('day_of_week', $schedule->day_of_week);

            $existing = Schedule::where('tukang_cukur_id', $tukangCukurId)
                ->where('day_of_week', $dayOfWeek)
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                return $this->error('Jadwal untuk hari tersebut sudah ada', 422);
            }
        }

        $oldValues = $schedule->toArray();

        $schedule->update($request->only([
            'tukang_cukur_id',
            'day_of_week',
            'open_time',
            'close_time',
            'is_available',
        ]));

        $schedule->load('tukangCukur');

        // Log activity
        Log::record(
            Log::ACTION_UPDATE,
            "Jadwal diupdate: {$schedule->tukangCukur->name} - {$schedule->day_name}",
            $schedule,
            $oldValues,
            $schedule->fresh()->toArray()
        );

        return $this->success([
            'id' => $schedule->id,
            'tukang_cukur_id' => $schedule->tukang_cukur_id,
            'tukang_cukur' => $schedule->tukangCukur,
            'day_of_week' => $schedule->day_of_week,
            'day_name' => $schedule->day_name,
            'open_time' => $schedule->open_time,
            'close_time' => $schedule->close_time,
            'is_available' => $schedule->is_available,
        ], 'Jadwal berhasil diupdate');
    }

    /**
     * Delete schedule
     */
    public function destroy(int $id): JsonResponse
    {
        $schedule = Schedule::with('tukangCukur')->find($id);

        if (!$schedule) {
            return $this->notFound('Jadwal tidak ditemukan');
        }

        $oldValues = $schedule->toArray();

        // Log activity
        Log::record(
            Log::ACTION_DELETE,
            "Jadwal dihapus: {$schedule->tukangCukur->name} - {$schedule->day_name}",
            $schedule,
            $oldValues
        );

        $schedule->delete();

        return $this->success(null, 'Jadwal berhasil dihapus');
    }
}
