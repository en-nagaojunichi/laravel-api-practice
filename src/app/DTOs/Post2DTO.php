<?php

declare(strict_types=1);

namespace App\DTOs;

final class Post2DTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly string $title,
        public readonly string $slug,
        public readonly ?string $body,
        public readonly string $status,
        public readonly ?string $published_at
    ) {}

    /**
     * 配列からDTOを生成
     *
     * @param array<string, mixed> $data validated() または payload() の戻り値
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'] ?? 0,
            $data['title'] ?? '',
            $data['slug'] ?? '',
            $data['body'] ?? null,
            $data['status'] ?? '',
            $data['published_at'] ?? null
        );
    }

    /**
     * 新規作成用の配列を返す
     *
     * @return array<string, mixed>
     */
    public function toCreateArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'status' => $this->status,
            'published_at' => $this->published_at,
        ];
    }

    /**
     * 更新用の配列を返す（null値を除外）
     *
     * @return array<string, mixed>
     */
    public function toUpdateArray(): array
    {
        return array_filter(
            $this->toCreateArray(),
            fn (mixed $value): bool => $value !== null
        );
    }
}