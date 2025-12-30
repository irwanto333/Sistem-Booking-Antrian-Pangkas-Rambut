<?php

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Service;

describe('Admin Service API', function () {
    beforeEach(function () {
        $this->admin = Admin::factory()->create();
        $this->token = auth('admin')->login($this->admin);
    });

    it('can get all services', function () {
        Service::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/admin/services');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can create a service', function () {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/admin/services', [
                'name' => 'Test Service',
                'description' => 'Test Description',
                'price' => 50000,
                'duration_minutes' => 30,
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.name', 'Test Service');

        $this->assertDatabaseHas('services', [
            'name' => 'Test Service',
        ]);
    });

    it('validates service creation', function () {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/admin/services', []);

        $response->assertStatus(422);
    });

    it('can get a service detail', function () {
        $service = Service::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/admin/services/{$service->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', $service->id);
    });

    it('can update a service', function () {
        $service = Service::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/admin/services/{$service->id}", [
                'name' => 'Updated Service',
                'price' => 75000,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Service',
        ]);
    });

    it('can delete a service', function () {
        $service = Service::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/admin/services/{$service->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    });

    it('cannot delete service with bookings', function () {
        $service = Service::factory()->create();
        Booking::factory()->create(['service_id' => $service->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/admin/services/{$service->id}");

        $response->assertStatus(422);
    });
});
