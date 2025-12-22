<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Admin;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    /**
     * Login admin
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('admin')->attempt($credentials)) {
            return $this->unauthorized('Email atau password salah');
        }

        $admin = auth('admin')->user();

        // Log activity
        Log::record(
            Log::ACTION_LOGIN,
            "Admin {$admin->name} berhasil login"
        );

        return $this->success([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ], 'Login berhasil');
    }

    /**
     * Logout admin
     */
    public function logout(): JsonResponse
    {
        $admin = auth('admin')->user();

        // Log activity
        Log::record(
            Log::ACTION_LOGOUT,
            "Admin {$admin->name} logout"
        );

        auth('admin')->logout();

        return $this->success(null, 'Logout berhasil');
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        $token = auth('admin')->refresh();

        return $this->success([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ], 'Token berhasil diperbarui');
    }

    /**
     * Get authenticated admin
     */
    public function me(): JsonResponse
    {
        $admin = auth('admin')->user();

        return $this->success([
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
        ], 'Data admin berhasil diambil');
    }
}
