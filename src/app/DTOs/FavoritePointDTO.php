<?php

declare(strict_types=1);

namespace App\DTOs;

final class FavoritePointDTO
{
    public function __construct(
        public readonly string $name,
        public readonly bool $is_active,
        public readonly int $sort_order
    ) {}

    /**
     * 配列からDTOを生成
     *
     * @param array<string, mixed> $data validated() または payload() の戻り値
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? ''
                $data['is_active'] ?? false,
            $data['sort_order'] ?? 0
        );
    }

    /**
     * 新規作成用の配列を返す
     *
     * 【カスタマイズ例】
     * - デフォルト値の設定: 'is_active' => $this->is_active ?? true
     * - 計算値の追加: 'slug' => Str::slug($this->title)
     *
     * @return array<string, mixed>
     */
    public function toCreateArray(): array
    {
        return [
            'name' => $this->name,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];
    }

    /**
     * 更新用の配列を返す（null値を除外）
     *
     * 【注意】
     * - 部分更新に対応するため、nullのフィールドは除外している
     * - 明示的にnullを設定したい場合は、このメソッドをカスタマイズする
     *
     * @return array<string, mixed>
     */
    public function toUpdateArray(): array
    {
        return array_filter(
            $this->toCreateArray(),
            fn(mixed $value): bool => $value !== null
        );
    }
}
