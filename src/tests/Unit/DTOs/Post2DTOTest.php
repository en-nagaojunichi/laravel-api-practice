<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\Post2DTO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class Post2DTOTest extends TestCase
{
    #[Test]
    public function from_array_creates_dto_with_values(): void
    {
        $data = [
            'user_id' => 1,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ];

        $dto = Post2DTO::fromArray($data);

        $this->assertSame(1, $dto->user_id);
        $this->assertSame('Test title', $dto->title);
        $this->assertSame('Test slug', $dto->slug);
        $this->assertSame('Test body', $dto->body);
        $this->assertSame('draft', $dto->status);
        $this->assertSame(now()->toDateTimeString(), $dto->published_at);
    }

    #[Test]
    public function from_array_uses_defaults_for_missing_values(): void
    {
        $dto = Post2DTO::fromArray([]);

        $this->assertSame(0, $dto->user_id);
        $this->assertSame('', $dto->title);
        $this->assertSame('', $dto->slug);
        $this->assertSame(null, $dto->body);
        $this->assertSame('', $dto->status);
        $this->assertSame(null, $dto->published_at);
    }

    #[Test]
    public function to_create_array_returns_all_fields(): void
    {
        $data = [
            'user_id' => 1,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ];

        $dto = Post2DTO::fromArray($data);
        $result = $dto->toCreateArray();

        $this->assertSame($data, $result);
    }

    #[Test]
    public function to_update_array_excludes_null_values(): void
    {
        $dto = Post2DTO::fromArray([
            'user_id' => 1,
        ]);

        $result = $dto->toUpdateArray();

        $this->assertArrayHasKey('user_id', $result);
    }
}