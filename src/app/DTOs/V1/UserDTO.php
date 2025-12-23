<?php

declare(strict_types=1);

namespace App\DTOs\V1;

final class UserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $email_verified_at,
        public readonly string $password,
        public readonly ?string $remember_token
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
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['email_verified_at'] ?? null,
            $data['password'] ?? '',
            $data['remember_token'] ?? null
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
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password' => $this->password,
            'remember_token' => $this->remember_token,
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
