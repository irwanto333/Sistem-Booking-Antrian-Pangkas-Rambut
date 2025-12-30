<?php

use App\Models\Admin;
use App\Models\Schedule;
use App\Models\TukangCukur;

describe('Admin Schedule API', function () {
    beforeEach(function () {
        $this->admin = Admin::factory()->create();
        $this->token = auth('admin')->login($this->admin);
    });

    it('can get all schedules', function () {
        $tukangCukur = TukangCukur::factory()->create();
        Schedule::factory()->count(3)->create(['tukang_cukur_id' => $tukangCukur->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/schedules');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can create a schedule', function () {
        $tukangCukur = TukangCukur::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/admin/schedules', [
                'tukang_cukur_id' => $tukangCukur->id,
                'day_of_week' => 1,
                'open_time' => '08:00',
                'close_time' => '17:00',
                'is_available' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('schedules', [
            'tukang_cukur_id' => $tukangCukur->id,
            'day_of_week' => 1,
        ]);
    });

    it('prevents duplicate schedule for same tukang cukur and day', function () {
        $tukangCukur = TukangCukur::factory()->create();
        Schedule::factory()->create([
            'tukang_cukur_id' => $tukangCukur->id,
            'day_of_week' => 1,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/admin/schedules', [
                'tukang_cukur_id' => $tukangCukur->id,
                'day_of_week' => 1,
                'open_time' => '08:00',
                'close_time' => '17:00',
            ]);

        $response->assertStatus(422);
    });

    it('can update a schedule', function () {
        $schedule = Schedule::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/admin/schedules/{$schedule->id}", [
                'open_time' => '09:00',
                'close_time' => '18:00',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can delete a schedule', function () {
        $schedule = Schedule::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/admin/schedules/{$schedule->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('schedules', [
            'id' => $schedule->id,
        ]);
    });
});
