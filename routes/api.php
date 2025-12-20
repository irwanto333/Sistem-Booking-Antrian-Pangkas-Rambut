<?php

use Illuminate\Support\Facades\Route;

// Public Controllers
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TukangCukurController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\QueueController;

// Admin Controllers
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\TukangCukurController as AdminTukangCukurController;
use App\Http\Controllers\Api\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Api\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Api\Admin\QueueController as AdminQueueController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\LogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public Routes (Customer - No Authentication Required)
|
*/

// Services - List active services
Route::get('/services', [ServiceController::class, 'index']);

// Tukang Cukur - List active tukang cukur
Route::get('/tukang-cukurs', [TukangCukurController::class, 'index']);

// Schedules - Get available schedules
Route::get('/schedules/available', [ScheduleController::class, 'available']);

// Bookings - Public booking operations
Route::prefix('bookings')->group(function () {
    Route::post('/', [BookingController::class, 'store']);
    Route::get('/{code}/status', [BookingController::class, 'status']);
    Route::post('/{code}/cancel', [BookingController::class, 'cancel']);
});

// Queue - View today's queue
Route::get('/queue/today', [QueueController::class, 'today']);

/*
|--------------------------------------------------------------------------
| Admin Routes (Authentication Required)
|--------------------------------------------------------------------------
*/

// Auth Routes (public)
Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Admin Routes
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Services CRUD
    Route::apiResource('services', AdminServiceController::class);

    // Tukang Cukur CRUD
    Route::apiResource('tukang-cukurs', AdminTukangCukurController::class);

    // Schedules CRUD
    Route::apiResource('schedules', AdminScheduleController::class)->except(['show']);

    // Bookings Management
    Route::get('/bookings', [AdminBookingController::class, 'index']);
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show']);
    Route::put('/bookings/{id}/status', [AdminBookingController::class, 'updateStatus']);
    Route::delete('/bookings/{id}', [AdminBookingController::class, 'destroy']);

    // Queue Management
    Route::get('/queue', [AdminQueueController::class, 'index']);
    Route::post('/queue/next', [AdminQueueController::class, 'next']);
    Route::post('/queue/reset', [AdminQueueController::class, 'reset']);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily']);
        Route::get('/weekly', [ReportController::class, 'weekly']);
        Route::get('/monthly', [ReportController::class, 'monthly']);
    });

    // Logs
    Route::get('/logs', [LogController::class, 'index']);
    Route::get('/logs/{id}', [LogController::class, 'show']);
    Route::delete('/logs/{id}', [LogController::class, 'destroy']);
    Route::delete('/logs', [LogController::class, 'clear']);
});
