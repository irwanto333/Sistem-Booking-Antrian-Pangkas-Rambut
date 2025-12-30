<?php

use App\Models\Admin;
use App\Models\Booking;

describe('Admin Queue API', function () {
    beforeEach(function () {
        $this->admin = Admin::factory()->create();
        $this->token = auth('admin')->login($this->admin);
    });

    it('can get current queue', function () {
        Booking::factory()->count(3)->create([
            'booking_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/queue');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'date',
                    'current_serving',
                    'waiting_list',
                    'total_waiting',
                ],
            ]);
    });

    it('can call next queue', function () {
        $booking = Booking::factory()->create([
            'booking_date' => now()->toDateString(),
            'status' => 'pending',
            'queue_number' => 1,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/admin/queue/next');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'in_progress',
        ]);
    });

    it('can reset daily queue', function () {
        Booking::factory()->count(3)->create([
            'booking_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/admin/queue/reset');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });
});
