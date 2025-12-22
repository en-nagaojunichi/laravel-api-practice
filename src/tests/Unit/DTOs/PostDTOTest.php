<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\PostDTO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * PostDTO のユニットテスト
 *
 * DTOクラスの各メソッドが正しく動作することを検証する
 * - 配列からの生成
 * - デフォルト値の適用
 * - 配列への変換
 */
final class PostDTOTest extends TestCase
{
    /**
     * fromArray: 値が正しく設定されること
     *
     * - 配列からDTOを生成できること
     * - 各プロパティに正しい値が設定されること
     */
    #[Test]
    public function from_array_creates_dto_with_values(): void
    {
        // Arrange: 入力データを準備
        $data = [
            'user_id' => 1,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ];

        // Act: DTOを生成
        $dto = PostDTO::fromArray($data);

        // Assert: 各プロパティが正しく設定されていること
        $this->assertSame(1, $dto->user_id);
        $this->assertSame('Test title', $dto->title);
        $this->assertSame('Test slug', $dto->slug);
        $this->assertSame('Test body', $dto->body);
        $this->assertSame('draft', $dto->status);
        $this->assertSame(now()->toDateTimeString(), $dto->published_at);
    }

    /**
     * fromArray: 未指定の値にはデフォルト値が適用されること
     *
     * - 空配列でもDTOが生成できること
     * - 各プロパティにデフォルト値が設定されること
     */
    #[Test]
    public function from_array_uses_defaults_for_missing_values(): void
    {
        // Act: 空配列からDTOを生成
        $dto = PostDTO::fromArray([]);

        // Assert: デフォルト値が設定されていること
        $this->assertSame(0, $dto->user_id);
        $this->assertSame('', $dto->title);
        $this->assertSame('', $dto->slug);
        $this->assertSame(null, $dto->body);
        $this->assertSame('', $dto->status);
        $this->assertSame(null, $dto->published_at);
    }

    /**
     * toCreateArray: 新規作成用の配列が返されること
     *
     * - 全フィールドが含まれること
     * - 値が正しく変換されること
     */
    #[Test]
    public function to_create_array_returns_all_fields(): void
    {
        // Arrange: DTOを作成
        $data = [
            'user_id' => 1,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ];
        $dto = PostDTO::fromArray($data);

        // Act: 配列に変換
        $result = $dto->toCreateArray();

        // Assert: 入力データと同じ内容であること
        $this->assertSame($data, $result);
    }

    /**
     * toUpdateArray: null値が除外されること
     *
     * - 部分更新に対応するためnull値は除外される
     * - 設定された値のみが含まれること
     */
    #[Test]
    public function to_update_array_excludes_null_values(): void
    {
        // Arrange: 一部のフィールドのみ設定したDTOを作成
        $dto = PostDTO::fromArray([
            'user_id' => 1,
        ]);

        // Act: 更新用配列に変換
        $result = $dto->toUpdateArray();

        // Assert: 設定したフィールドが含まれていること
        $this->assertArrayHasKey('user_id', $result);
    }
}
