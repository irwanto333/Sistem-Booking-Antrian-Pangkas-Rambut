<?php

use App\Models\Schedule;
use App\Models\TukangCukur;

describe('Public Schedule API', function () {
    it('can get available schedules', function () {
        $tukangCukur = TukangCukur::factory()->create(['is_active' => true]);

        // Get today's day of week (0 = Sunday, 6 = Saturday)
        $dayOfWeek = now()->dayOfWeek;

        Schedule::factory()->create([
            'tukang_cukur_id' => $tukangCukur->id,
            'day_of_week' => $dayOfWeek,
            'open_time' => '08:00',
            'close_time' => '20:00',
            'is_available' => true,
        ]);

        $response = $this->getJson('/api/schedules/available?date=' . now()->toDateString());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson(['success' => true]);
    });

    it('can filter schedules by tukang cukur id', function () {
        $tukangCukur1 = TukangCukur::factory()->create(['is_active' => true]);
        $tukangCukur2 = TukangCukur::factory()->create(['is_active' => true]);

        $dayOfWeek = now()->dayOfWeek;

        Schedule::factory()->create([
            'tukang_cukur_id' => $tukangCukur1->id,
            'day_of_week' => $dayOfWeek,
            'is_available' => true,
        ]);

        Schedule::factory()->create([
            'tukang_cukur_id' => $tukangCukur2->id,
            'day_of_week' => $dayOfWeek,
            'is_available' => true,
        ]);

        $response = $this->getJson('/api/schedules/available?date=' . now()->toDateString() . '&tukang_cukur_id=' . $tukangCukur1->id);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('returns schedules for today when no date specified', function () {
        $response = $this->getJson('/api/schedules/available');

        $response->assertStatus(200);
    });
});
