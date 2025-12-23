<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * User API のフィーチャーテスト
 *
 * APIエンドポイントが正しく動作することを検証する
 * - HTTPリクエスト/レスポンス
 * - ステータスコード
 * - レスポンス構造
 */
final class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 一覧取得API（GET /api/users）
     *
     * - ページネーションされたリストが返されること
     * - 正しいJSON構造であること
     */
    #[Test]
    public function index_returns_paginated_list(): void
    {
        // Arrange: テストデータを3件作成
        User::factory()->count(3)->create();

        // Act: 一覧取得APIを実行
        $response = $this->getJson('/api/users');

        // Assert: 200 OK、ページネーション構造であること
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'email_verified_at', 'password', 'remember_token'],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * 詳細取得API（GET /api/users/{id}）
     *
     * - 指定したリソースが返されること
     * - 正しいJSON構造であること
     */
    #[Test]
    public function show_returns_single_resource(): void
    {
        // Arrange: テストデータを1件作成
        $model = User::factory()->create();

        // Act: 詳細取得APIを実行
        $response = $this->getJson("/api/users/{$model->id}");

        // Assert: 200 OK、リソース構造であること
        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'email_verified_at', 'password', 'remember_token'],
            ]);
    }

    /**
     * 新規作成API（POST /api/users）
     *
     * - リソースが作成されること
     * - 201 Created が返されること
     */
    #[Test]
    public function store_creates_new_resource(): void
    {
        // Arrange: 外部キーの関連モデルを作成し、リクエストデータを準備

        $data = [
            'name' => 'Test name',
            'email' => 'Test email',
            'email_verified_at' => now()->toDateTimeString(),
            'password' => 'Test password',
            'remember_token' => 'Test remember_token',
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/users', $data);

        // Assert: 201 Created、リソース構造であること
        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'email_verified_at', 'password', 'remember_token'],
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
        $response = $this->postJson('/api/users', []);

        // Assert: 422 Unprocessable Entity、必須フィールドのエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * 更新API（PUT /api/users/{id}）
     *
     * - リソースが更新されること
     * - 200 OK が返されること
     */
    #[Test]
    public function update_modifies_existing_resource(): void
    {
        // Arrange: 更新対象のモデルを作成し、更新データを準備
        $model = User::factory()->create();

        $data = [
            'name' => 'Test name',
            'email' => 'Test email',
            'email_verified_at' => now()->toDateTimeString(),
            'password' => 'Test password',
            'remember_token' => 'Test remember_token',
        ];

        // Act: 更新APIを実行
        $response = $this->putJson("/api/users/{$model->id}", $data);

        // Assert: 200 OK
        $response->assertOk();
    }

    /**
     * 削除API（DELETE /api/users/{id}）
     *
     * - リソースが削除されること
     * - 204 No Content が返されること
     */
    #[Test]
    public function destroy_deletes_resource(): void
    {
        // Arrange: 削除対象のモデルを作成
        $model = User::factory()->create();

        // Act: 削除APIを実行
        $response = $this->deleteJson("/api/users/{$model->id}");

        // Assert: 204 No Content、DBから削除されていること
        $response->assertNoContent();

        $this->assertDatabaseMissing('users', ['id' => $model->id]);
    }
}
