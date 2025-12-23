<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * User API の検索・フィルタリングテスト
 *
 * 一覧取得APIの検索機能を検証する
 * - フィルタリング
 * - 並び替え
 * - ページネーション
 */
final class UserApiSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 空テーブル - データ0件でも正常に動作する
     */
    #[Test]
    public function index_returns_empty_list_when_no_data(): void
    {
        // Act: データがない状態で一覧取得
        $response = $this->getJson('/api/users');

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
        User::factory()->count(15)->create();

        // Act: 5件/ページで一覧取得
        $response = $this->getJson('/api/users?per_page=5');

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
        $old = User::factory()->create(['created_at' => now()->subDay()]);
        $new = User::factory()->create(['created_at' => now()]);

        // Act: created_at 昇順で一覧取得
        $response = $this->getJson('/api/users?sort_by=created_at&sort_order=asc');

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
        $old = User::factory()->create(['created_at' => now()->subDay()]);
        $new = User::factory()->create(['created_at' => now()]);

        // Act: created_at 降順で一覧取得
        $response = $this->getJson('/api/users?sort_by=created_at&sort_order=desc');

        // Assert: 新しい順に並んでいること
        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals($new->id, $data[0]['id']);
        $this->assertEquals($old->id, $data[1]['id']);
    }

    /**
     * フィルタリング - name（部分一致）
     */
    #[Test]
    public function index_filters_by_name(): void
    {
        // Arrange: テストデータを作成
        $target = User::factory()->create(['name' => 'テスト検索値']);
        User::factory()->create(['name' => '別の値']);

        // Act: nameでフィルタリング
        $response = $this->getJson('/api/users?name=検索');

        // Assert: フィルタリングされた結果が返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($target->id, $response->json('data.0.id'));
    }

    /**
     * フィルタリング - email（完全一致）
     */
    #[Test]
    public function index_filters_by_email(): void
    {
        // Arrange: テストデータを作成
        $target = User::factory()->create(['email' => 'test@example.com']);
        User::factory()->create(['email' => 'other-value']);

        // Act: emailでフィルタリング
        $response = $this->getJson('/api/users?email=test@example.com');

        // Assert: フィルタリングされた結果が返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($target->id, $response->json('data.0.id'));
    }

    /**
     * フィルタリング - email_verified_at（完全一致）
     */
    #[Test]
    public function index_filters_by_email_verified_at(): void
    {
        // Arrange: 異なる日付でデータを作成
        User::factory()->create(['email_verified_at' => '2025-01-15']);
        User::factory()->create(['email_verified_at' => '2025-02-20']);

        // Act: email_verified_atでフィルタリング
        $response = $this->getJson('/api/users?email_verified_at=2025-01-15');

        // Assert: 1件が返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * フィルタリング - email_verified_at（範囲：from）
     */
    #[Test]
    public function index_filters_by_email_verified_at_from(): void
    {
        // Arrange: 異なる日付でデータを作成
        User::factory()->create(['email_verified_at' => '2025-01-01']);
        User::factory()->create(['email_verified_at' => '2025-01-15']);
        User::factory()->create(['email_verified_at' => '2025-01-31']);

        // Act: email_verified_at_from でフィルタリング
        $response = $this->getJson('/api/users?email_verified_at_from=2025-01-15');

        // Assert: 2件が返されること（1/15以降）
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /**
     * フィルタリング - email_verified_at（範囲：to）
     */
    #[Test]
    public function index_filters_by_email_verified_at_to(): void
    {
        // Arrange: 異なる日付でデータを作成
        User::factory()->create(['email_verified_at' => '2025-01-01']);
        User::factory()->create(['email_verified_at' => '2025-01-15']);
        User::factory()->create(['email_verified_at' => '2025-01-31']);

        // Act: email_verified_at_to でフィルタリング
        $response = $this->getJson('/api/users?email_verified_at_to=2025-01-15');

        // Assert: 2件が返されること（1/15以前）
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    /**
     * フィルタリング - email_verified_at（範囲：from〜to）
     */
    #[Test]
    public function index_filters_by_email_verified_at_range(): void
    {
        // Arrange: 異なる日付でデータを作成
        User::factory()->create(['email_verified_at' => '2025-01-01']);
        User::factory()->create(['email_verified_at' => '2025-01-15']);
        User::factory()->create(['email_verified_at' => '2025-01-31']);

        // Act: email_verified_at_from と email_verified_at_to でフィルタリング
        $response = $this->getJson('/api/users?email_verified_at_from=2025-01-10&email_verified_at_to=2025-01-20');

        // Assert: 1件が返されること（1/10〜1/20）
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
