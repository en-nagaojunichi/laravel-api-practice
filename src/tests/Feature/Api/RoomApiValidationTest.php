<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Room API の境界値テスト
 *
 * バリデーションの境界値を検証する
 * - 最大文字数
 * - 最小値/最大値
 * - 必須項目
 */
final class RoomApiValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 境界値テスト - region の最大文字数超過
     */
    #[Test]
    public function store_returns_422_when_region_exceeds_max_length(): void
    {
        // Arrange: 最大文字数を超えるデータ
        $data = [
            'region' => str_repeat('a', 50 + 1),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/rooms', $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['region']);
    }

    /**
     * 境界値テスト - facility_code の最大文字数超過
     */
    #[Test]
    public function store_returns_422_when_facility_code_exceeds_max_length(): void
    {
        // Arrange: 最大文字数を超えるデータ
        $data = [
            'facility_code' => str_repeat('a', 10 + 1),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/rooms', $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['facility_code']);
    }

    /**
     * 境界値テスト - room_number の最大文字数超過
     */
    #[Test]
    public function store_returns_422_when_room_number_exceeds_max_length(): void
    {
        // Arrange: 最大文字数を超えるデータ
        $data = [
            'room_number' => str_repeat('a', 10 + 1),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/rooms', $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['room_number']);
    }

    /**
     * 境界値テスト - name の最大文字数超過
     */
    #[Test]
    public function store_returns_422_when_name_exceeds_max_length(): void
    {
        // Arrange: 最大文字数を超えるデータ
        $data = [
            'name' => str_repeat('a', 255 + 1),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/rooms', $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }
}
