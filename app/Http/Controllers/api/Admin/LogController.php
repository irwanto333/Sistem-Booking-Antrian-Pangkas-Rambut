<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogController extends BaseController
{
    /**
     * Get all logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = Log::with('admin');

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by action
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Filter by admin
        if ($request->has('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return $this->success($logs, 'Daftar log berhasil diambil');
    }

    /**
     * Get log detail
     */
    public function show(int $id): JsonResponse
    {
        $log = Log::with('admin')->find($id);

        if (!$log) {
            return $this->notFound('Log tidak ditemukan');
        }

        return $this->success($log, 'Detail log berhasil diambil');
    }

    /**
     * Delete log
     */
    public function destroy(int $id): JsonResponse
    {
        $log = Log::find($id);

        if (!$log) {
            return $this->notFound('Log tidak ditemukan');
        }

        $log->delete();

        return $this->success(null, 'Log berhasil dihapus');
    }

    /**
     * Clear old logs
     */
    public function clear(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);

        $deleted = Log::where('created_at', '<', now()->subDays($days))->delete();

        Log::record(
            Log::ACTION_DELETE,
            "Log lama dihapus. {$deleted} log yang lebih dari {$days} hari dihapus."
        );

        return $this->success([
            'deleted_count' => $deleted,
            'days' => $days,
        ], "Log yang lebih dari {$days} hari berhasil dihapus");
    }
}
