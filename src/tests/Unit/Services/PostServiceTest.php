<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\PostDTO;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    private PostService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PostService::class);
    }

    #[Test]
    public function paginate_returns_paginated_results(): void
    {
        Post::factory()->count(5)->create();

        $result = $this->service->paginate(10);

        $this->assertCount(5, $result->items());
    }

    #[Test]
    public function create_stores_new_record(): void
    {
        $dto = PostDTO::fromArray([
            'user_id' => 1,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ]);

        $model = $this->service->create($dto);

        $this->assertInstanceOf(Post::class, $model);
        $this->assertDatabaseHas('posts', [
            'user_id' => 1,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ]);
    }

    #[Test]
    public function update_modifies_existing_record(): void
    {
        $model = Post::factory()->create();

        $dto = PostDTO::fromArray([
            'user_id' => 1,
        ]);

        $updated = $this->service->update($model, $dto);

        $this->assertInstanceOf(Post::class, $updated);
    }

    #[Test]
    public function delete_removes_record(): void
    {
        $model = Post::factory()->create();
        $id = $model->id;

        $this->service->delete($model);

        $this->assertDatabaseMissing('posts', ['id' => $id]);
    }
}