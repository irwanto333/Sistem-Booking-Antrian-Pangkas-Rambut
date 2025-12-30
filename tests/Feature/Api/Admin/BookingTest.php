<?php

use App\Models\Admin;
use App\Models\Booking;

describe('Admin Booking API', function () {
    beforeEach(function () {
        $this->admin = Admin::factory()->create();
        $this->token = auth('admin')->login($this->admin);
    });

    it('can get all bookings', function () {
        Booking::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/bookings');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can filter bookings by date', function () {
        Booking::factory()->create(['booking_date' => now()->toDateString()]);
        Booking::factory()->create(['booking_date' => now()->addDay()->toDateString()]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/bookings?date=' . now()->toDateString());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can filter bookings by status', function () {
        Booking::factory()->create(['status' => 'pending']);
        Booking::factory()->create(['status' => 'completed']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/bookings?status=pending');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can get booking detail', function () {
        $booking = Booking::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/admin/bookings/{$booking->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', $booking->id);
    });

    it('can update booking status', function () {
        $booking = Booking::factory()->create(['status' => 'pending']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/admin/bookings/{$booking->id}/status", [
                'status' => 'confirmed',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    });

    it('validates status value', function () {
        $booking = Booking::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/admin/bookings/{$booking->id}/status", [
                'status' => 'invalid_status',
            ]);

        $response->assertStatus(422);
    });

    it('can delete a booking', function () {
        $booking = Booking::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/admin/bookings/{$booking->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
        ]);
    });
});
