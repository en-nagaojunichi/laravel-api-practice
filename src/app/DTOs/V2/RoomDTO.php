<?php

declare(strict_types=1);

namespace App\DTOs\V2;

final class RoomDTO
{
    public function __construct(
        public readonly string $region,
        public readonly string $facility_code,
        public readonly string $room_number,
        public readonly string $name,
        public readonly int $capacity,
        public readonly bool $is_active
    ) {
    }

    /**
     * 配列からDTOを生成
     *
     * @param array<string, mixed> $data validated() または payload() の戻り値
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['region'] ?? '',
            $data['facility_code'] ?? '',
            $data['room_number'] ?? '',
            $data['name'] ?? '',
            $data['capacity'] ?? 0,
            (bool) ($data['is_active'] ?? false)
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
            'region' => $this->region,
            'facility_code' => $this->facility_code,
            'room_number' => $this->room_number,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'is_active' => $this->is_active,
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
