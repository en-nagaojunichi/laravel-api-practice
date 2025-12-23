<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * User API のエラー系テスト
 *
 * APIエンドポイントのエラーハンドリングを検証する
 * - 404 Not Found
 * - 422 Validation Error
 */
final class UserApiErrorTest extends TestCase
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
        // Act: 存在しないIDで詳細取得APIを実行
        $response = $this->getJson('/api/users/99999');

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
        // Act: 存在しないIDで更新APIを実行
        $response = $this->putJson('/api/users/99999', []);

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
        // Act: 存在しないIDで削除APIを実行
        $response = $this->deleteJson('/api/users/99999');

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
        $response = $this->postJson('/api/users', []);

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
            'email_verified_at' => 'invalid-date',
        ];

        // Act: 不正なデータで新規作成APIを実行
        $response = $this->postJson('/api/users', $data);

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
        $model = User::factory()->create();

        $data = [
            'email_verified_at' => 'invalid-date',
        ];

        // Act: 不正なデータで更新APIを実行
        $response = $this->putJson("/api/users/{$model->id}", $data);

        // Assert: 422 Unprocessable Entity
        $response->assertUnprocessable();
    }

    /**
     * ユニーク制約 - email 重複エラー
     */
    #[Test]
    public function store_fails_with_duplicate_email(): void
    {
        // Arrange: 既存データを作成
        $existing = User::factory()->create();

        // Act: 同じ email で新規作成
        $data = User::factory()->make(['email' => $existing->email])->toArray();
        $response = $this->postJson('/api/users', $data);

        // Assert: 422 かつ email のエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }
}
