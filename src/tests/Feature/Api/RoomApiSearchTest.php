<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Room API の検索・フィルタリングテスト（複合キー）
 *
 * 一覧取得APIの検索機能を検証する
 * - フィルタリング
 * - 並び替え
 * - ページネーション
 */
final class RoomApiSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 空のテーブルからの一覧取得
     *
     * - データが0件でも正常にレスポンスが返ること
     */
    #[Test]
    public function index_returns_empty_list_when_no_data(): void
    {
        // Act: データがない状態で一覧取得
        $response = $this->getJson('/api/rooms');

        // Assert: 200 OK、空の配列が返されること
        $response->assertOk()
            ->assertJson([
                'data' => [],
            ]);
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
        Room::factory()->count(15)->create();

        // Act: 5件/ページで一覧取得
        $response = $this->getJson('/api/rooms?per_page=5');

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
        $old = Room::factory()->create(['created_at' => now()->subDay()]);
        $new = Room::factory()->create(['created_at' => now()]);

        // Act: created_at 昇順で一覧取得
        $response = $this->getJson('/api/rooms?sort_by=created_at&sort_order=asc');

        // Assert: 古い順に並んでいること
        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals($old->region, $data[0]['region']);
        $this->assertEquals($new->region, $data[1]['region']);
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
        $old = Room::factory()->create(['created_at' => now()->subDay()]);
        $new = Room::factory()->create(['created_at' => now()]);

        // Act: created_at 降順で一覧取得
        $response = $this->getJson('/api/rooms?sort_by=created_at&sort_order=desc');

        // Assert: 新しい順に並んでいること
        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals($new->region, $data[0]['region']);
        $this->assertEquals($old->region, $data[1]['region']);
    }

    /**
     * フィルタ - capacity（数値完全一致）
     */
    #[Test]
    public function index_filters_by_capacity_exact(): void
    {
        // Arrange: 異なる値でデータを作成
        Room::factory()->create(['capacity' => 100]);
        Room::factory()->create(['capacity' => 200]);

        // Act: capacityでフィルタ
        $response = $this->getJson('/api/rooms?capacity=100');

        // Assert: 1件のみ返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * フィルタ - capacity_min（数値範囲）
     */
    #[Test]
    public function index_filters_by_capacity_min(): void
    {
        // Arrange: 異なる値でデータを作成
        Room::factory()->create(['capacity' => 50]);
        Room::factory()->create(['capacity' => 150]);

        // Act: capacity_minでフィルタ
        $response = $this->getJson('/api/rooms?capacity_min=100');

        // Assert: 1件のみ返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * フィルタ - capacity_max（数値範囲）
     */
    #[Test]
    public function index_filters_by_capacity_max(): void
    {
        // Arrange: 異なる値でデータを作成
        Room::factory()->create(['capacity' => 50]);
        Room::factory()->create(['capacity' => 150]);

        // Act: capacity_maxでフィルタ
        $response = $this->getJson('/api/rooms?capacity_max=100');

        // Assert: 1件のみ返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * フィルタ - capacity 範囲指定（min と max の両方）
     */
    #[Test]
    public function index_filters_by_capacity_range(): void
    {
        // Arrange: 異なる値でデータを作成
        Room::factory()->create(['capacity' => 50]);
        Room::factory()->create(['capacity' => 100]);
        Room::factory()->create(['capacity' => 200]);

        // Act: 範囲指定でフィルタ
        $response = $this->getJson('/api/rooms?capacity_min=75&capacity_max=150');

        // Assert: 1件のみ返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * フィルタ - is_active（boolean）
     */
    #[Test]
    public function index_filters_by_is_active(): void
    {
        // Arrange: 異なる値でデータを作成
        Room::factory()->create(['is_active' => true]);
        Room::factory()->create(['is_active' => false]);

        // Act: is_active=trueでフィルタ
        $response = $this->getJson('/api/rooms?is_active=1');

        // Assert: 1件のみ返されること
        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
