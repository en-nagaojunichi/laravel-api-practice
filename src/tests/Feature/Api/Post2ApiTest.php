<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Post2;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;

final class Post2ApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_paginated_list(): void
    {
        Post2::factory()->count(3)->create();

        $response = $this->getJson('/api/post2s');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'title', 'slug', 'body', 'status', 'published_at'],
                ],
                'links',
                'meta',
            ]);
    }

    #[Test]
    public function show_returns_single_resource(): void
    {
        $model = Post2::factory()->create();

        $response = $this->getJson("/api/post2s/{$model->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'title', 'slug', 'body', 'status', 'published_at'],
            ]);
    }

    #[Test]
    public function store_creates_new_resource(): void
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ];

        $response = $this->postJson('/api/post2s', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'title', 'slug', 'body', 'status', 'published_at'],
            ]);
    }

    #[Test]
    public function store_returns_validation_error(): void
    {
        $response = $this->postJson('/api/post2s', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id', 'title', 'slug', 'status']);
    }

    #[Test]
    public function update_modifies_existing_resource(): void
    {
        $model = Post2::factory()->create();

        $data = [
            'user_id' => $model->user_id,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ];

        $response = $this->putJson("/api/post2s/{$model->id}", $data);

        $response->assertOk();
    }

    #[Test]
    public function destroy_deletes_resource(): void
    {
        $model = Post2::factory()->create();

        $response = $this->deleteJson("/api/post2s/{$model->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('posts', ['id' => $model->id]);
    }
}
