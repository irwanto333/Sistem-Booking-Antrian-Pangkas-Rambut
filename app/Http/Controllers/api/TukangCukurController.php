<?php

namespace App\Http\Controllers\Api;

use App\Models\TukangCukur;
use Illuminate\Http\JsonResponse;

class TukangCukurController extends BaseController
{
    /**
     * Get all active tukang cukur (Public)
     */
    public function index(): JsonResponse
    {
        $tukangCukurs = TukangCukur::active()
            ->orderBy('name')
            ->get();

        return $this->success($tukangCukurs, 'Daftar tukang cukur berhasil diambil');
    }
}
