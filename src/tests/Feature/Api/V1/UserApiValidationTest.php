<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * User API の境界値テスト
 *
 * バリデーションの境界値を検証する
 * - 最大文字数
 * - 最小値/最大値
 * - 必須項目
 */
final class UserApiValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 境界値 - name 最大文字数超過
     */
    #[Test]
    public function store_fails_when_name_exceeds_max_length(): void
    {
        // Arrange: 255文字を超えるデータ
        $data = [
            'name' => str_repeat('a', 256),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/v1/users', $data);

        // Assert: 422 かつ name のエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * 境界値 - email 最大文字数超過
     */
    #[Test]
    public function store_fails_when_email_exceeds_max_length(): void
    {
        // Arrange: 255文字を超えるデータ
        $data = [
            'email' => str_repeat('a', 256),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/v1/users', $data);

        // Assert: 422 かつ email のエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 境界値 - password 最大文字数超過
     */
    #[Test]
    public function store_fails_when_password_exceeds_max_length(): void
    {
        // Arrange: 255文字を超えるデータ
        $data = [
            'password' => str_repeat('a', 256),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/v1/users', $data);

        // Assert: 422 かつ password のエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * 境界値 - remember_token 最大文字数超過
     */
    #[Test]
    public function store_fails_when_remember_token_exceeds_max_length(): void
    {
        // Arrange: 100文字を超えるデータ
        $data = [
            'remember_token' => str_repeat('a', 101),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/v1/users', $data);

        // Assert: 422 かつ remember_token のエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['remember_token']);
    }
}
