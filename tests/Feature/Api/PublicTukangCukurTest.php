<?php

use App\Models\TukangCukur;

describe('Public Tukang Cukur API', function () {
    it('can get all active tukang cukur', function () {
        TukangCukur::factory()->count(3)->create(['is_active' => true]);
        TukangCukur::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/tukang-cukurs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'phone',
                        'is_active',
                    ]
                ]
            ])
            ->assertJson(['success' => true]);

        expect($response->json('data'))->toHaveCount(3);
    });

    it('returns empty array when no active tukang cukur', function () {
        TukangCukur::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/tukang-cukurs');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    });
});
