<?php

use App\Models\Admin;

describe('Admin Auth API', function () {
    it('can login with valid credentials', function () {
        $admin = Admin::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'expires_in',
                    'admin' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ])
            ->assertJson(['success' => true]);
    });

    it('cannot login with invalid credentials', function () {
        $admin = Admin::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    });

    it('validates login request', function () {
        $response = $this->postJson('/api/admin/login', []);

        $response->assertStatus(422);
    });

    it('can logout with valid token', function () {
        $admin = Admin::factory()->create();
        $token = auth('admin')->login($admin);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/admin/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    });

    it('can get current admin info', function () {
        $admin = Admin::factory()->create();
        $token = auth('admin')->login($admin);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
            ]);
    });

    it('cannot access protected route without token', function () {
        $response = $this->getJson('/api/admin/me');

        $response->assertStatus(401);
    });
});
