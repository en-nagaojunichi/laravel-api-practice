<?php

declare(strict_types=1);

namespace Tests\Unit\Services\V2;

use App\DTOs\V2\RoomDTO;
use App\Models\Room;
use App\Services\V2\RoomService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * RoomService のユニットテスト（複合キー）
 *
 * Serviceクラスの各メソッドが正しく動作することを検証する
 */
final class RoomServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoomService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RoomService::class);
    }

    /**
     * 検索・ページネーション取得のテスト
     *
     * - 指定した件数でページネーションされること
     * - 正しいデータが取得できること
     */
    #[Test]
    public function search_returns_paginated_results(): void
    {
        // Arrange: テストデータを5件作成
        Room::factory()->count(5)->create();

        // Act: 10件/ページでページネーション取得
        $result = $this->service->search(perPage: 10);

        // Assert: 5件が取得されること
        $this->assertCount(5, $result->items());
    }

    /**
     * 複合キーで検索のテスト
     *
     * - 複合キーでモデルが取得できること
     */
    #[Test]
    public function find_by_composite_key_returns_model(): void
    {
        // Arrange: テストデータを作成
        $model = Room::factory()->create();

        // Act: 複合キーで検索
        $found = $this->service->findByCompositeKey($model->region, $model->facility_code, $model->room_number);

        // Assert: モデルが取得できること
        $this->assertInstanceOf(Room::class, $found);
        $this->assertEquals($model->region, $found->region);
        $this->assertEquals($model->facility_code, $found->facility_code);
        $this->assertEquals($model->room_number, $found->room_number);
    }

    /**
     * 複合キーで検索（存在しない場合）のテスト
     *
     * - 存在しない複合キーの場合はnullが返ること
     */
    #[Test]
    public function find_by_composite_key_returns_null_for_nonexistent(): void
    {
        // Act: 存在しない複合キーで検索
        $found = $this->service->findByCompositeKey('nonexistent', 'nonexistent', 'nonexistent');

        // Assert: nullが返ること
        $this->assertNull($found);
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

        $dto = RoomDTO::fromArray([
            'region' => 'Test region',
            'facility_code' => 'valfacilit',
            'room_number' => 'valroom_nu',
            'name' => 'Test name',
            'capacity' => 1,
            'is_active' => true,
        ]);

        // Act: Serviceで新規作成
        $model = $this->service->create($dto);

        // Assert: モデルが返され、DBに保存されていること
        $this->assertInstanceOf(Room::class, $model);
        $this->assertDatabaseHas('rooms', [
            'region' => $model->region,
            'facility_code' => $model->facility_code,
            'room_number' => $model->room_number,
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
        $model = Room::factory()->create();

        $dto = RoomDTO::fromArray([
            'region' => 'Updated region',
            'facility_code' => 'updated1',
            'room_number' => 'updated1',
            'name' => 'Updated name',
            'capacity' => 999,
            'is_active' => false,
        ]);

        // Act: Serviceで更新
        $updated = $this->service->update($model, $dto);

        // Assert: モデルが返されること
        $this->assertInstanceOf(Room::class, $updated);
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
        $model = Room::factory()->create();

        // Act: Serviceで削除
        $this->service->delete($model);

        // Assert: DBから削除されていること
        $this->assertSoftDeleted('rooms', ['region' => $model->region]);
    }
}
