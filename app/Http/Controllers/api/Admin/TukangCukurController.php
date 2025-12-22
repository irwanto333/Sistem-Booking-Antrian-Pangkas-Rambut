<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Log;
use App\Models\TukangCukur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TukangCukurController extends BaseController
{
    /**
     * Get all tukang cukur
     */
    public function index(): JsonResponse
    {
        $tukangCukurs = TukangCukur::with('schedules')->orderBy('name')->get();

        return $this->success($tukangCukurs, 'Daftar tukang cukur berhasil diambil');
    }

    /**
     * Create new tukang cukur
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $data = $request->only(['name', 'phone', 'is_active']);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('tukang-cukur', 'public');
        }

        $tukangCukur = TukangCukur::create($data);

        // Log activity
        Log::record(
            Log::ACTION_CREATE,
            "Tukang cukur baru dibuat: {$tukangCukur->name}",
            $tukangCukur,
            null,
            $tukangCukur->toArray()
        );

        return $this->created($tukangCukur, 'Tukang cukur berhasil dibuat');
    }

    /**
     * Get tukang cukur detail
     */
    public function show(int $id): JsonResponse
    {
        $tukangCukur = TukangCukur::with('schedules')->find($id);

        if (!$tukangCukur) {
            return $this->notFound('Tukang cukur tidak ditemukan');
        }

        return $this->success($tukangCukur, 'Detail tukang cukur berhasil diambil');
    }

    /**
     * Update tukang cukur
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $tukangCukur = TukangCukur::find($id);

        if (!$tukangCukur) {
            return $this->notFound('Tukang cukur tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $oldValues = $tukangCukur->toArray();
        $data = $request->only(['name', 'phone', 'is_active']);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($tukangCukur->photo) {
                Storage::disk('public')->delete($tukangCukur->photo);
            }
            $data['photo'] = $request->file('photo')->store('tukang-cukur', 'public');
        }

        $tukangCukur->update($data);

        // Log activity
        Log::record(
            Log::ACTION_UPDATE,
            "Tukang cukur diupdate: {$tukangCukur->name}",
            $tukangCukur,
            $oldValues,
            $tukangCukur->fresh()->toArray()
        );

        return $this->success($tukangCukur->fresh(), 'Tukang cukur berhasil diupdate');
    }

    /**
     * Delete tukang cukur
     */
    public function destroy(int $id): JsonResponse
    {
        $tukangCukur = TukangCukur::find($id);

        if (!$tukangCukur) {
            return $this->notFound('Tukang cukur tidak ditemukan');
        }

        // Check if tukang cukur has bookings
        if ($tukangCukur->bookings()->exists()) {
            return $this->error('Tukang cukur tidak dapat dihapus karena sudah memiliki booking', 422);
        }

        $oldValues = $tukangCukur->toArray();

        // Delete photo
        if ($tukangCukur->photo) {
            Storage::disk('public')->delete($tukangCukur->photo);
        }

        // Log activity
        Log::record(
            Log::ACTION_DELETE,
            "Tukang cukur dihapus: {$tukangCukur->name}",
            $tukangCukur,
            $oldValues
        );

        $tukangCukur->delete();

        return $this->success(null, 'Tukang cukur berhasil dihapus');
    }
}
