<?php

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\TukangCukur;

describe('Public Booking API', function () {
    it('can create a booking', function () {
        $service = Service::factory()->create(['is_active' => true, 'duration_minutes' => 30]);
        $tukangCukur = TukangCukur::factory()->create(['is_active' => true]);

        // Tomorrow's date
        $bookingDate = now()->addDay()->toDateString();
        $dayOfWeek = now()->addDay()->dayOfWeek;

        Schedule::factory()->create([
            'tukang_cukur_id' => $tukangCukur->id,
            'day_of_week' => $dayOfWeek,
            'open_time' => '08:00',
            'close_time' => '20:00',
            'is_available' => true,
        ]);

        $response = $this->postJson('/api/bookings', [
            'customer_name' => 'John Doe',
            'customer_phone' => '081234567890',
            'tukang_cukur_id' => $tukangCukur->id,
            'service_id' => $service->id,
            'booking_date' => $bookingDate,
            'booking_time' => '10:00',
            'notes' => 'Test booking',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'booking_code',
                    'customer_name',
                    'booking_date',
                    'booking_time',
                    'queue_number',
                    'status',
                ]
            ])
            ->assertJson(['success' => true]);
    });

    it('validates required fields when creating booking', function () {
        $response = $this->postJson('/api/bookings', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    });

    it('can check booking status by code', function () {
        $booking = Booking::factory()->create();

        $response = $this->getJson("/api/bookings/{$booking->booking_code}/status");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'booking_code',
                    'status',
                    'queue_number',
                ]
            ])
            ->assertJson(['success' => true]);
    });

    it('returns 404 for non-existent booking code', function () {
        $response = $this->getJson('/api/bookings/INVALID-CODE/status');

        $response->assertStatus(404)
            ->assertJson(['success' => false]);
    });

    it('can cancel a booking with valid phone', function () {
        $booking = Booking::factory()->create([
            'status' => 'pending',
            'booking_date' => now()->addDay()->toDateString(),
        ]);

        $response = $this->postJson("/api/bookings/{$booking->booking_code}/cancel", [
            'customer_phone' => $booking->customer_phone,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        expect(Booking::find($booking->id)->status)->toBe('cancelled');
    });

    it('cannot cancel booking with wrong phone', function () {
        $booking = Booking::factory()->create([
            'status' => 'pending',
            'customer_phone' => '081234567890',
        ]);

        $response = $this->postJson("/api/bookings/{$booking->booking_code}/cancel", [
            'customer_phone' => '089999999999',
        ]);

        $response->assertStatus(404);
    });
});
