<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends BaseController
{
    /**
     * Get all active services (Public)
     */
    public function index(): JsonResponse
    {
        $services = Service::active()
            ->orderBy('name')
            ->get();

        return $this->success($services, 'Daftar layanan berhasil diambil');
    }
}
