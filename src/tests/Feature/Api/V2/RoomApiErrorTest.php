<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V2;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Room API のエラー系テスト（複合キー）
 *
 * APIエンドポイントのエラーハンドリングを検証する
 * - 404 Not Found
 * - 422 Validation Error
 */
final class RoomApiErrorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 存在しないリソースの詳細取得（GET）
     *
     * - 404 Not Found が返されること
     */
    #[Test]
    public function show_returns_404_for_nonexistent_resource(): void
    {
        // Act: 存在しない複合キーで詳細取得APIを実行
        $response = $this->getJson('/api/v2/rooms/nonexistent/nonexistent/nonexistent');

        // Assert: 404 Not Found
        $response->assertNotFound();
    }

    /**
     * 存在しないリソースの更新（PUT）
     *
     * - 404 Not Found が返されること
     */
    #[Test]
    public function update_returns_404_for_nonexistent_resource(): void
    {
        // Act: 存在しない複合キーで更新APIを実行
        $response = $this->putJson('/api/v2/rooms/nonexistent/nonexistent/nonexistent', []);

        // Assert: 404 Not Found
        $response->assertNotFound();
    }

    /**
     * 存在しないリソースの削除（DELETE）
     *
     * - 404 Not Found が返されること
     */
    #[Test]
    public function destroy_returns_404_for_nonexistent_resource(): void
    {
        // Act: 存在しない複合キーで削除APIを実行
        $response = $this->deleteJson('/api/v2/rooms/nonexistent/nonexistent/nonexistent');

        // Assert: 404 Not Found
        $response->assertNotFound();
    }

    /**
     * 新規作成 - 空のリクエスト
     *
     * - 422 Unprocessable Entity が返されること
     */
    #[Test]
    public function store_returns_422_with_empty_request(): void
    {
        // Act: 空のデータで新規作成APIを実行
        $response = $this->postJson('/api/v2/rooms', []);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable();
    }

    /**
     * 新規作成 - 不正な型のデータ
     *
     * - 422 Unprocessable Entity が返されること
     */
    #[Test]
    public function store_returns_422_with_invalid_types(): void
    {
        // Arrange: 不正な型のデータを準備
        $data = [
            'capacity' => 'not-an-integer',
            'is_active' => 'not-an-integer',
        ];

        // Act: 不正なデータで新規作成APIを実行
        $response = $this->postJson('/api/v2/rooms', $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable();
    }

    /**
     * 更新 - 不正な型のデータ
     *
     * - 422 Unprocessable Entity が返されること
     */
    #[Test]
    public function update_returns_422_with_invalid_types(): void
    {
        // Arrange: 更新対象のモデルを作成
        $model = Room::factory()->create();

        $data = [
            'capacity' => 'not-an-integer',
            'is_active' => 'not-an-integer',
        ];

        // Act: 不正なデータで更新APIを実行（複合キー）
        $response = $this->putJson("/api/v2/rooms/{$model->region}/{$model->facility_code}/{$model->room_number}", $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable();
    }

    /**
     * ユニーク制約違反（region）
     *
     * - 重複する値で作成しようとした場合、422 が返されること
     */
    #[Test]
    public function store_returns_422_for_duplicate_region(): void
    {
        // Arrange: 既存のモデルを作成
        $existing = Room::factory()->create();

        $data = [
            'region' => $existing->region,
        ];

        // Act: 同じregionで新規作成を試みる
        $response = $this->postJson('/api/v2/rooms', $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['region']);
    }

    /**
     * ユニーク制約違反（facility_code）
     *
     * - 重複する値で作成しようとした場合、422 が返されること
     */
    #[Test]
    public function store_returns_422_for_duplicate_facility_code(): void
    {
        // Arrange: 既存のモデルを作成
        $existing = Room::factory()->create();

        $data = [
            'facility_code' => $existing->facility_code,
        ];

        // Act: 同じfacility_codeで新規作成を試みる
        $response = $this->postJson('/api/v2/rooms', $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['facility_code']);
    }

    /**
     * ユニーク制約違反（room_number）
     *
     * - 重複する値で作成しようとした場合、422 が返されること
     */
    #[Test]
    public function store_returns_422_for_duplicate_room_number(): void
    {
        // Arrange: 既存のモデルを作成
        $existing = Room::factory()->create();

        $data = [
            'room_number' => $existing->room_number,
        ];

        // Act: 同じroom_numberで新規作成を試みる
        $response = $this->postJson('/api/v2/rooms', $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['room_number']);
    }
}
