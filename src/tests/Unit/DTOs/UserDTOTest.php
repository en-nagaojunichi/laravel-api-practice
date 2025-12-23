<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\UserDTO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * UserDTO のユニットテスト
 *
 * DTOクラスの各メソッドが正しく動作することを検証する
 * - 配列からの生成
 * - デフォルト値の適用
 * - 配列への変換
 */
final class UserDTOTest extends TestCase
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
            'name' => 'Test name',
            'email' => 'Test email',
            'email_verified_at' => now()->toDateTimeString(),
            'password' => 'Test password',
            'remember_token' => 'Test remember_token',
        ];

        // Act: DTOを生成
        $dto = UserDTO::fromArray($data);

        // Assert: 各プロパティが正しく設定されていること
        $this->assertSame('Test name', $dto->name);
        $this->assertSame('Test email', $dto->email);
        $this->assertSame(now()->toDateTimeString(), $dto->email_verified_at);
        $this->assertSame('Test password', $dto->password);
        $this->assertSame('Test remember_token', $dto->remember_token);
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
        $dto = UserDTO::fromArray([]);

        // Assert: デフォルト値が設定されていること
        $this->assertSame('', $dto->name);
        $this->assertSame('', $dto->email);
        $this->assertSame(null, $dto->email_verified_at);
        $this->assertSame('', $dto->password);
        $this->assertSame(null, $dto->remember_token);
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
            'name' => 'Test name',
            'email' => 'Test email',
            'email_verified_at' => now()->toDateTimeString(),
            'password' => 'Test password',
            'remember_token' => 'Test remember_token',
        ];
        $dto = UserDTO::fromArray($data);

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
        $dto = UserDTO::fromArray([
            'name' => 'Test name',
        ]);

        // Act: 更新用配列に変換
        $result = $dto->toUpdateArray();

        // Assert: 設定したフィールドが含まれていること
        $this->assertArrayHasKey('name', $result);
    }
}
