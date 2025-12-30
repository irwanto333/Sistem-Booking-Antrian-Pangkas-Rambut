<?php

use App\Models\Admin;
use App\Models\Booking;

describe('Admin Dashboard API', function () {
    it('can get dashboard data', function () {
        $admin = Admin::factory()->create();
        $token = auth('admin')->login($admin);

        // Create some bookings
        Booking::factory()->count(3)->create([
            'booking_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'today',
                    'weekly',
                    'monthly',
                ],
            ])
            ->assertJson(['success' => true]);
    });

    it('cannot access dashboard without auth', function () {
        $response = $this->getJson('/api/admin/dashboard');

        $response->assertStatus(401);
    });
});
