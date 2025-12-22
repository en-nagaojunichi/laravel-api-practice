<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Post API のフィーチャーテスト
 *
 * APIエンドポイントが正しく動作することを検証する
 * - HTTPリクエスト/レスポンス
 * - ステータスコード
 * - レスポンス構造
 */
final class PostApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 一覧取得API（GET /api/posts）
     *
     * - ページネーションされたリストが返されること
     * - 正しいJSON構造であること
     */
    #[Test]
    public function index_returns_paginated_list(): void
    {
        // Arrange: テストデータを3件作成
        Post::factory()->count(3)->create();

        // Act: 一覧取得APIを実行
        $response = $this->getJson('/api/posts');

        // Assert: 200 OK、ページネーション構造であること
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'title', 'slug', 'body', 'status', 'published_at'],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * 詳細取得API（GET /api/posts/{id}）
     *
     * - 指定したリソースが返されること
     * - 正しいJSON構造であること
     */
    #[Test]
    public function show_returns_single_resource(): void
    {
        // Arrange: テストデータを1件作成
        $model = Post::factory()->create();

        // Act: 詳細取得APIを実行
        $response = $this->getJson("/api/posts/{$model->id}");

        // Assert: 200 OK、リソース構造であること
        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'title', 'slug', 'body', 'status', 'published_at'],
            ]);
    }

    /**
     * 新規作成API（POST /api/posts）
     *
     * - リソースが作成されること
     * - 201 Created が返されること
     */
    #[Test]
    public function store_creates_new_resource(): void
    {
        // Arrange: 外部キーの関連モデルを作成し、リクエストデータを準備
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/posts', $data);

        // Assert: 201 Created、リソース構造であること
        $response->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'title', 'slug', 'body', 'status', 'published_at'],
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
        $response = $this->postJson('/api/posts', []);

        // Assert: 422 Unprocessable Entity、必須フィールドのエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id', 'title', 'slug', 'status']);
    }

    /**
     * 更新API（PUT /api/posts/{id}）
     *
     * - リソースが更新されること
     * - 200 OK が返されること
     */
    #[Test]
    public function update_modifies_existing_resource(): void
    {
        // Arrange: 更新対象のモデルを作成し、更新データを準備
        $model = Post::factory()->create();

        $data = [
            'user_id' => $model->user_id,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ];

        // Act: 更新APIを実行
        $response = $this->putJson("/api/posts/{$model->id}", $data);

        // Assert: 200 OK
        $response->assertOk();
    }

    /**
     * 削除API（DELETE /api/posts/{id}）
     *
     * - リソースが削除されること
     * - 204 No Content が返されること
     */
    #[Test]
    public function destroy_deletes_resource(): void
    {
        // Arrange: 削除対象のモデルを作成
        $model = Post::factory()->create();

        // Act: 削除APIを実行
        $response = $this->deleteJson("/api/posts/{$model->id}");

        // Assert: 204 No Content、DBから削除されていること
        $response->assertNoContent();

        $this->assertSoftDeleted('posts', ['id' => $model->id]);
    }

    /**
     * ソフトデリート - 削除済みデータは一覧に表示されない
     */
    #[Test]
    public function index_excludes_soft_deleted_records(): void
    {
        // Arrange: 通常データと削除済みデータを作成
        Post::factory()->count(3)->create();
        Post::factory()->count(2)->create(['deleted_at' => now()]);

        // Act: 一覧取得
        $response = $this->getJson('/api/posts');

        // Assert: 削除済みを除く3件が返されること
        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * ソフトデリート - 削除済みデータは詳細取得できない
     */
    #[Test]
    public function show_returns_404_for_soft_deleted_record(): void
    {
        // Arrange: 削除済みデータを作成
        $model = Post::factory()->create(['deleted_at' => now()]);

        // Act: 詳細取得
        $response = $this->getJson("/api/posts/{$model->id}");

        // Assert: 404 Not Found
        $response->assertNotFound();
    }
}
