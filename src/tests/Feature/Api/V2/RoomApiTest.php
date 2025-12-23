<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V2;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Room API のフィーチャーテスト（複合キー）
 *
 * APIエンドポイントが正しく動作することを検証する
 * - HTTPリクエスト/レスポンス
 * - ステータスコード
 * - レスポンス構造
 */
final class RoomApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 一覧取得API（GET /api/v2/rooms）
     *
     * - ページネーションされたリストが返されること
     * - 正しいJSON構造であること
     */
    #[Test]
    public function index_returns_paginated_list(): void
    {
        // Arrange: テストデータを3件作成
        Room::factory()->count(3)->create();

        // Act: 一覧取得APIを実行
        $response = $this->getJson('/api/v2/rooms');

        // Assert: 200 OK、ページネーション構造であること
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['region', 'facility_code', 'room_number', 'name', 'capacity', 'is_active'],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * 詳細取得API（複合キー）
     *
     * - 指定したリソースが返されること
     * - 正しいJSON構造であること
     */
    #[Test]
    public function show_returns_single_resource(): void
    {
        // Arrange: テストデータを1件作成
        $model = Room::factory()->create();

        // Act: 詳細取得APIを実行（複合キー）
        $response = $this->getJson("/api/v2/rooms/{$model->region}/{$model->facility_code}/{$model->room_number}");

        // Assert: 200 OK、リソース構造であること
        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['region', 'facility_code', 'room_number', 'name', 'capacity', 'is_active'],
            ]);
    }

    /**
     * 新規作成API（POST /api/v2/rooms）
     *
     * - リソースが作成されること
     * - 201 Created が返されること
     */
    #[Test]
    public function store_creates_new_resource(): void
    {
        // Arrange: 外部キーの関連モデルを作成し、リクエストデータを準備

        $data = [
            'region' => 'Test region',
            'facility_code' => 'valfacilit',
            'room_number' => 'valroom_nu',
            'name' => 'Test name',
            'capacity' => 1,
            'is_active' => true,
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/v2/rooms', $data);

        // Assert: 201 Created、リソース構造であること
        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['region', 'facility_code', 'room_number', 'name', 'capacity', 'is_active'],
            ]);
    }

    /**
     * 新規作成API - バリデーションエラー
     *
     * - 必須項目が欠けている場合、422 Unprocessable Entity が返されること
     * - バリデーションエラーが含まれること
     */
    #[Test]
    public function store_returns_validation_error(): void
    {
        // Act: 空のデータで新規作成APIを実行
        $response = $this->postJson('/api/v2/rooms', []);

        // Assert: 422 Unprocessable Entity、必須フィールドのエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['region', 'facility_code', 'room_number', 'name', 'capacity', 'is_active']);
    }

    /**
     * 更新API（複合キー）
     *
     * - リソースが更新されること
     * - 200 OK が返されること
     */
    #[Test]
    public function update_modifies_existing_resource(): void
    {
        // Arrange: 更新対象のモデルを作成し、更新データを準備
        $model = Room::factory()->create();

        $data = [
            'region' => 'Test region',
            'facility_code' => 'valfacilit',
            'room_number' => 'valroom_nu',
            'name' => 'Test name',
            'capacity' => 1,
            'is_active' => true,
        ];

        // Act: 更新APIを実行（複合キー）
        $response = $this->putJson("/api/v2/rooms/{$model->region}/{$model->facility_code}/{$model->room_number}", $data);

        // Assert: 200 OK
        $response->assertOk();
    }

    /**
     * 削除API（複合キー）
     *
     * - リソースが削除されること
     * - 204 No Content が返されること
     */
    #[Test]
    public function destroy_deletes_resource(): void
    {
        // Arrange: 削除対象のモデルを作成
        $model = Room::factory()->create();

        // Act: 削除APIを実行（複合キー）
        $response = $this->deleteJson("/api/v2/rooms/{$model->region}/{$model->facility_code}/{$model->room_number}");

        // Assert: 204 No Content、DBから削除されていること
        $response->assertNoContent();

        $this->assertSoftDeleted('rooms', ['region' => $model->region]);
    }

    /**
     * ソフトデリートされたリソースは一覧に表示されないこと
     */
    #[Test]
    public function index_excludes_soft_deleted_resources(): void
    {
        // Arrange: 通常のデータと削除済みデータを作成
        Room::factory()->count(2)->create();
        Room::factory()->create(['deleted_at' => now()]);

        // Act: 一覧取得
        $response = $this->getJson('/api/v2/rooms');

        // Assert: 削除済みを除く2件のみ返されること
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }
}
