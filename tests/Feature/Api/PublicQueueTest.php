<?php

use App\Models\Booking;

describe('Public Queue API', function () {
    it('can get today queue', function () {
        Booking::factory()->count(3)->create([
            'booking_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->getJson('/api/queue/today');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'date',
                    'queue_list',
                    'total_waiting',
                ]
            ])
            ->assertJson(['success' => true]);
    });

    it('returns empty queue when no bookings today', function () {
        $response = $this->getJson('/api/queue/today');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_waiting' => 0,
                ]
            ]);
    });
});
