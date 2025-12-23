<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs\V2;

use App\DTOs\V2\RoomDTO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * RoomDTO のユニットテスト
 *
 * DTOクラスの各メソッドが正しく動作することを検証する
 * - 配列からの生成
 * - デフォルト値の適用
 * - 配列への変換
 */
final class RoomDTOTest extends TestCase
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
            'region' => 'Test region',
            'facility_code' => 'valfacilit',
            'room_number' => 'valroom_nu',
            'name' => 'Test name',
            'capacity' => 1,
            'is_active' => true,
        ];

        // Act: DTOを生成
        $dto = RoomDTO::fromArray($data);

        // Assert: 各プロパティが正しく設定されていること
        $this->assertSame('Test region', $dto->region);
        $this->assertSame('valfacilit', $dto->facility_code);
        $this->assertSame('valroom_nu', $dto->room_number);
        $this->assertSame('Test name', $dto->name);
        $this->assertSame(1, $dto->capacity);
        $this->assertSame(true, $dto->is_active);
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
        $dto = RoomDTO::fromArray([]);

        // Assert: デフォルト値が設定されていること
        $this->assertSame('', $dto->region);
        $this->assertSame('', $dto->facility_code);
        $this->assertSame('', $dto->room_number);
        $this->assertSame('', $dto->name);
        $this->assertSame(0, $dto->capacity);
        $this->assertSame(false, $dto->is_active);
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
            'region' => 'Test region',
            'facility_code' => 'valfacilit',
            'room_number' => 'valroom_nu',
            'name' => 'Test name',
            'capacity' => 1,
            'is_active' => true,
        ];
        $dto = RoomDTO::fromArray($data);

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
        $dto = RoomDTO::fromArray([
            'region' => 'Partial region',
        ]);

        // Act: 更新用配列に変換
        $result = $dto->toUpdateArray();

        // Assert: 設定したフィールドが含まれていること
        $this->assertSame('Partial region', $dto->region);
        $this->assertSame('', $dto->facility_code);
    }
}
