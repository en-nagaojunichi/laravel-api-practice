<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Post API の境界値テスト
 *
 * バリデーションの境界値を検証する
 * - 最大文字数
 * - 最小値/最大値
 * - 必須項目
 */
final class PostApiValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 境界値 - title 最大文字数超過
     */
    #[Test]
    public function store_fails_when_title_exceeds_max_length(): void
    {
        // Arrange: 255文字を超えるデータ
        $data = [
            'title' => str_repeat('a', 256),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/posts', $data);

        // Assert: 422 かつ title のエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * 境界値 - slug 最大文字数超過
     */
    #[Test]
    public function store_fails_when_slug_exceeds_max_length(): void
    {
        // Arrange: 255文字を超えるデータ
        $data = [
            'slug' => str_repeat('a', 256),
        ];

        // Act: 新規作成APIを実行
        $response = $this->postJson('/api/posts', $data);

        // Assert: 422 かつ slug のエラーがあること
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }
}
