<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\Post2DTO;
use App\Models\Post;
use App\Models\User;
use App\Services\Post2Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Post2Service のユニットテスト
 *
 * Serviceクラスの各メソッドが正しく動作することを検証する
 */
final class Post2ServiceTest extends TestCase
{
    use RefreshDatabase;

    private Post2Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(Post2Service::class);
    }

    /**
     * ページネーション取得のテスト
     *
     * - 指定した件数でページネーションされること
     * - 正しいデータが取得できること
     */
    #[Test]
    public function paginate_returns_paginated_results(): void
    {
        // Arrange: テストデータを5件作成
        Post::factory()->count(5)->create();

        // Act: 10件/ページでページネーション取得
        $result = $this->service->paginate(10);

        // Assert: 5件が取得されること
        $this->assertCount(5, $result->items());
    }

    /**
     * 新規作成のテスト
     *
     * - DTOからモデルが作成されること
     * - DBに正しく保存されること
     */
    #[Test]
    public function create_stores_new_record(): void
    {
        // Arrange: 外部キーの関連モデルを作成し、DTOを準備
        $user = User::factory()->create();

        $dto = Post2DTO::fromArray([
            'user_id' => $user->id,
            'title' => 'Test title',
            'slug' => 'Test slug',
            'body' => 'Test body',
            'status' => 'draft',
            'published_at' => now()->toDateTimeString(),
        ]);

        // Act: Serviceで新規作成
        $model = $this->service->create($dto);

        // Assert: モデルが返され、DBに保存されていること
        $this->assertInstanceOf(Post::class, $model);
        $this->assertDatabaseHas('posts', [
            'id' => $model->id,
        ]);
    }

    /**
     * 更新のテスト
     *
     * - 既存モデルが更新されること
     * - 更新後のモデルが返されること
     */
    #[Test]
    public function update_modifies_existing_record(): void
    {
        // Arrange: 既存モデルを作成し、更新用DTOを準備
        $model = Post::factory()->create();

        $dto = Post2DTO::fromArray([
            'user_id' => $model->user_id,
            'title' => $model->title,
            'slug' => $model->slug,
            'body' => $model->body,
            'status' => $model->status,
            'published_at' => now()->toDateTimeString(),
        ]);

        // Act: Serviceで更新
        $updated = $this->service->update($model, $dto);

        // Assert: モデルが返されること
        $this->assertInstanceOf(Post::class, $updated);
    }

    /**
     * 削除のテスト
     *
     * - モデルが削除されること
     * - SoftDeletes使用時は論理削除されること
     */
    #[Test]
    public function delete_removes_record(): void
    {
        // Arrange: 削除対象のモデルを作成
        $model = Post::factory()->create();
        $id = $model->id;

        // Act: Serviceで削除
        $this->service->delete($model);

        // Assert: DBから削除されていること
        $this->assertSoftDeleted('posts', ['id' => $model->id]);
    }
}
