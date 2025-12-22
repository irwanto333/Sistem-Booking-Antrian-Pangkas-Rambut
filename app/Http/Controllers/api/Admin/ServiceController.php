<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Log;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends BaseController
{
    /**
     * Get all services
     */
    public function index(): JsonResponse
    {
        $services = Service::orderBy('name')->get();

        return $this->success($services, 'Daftar layanan berhasil diambil');
    }

    /**
     * Create new service
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $service = Service::create($request->only([
            'name',
            'description',
            'price',
            'duration_minutes',
            'is_active',
        ]));

        // Log activity
        Log::record(
            Log::ACTION_CREATE,
            "Layanan baru dibuat: {$service->name}",
            $service,
            null,
            $service->toArray()
        );

        return $this->created($service, 'Layanan berhasil dibuat');
    }

    /**
     * Get service detail
     */
    public function show(int $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->notFound('Layanan tidak ditemukan');
        }

        return $this->success($service, 'Detail layanan berhasil diambil');
    }

    /**
     * Update service
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->notFound('Layanan tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $oldValues = $service->toArray();

        $service->update($request->only([
            'name',
            'description',
            'price',
            'duration_minutes',
            'is_active',
        ]));

        // Log activity
        Log::record(
            Log::ACTION_UPDATE,
            "Layanan diupdate: {$service->name}",
            $service,
            $oldValues,
            $service->fresh()->toArray()
        );

        return $this->success($service->fresh(), 'Layanan berhasil diupdate');
    }

    /**
     * Delete service
     */
    public function destroy(int $id): JsonResponse
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->notFound('Layanan tidak ditemukan');
        }

        // Check if service has bookings
        if ($service->bookings()->exists()) {
            return $this->error('Layanan tidak dapat dihapus karena sudah memiliki booking', 422);
        }

        $oldValues = $service->toArray();

        // Log activity
        Log::record(
            Log::ACTION_DELETE,
            "Layanan dihapus: {$service->name}",
            $service,
            $oldValues
        );

        $service->delete();

        return $this->success(null, 'Layanan berhasil dihapus');
    }
}
