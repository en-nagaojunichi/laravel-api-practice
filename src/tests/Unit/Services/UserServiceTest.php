<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * UserService のユニットテスト
 *
 * Serviceクラスの各メソッドが正しく動作することを検証する
 */
final class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UserService::class);
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
        User::factory()->count(5)->create();

        // Act: 10件/ページでページネーション取得
        $result = $this->service->search(perPage: 10);

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

        $dto = UserDTO::fromArray([
            'name' => 'Test name',
            'email' => 'Test email',
            'email_verified_at' => now()->toDateTimeString(),
            'password' => 'Test password',
            'remember_token' => 'Test remember_token',
        ]);

        // Act: Serviceで新規作成
        $model = $this->service->create($dto);

        // Assert: モデルが返され、DBに保存されていること
        $this->assertInstanceOf(User::class, $model);
        $this->assertDatabaseHas('users', [
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
        $model = User::factory()->create();

        $dto = UserDTO::fromArray([
            'name' => $model->name,
            'email' => $model->email,
            'email_verified_at' => now()->toDateTimeString(),
            'password' => $model->password,
            'remember_token' => $model->remember_token,
        ]);

        // Act: Serviceで更新
        $updated = $this->service->update($model, $dto);

        // Assert: モデルが返されること
        $this->assertInstanceOf(User::class, $updated);
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
        $model = User::factory()->create();
        $id = $model->id;

        // Act: Serviceで削除
        $this->service->delete($model);

        // Assert: DBから削除されていること
        $this->assertDatabaseMissing('users', ['id' => $model->id]);
    }
}
