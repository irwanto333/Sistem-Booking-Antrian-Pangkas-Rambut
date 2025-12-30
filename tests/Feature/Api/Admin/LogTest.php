<?php

use App\Models\Admin;
use App\Models\Log;

describe('Admin Log API', function () {
    beforeEach(function () {
        $this->admin = Admin::factory()->create();
        $this->token = auth('admin')->login($this->admin);
    });

    it('can get all logs', function () {
        Log::factory()->count(10)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/logs');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can filter logs by action', function () {
        Log::factory()->create(['action' => 'create']);
        Log::factory()->create(['action' => 'update']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/logs?action=create');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can get log detail', function () {
        $log = Log::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/admin/logs/{$log->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', $log->id);
    });

    it('can delete a log', function () {
        $log = Log::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/admin/logs/{$log->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('logs', [
            'id' => $log->id,
        ]);
    });

    it('can clear old logs', function () {
        Log::factory()->count(5)->create([
            'created_at' => now()->subDays(40),
        ]);
        Log::factory()->count(3)->create([
            'created_at' => now(),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson('/api/admin/logs?days=30');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });
});
