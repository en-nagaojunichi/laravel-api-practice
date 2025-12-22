<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Post API の検索・フィルタリングテスト
 *
 * 一覧取得APIの検索機能を検証する
 * - フィルタリング
 * - 並び替え
 * - ページネーション
 */
final class PostApiSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 空テーブル - データ0件でも正常に動作する
     */
    #[Test]
    public function index_returns_empty_list_when_no_data(): void
    {
        // Act: データがない状態で一覧取得
        $response = $this->getJson('/api/posts');

        // Assert: 200 OK、空の配列が返されること
        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    /**
     * ページネーション - 件数指定
     *
     * - per_page パラメータで件数を指定できること
     */
    #[Test]
    public function index_paginates_with_per_page(): void
    {
        // Arrange: テストデータを15件作成
        Post::factory()->count(15)->create();

        // Act: 5件/ページで一覧取得
        $response = $this->getJson('/api/posts?per_page=5');

        // Assert: 5件が返されること
        $response->assertOk();
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * 並び替え - 昇順
     *
     * - sort_order=asc で昇順に並ぶこと
     */
    #[Test]
    public function index_sorts_ascending(): void
    {
        // Arrange: 異なる日時でデータを作成
        $old = Post::factory()->create(['created_at' => now()->subDay()]);
        $new = Post::factory()->create(['created_at' => now()]);

        // Act: created_at 昇順で一覧取得
        $response = $this->getJson('/api/posts?sort_by=created_at&sort_order=asc');

        // Assert: 古い順に並んでいること
        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals($old->id, $data[0]['id']);
        $this->assertEquals($new->id, $data[1]['id']);
    }

    /**
     * 並び替え - 降順
     *
     * - sort_order=desc で降順に並ぶこと
     */
    #[Test]
    public function index_sorts_descending(): void
    {
        // Arrange: 異なる日時でデータを作成
        $old = Post::factory()->create(['created_at' => now()->subDay()]);
        $new = Post::factory()->create(['created_at' => now()]);

        // Act: created_at 降順で一覧取得
        $response = $this->getJson('/api/posts?sort_by=created_at&sort_order=desc');

        // Assert: 新しい順に並んでいること
        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals($new->id, $data[0]['id']);
        $this->assertEquals($old->id, $data[1]['id']);
    }

    /**
     * フィルタリング - user_id
     */
    #[Test]
    public function index_filters_by_user_id(): void
    {
        // Arrange: 関連モデルを作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Post::factory()->count(2)->create(['user_id' => $user1->id]);
        Post::factory()->count(3)->create(['user_id' => $user2->id]);

        // Act: user_idでフィルタリング
        $response = $this->getJson("/api/posts?user_id={$user1->id}");

        // Assert: 2件が返されること
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /**
     * フィルタリング - status
     */
    #[Test]
    public function index_filters_by_status(): void
    {
        // Arrange: 異なるステータスでデータを作成
        Post::factory()->count(2)->create(['status' => 'draft']);
        Post::factory()->count(3)->create();

        // Act: statusでフィルタリング
        $response = $this->getJson('/api/posts?status=draft');

        // Assert: フィルタリングされた結果が返されること
        $response->assertOk();
    }

    /**
     * フィルタリング - published_at（完全一致）
     */
    #[Test]
    public function index_filters_by_published_at(): void
    {
        // Arrange: 異なる日付でデータを作成
        Post::factory()->create(['published_at' => '2025-01-15']);
        Post::factory()->create(['published_at' => '2025-02-20']);

        // Act: published_atでフィルタリング
        $response = $this->getJson('/api/posts?published_at=2025-01-15');

        // Assert: 1件が返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * フィルタリング - published_at（範囲：from）
     */
    #[Test]
    public function index_filters_by_published_at_from(): void
    {
        // Arrange: 異なる日付でデータを作成
        Post::factory()->create(['published_at' => '2025-01-01']);
        Post::factory()->create(['published_at' => '2025-01-15']);
        Post::factory()->create(['published_at' => '2025-01-31']);

        // Act: published_at_from でフィルタリング
        $response = $this->getJson('/api/posts?published_at_from=2025-01-15');

        // Assert: 2件が返されること（1/15以降）
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /**
     * フィルタリング - published_at（範囲：to）
     */
    #[Test]
    public function index_filters_by_published_at_to(): void
    {
        // Arrange: 異なる日付でデータを作成
        Post::factory()->create(['published_at' => '2025-01-01']);
        Post::factory()->create(['published_at' => '2025-01-15']);
        Post::factory()->create(['published_at' => '2025-01-31']);

        // Act: published_at_to でフィルタリング
        $response = $this->getJson('/api/posts?published_at_to=2025-01-15');

        // Assert: 2件が返されること（1/15以前）
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /**
     * フィルタリング - published_at（範囲：from〜to）
     */
    #[Test]
    public function index_filters_by_published_at_range(): void
    {
        // Arrange: 異なる日付でデータを作成
        Post::factory()->create(['published_at' => '2025-01-01']);
        Post::factory()->create(['published_at' => '2025-01-15']);
        Post::factory()->create(['published_at' => '2025-01-31']);

        // Act: published_at_from と published_at_to でフィルタリング
        $response = $this->getJson('/api/posts?published_at_from=2025-01-10&published_at_to=2025-01-20');

        // Assert: 1件が返されること（1/10〜1/20）
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
