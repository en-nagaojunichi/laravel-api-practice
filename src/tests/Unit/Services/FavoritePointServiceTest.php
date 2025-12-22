<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\FavoritePointDTO;
use App\Models\FavoritePoint;
use App\Services\FavoritePointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FavoritePointServiceTest extends TestCase
{
    use RefreshDatabase;

    private FavoritePointService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FavoritePointService::class);
    }

    #[Test]
    public function paginate_returns_paginated_results(): void
    {
        FavoritePoint::factory()->count(5)->create();

        $result = $this->service->paginate(10);

        $this->assertCount(5, $result->items());
    }

    #[Test]
    public function create_stores_new_record(): void
    {
        $dto = FavoritePointDTO::fromArray([
            'name' => 'Test name',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $model = $this->service->create($dto);

        $this->assertInstanceOf(FavoritePoint::class, $model);
        $this->assertDatabaseHas('favorite_points', [
            'name' => 'Test name',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    #[Test]
    public function update_modifies_existing_record(): void
    {
        $model = FavoritePoint::factory()->create();

        $dto = FavoritePointDTO::fromArray([
            'name' => 'Test name',
        ]);

        $updated = $this->service->update($model, $dto);

        $this->assertInstanceOf(FavoritePoint::class, $updated);
    }

    #[Test]
    public function delete_removes_record(): void
    {
        $model = FavoritePoint::factory()->create();
        $id = $model->id;

        $this->service->delete($model);

        $this->assertDatabaseMissing('favorite_points', ['id' => $id]);
    }
}
