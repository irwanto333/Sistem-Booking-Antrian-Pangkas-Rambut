<?php

use App\Models\Service;

beforeEach(function () {
    // Fresh database for each test
});

describe('Public Service API', function () {
    it('can get all active services', function () {
        // Create some services
        Service::factory()->count(3)->create(['is_active' => true]);
        Service::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/services');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'duration_minutes',
                        'is_active',
                    ]
                ]
            ])
            ->assertJson(['success' => true]);

        // Should only return active services
        expect($response->json('data'))->toHaveCount(3);
    });

    it('returns empty array when no active services', function () {
        Service::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/services');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    });
});
