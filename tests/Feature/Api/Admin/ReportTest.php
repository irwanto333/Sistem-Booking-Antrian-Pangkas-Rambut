<?php

use App\Models\Admin;
use App\Models\Booking;

describe('Admin Report API', function () {
    beforeEach(function () {
        $this->admin = Admin::factory()->create();
        $this->token = auth('admin')->login($this->admin);
    });

    it('can get daily report', function () {
        Booking::factory()->count(5)->create([
            'booking_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/reports/daily');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'date',
                    'summary',
                    'by_service',
                    'by_tukang_cukur',
                ],
            ]);
    });

    it('can get daily report for specific date', function () {
        $date = now()->subDays(2)->toDateString();
        Booking::factory()->count(3)->create([
            'booking_date' => $date,
            'status' => 'completed',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/admin/reports/daily?date={$date}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.date', $date);
    });

    it('can get weekly report', function () {
        Booking::factory()->count(10)->create([
            'booking_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/reports/weekly');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'period',
                    'summary',
                    'daily_breakdown',
                ],
            ]);
    });

    it('can get monthly report', function () {
        Booking::factory()->count(15)->create([
            'booking_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/reports/monthly');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'period',
                    'summary',
                    'weekly_breakdown',
                    'top_services',
                    'top_tukang_cukur',
                ],
            ]);
    });

    it('can get monthly report for specific month', function () {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/reports/monthly?month=11&year=2025');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.period.month', '11')
            ->assertJsonPath('data.period.year', '2025');
    });
});
