<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\FavoritePointDTO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FavoritePointDTOTest extends TestCase
{
    #[Test]
    public function from_array_creates_dto_with_values(): void
    {
        $data = [
            'name' => 'Test name',
            'is_active' => true,
            'sort_order' => 1,
        ];

        $dto = FavoritePointDTO::fromArray($data);

        $this->assertSame('Test name', $dto->name);
        $this->assertSame(true, $dto->is_active);
        $this->assertSame(1, $dto->sort_order);
    }

    #[Test]
    public function from_array_uses_defaults_for_missing_values(): void
    {
        $dto = FavoritePointDTO::fromArray([]);

        $this->assertSame('', $dto->name);
        $this->assertSame(false, $dto->is_active);
        $this->assertSame(0, $dto->sort_order);
    }

    #[Test]
    public function to_create_array_returns_all_fields(): void
    {
        $data = [
            'name' => 'Test name',
            'is_active' => true,
            'sort_order' => 1,
        ];

        $dto = FavoritePointDTO::fromArray($data);
        $result = $dto->toCreateArray();

        $this->assertSame($data, $result);
    }

    #[Test]
    public function to_update_array_excludes_null_values(): void
    {
        $dto = FavoritePointDTO::fromArray([
            'name' => 'Test name',
        ]);

        $result = $dto->toUpdateArray();

        $this->assertArrayHasKey('name', $result);
    }
}
