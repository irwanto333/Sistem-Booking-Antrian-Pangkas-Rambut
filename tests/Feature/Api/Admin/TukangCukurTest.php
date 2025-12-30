<?php

use App\Models\Admin;
use App\Models\Booking;
use App\Models\TukangCukur;

describe('Admin Tukang Cukur API', function () {
    beforeEach(function () {
        $this->admin = Admin::factory()->create();
        $this->token = auth('admin')->login($this->admin);
    });

    it('can get all tukang cukur', function () {
        TukangCukur::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/tukang-cukurs');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can create a tukang cukur', function () {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/admin/tukang-cukurs', [
                'name' => 'Test Tukang Cukur',
                'phone' => '081234567890',
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.name', 'Test Tukang Cukur');

        $this->assertDatabaseHas('tukang_cukurs', [
            'name' => 'Test Tukang Cukur',
        ]);
    });

    it('can get a tukang cukur detail', function () {
        $tukangCukur = TukangCukur::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/admin/tukang-cukurs/{$tukangCukur->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', $tukangCukur->id);
    });

    it('can update a tukang cukur', function () {
        $tukangCukur = TukangCukur::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/admin/tukang-cukurs/{$tukangCukur->id}", [
                'name' => 'Updated Tukang Cukur',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('tukang_cukurs', [
            'id' => $tukangCukur->id,
            'name' => 'Updated Tukang Cukur',
        ]);
    });

    it('can delete a tukang cukur', function () {
        $tukangCukur = TukangCukur::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/admin/tukang-cukurs/{$tukangCukur->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('tukang_cukurs', [
            'id' => $tukangCukur->id,
        ]);
    });

    it('cannot delete tukang cukur with bookings', function () {
        $tukangCukur = TukangCukur::factory()->create();
        Booking::factory()->create(['tukang_cukur_id' => $tukangCukur->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/admin/tukang-cukurs/{$tukangCukur->id}");

        $response->assertStatus(422);
    });
});
