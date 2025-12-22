<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\FavoritePoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FavoritePointApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_paginated_list(): void
    {
        FavoritePoint::factory()->count(3)->create();

        $response = $this->getJson('/api/favorite-points');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'is_active', 'sort_order'],
                ],
                'links',
                'meta',
            ]);
    }

    #[Test]
    public function show_returns_single_resource(): void
    {
        $model = FavoritePoint::factory()->create();

        $response = $this->getJson("/api/favorite-points/{$model->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'is_active', 'sort_order'],
            ]);
    }

    #[Test]
    public function store_creates_new_resource(): void
    {
        $data = [
            'name' => 'Test name',
            'is_active' => true,
            'sort_order' => 1,
        ];

        $response = $this->postJson('/api/favorite-points', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'is_active', 'sort_order'],
            ]);

        $this->assertDatabaseHas('favorite_points', $data);
    }

    #[Test]
    public function store_returns_validation_error(): void
    {
        $response = $this->postJson('/api/favorite-points', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'is_active', 'sort_order']);
    }

    #[Test]
    public function update_modifies_existing_resource(): void
    {
        $model = FavoritePoint::factory()->create();

        $data = [
            'name' => 'Test name',
        ];

        $response = $this->putJson("/api/favorite-points/{$model->id}", $data);

        $response->assertOk();

        $this->assertDatabaseHas('favorite_points', array_merge(['id' => $model->id], $data));
    }

    #[Test]
    public function destroy_deletes_resource(): void
    {
        $model = FavoritePoint::factory()->create();

        $response = $this->deleteJson("/api/favorite-points/{$model->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('favorite_points', ['id' => $model->id]);
    }
}
