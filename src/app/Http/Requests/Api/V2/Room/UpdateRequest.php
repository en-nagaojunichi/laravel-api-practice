<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V2\Room;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRequest extends FormRequest
{
    /**
     * リクエストの認可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            // TODO: unique制約の除外は複合キーに合わせて手動設定が必要
            'region' => ['sometimes', 'filled', 'string', 'max:50'],
            // TODO: unique制約の除外は複合キーに合わせて手動設定が必要
            'facility_code' => ['sometimes', 'filled', 'string', 'max:10'],
            // TODO: unique制約の除外は複合キーに合わせて手動設定が必要
            'room_number' => ['sometimes', 'filled', 'string', 'max:10'],
            'name' => ['sometimes', 'filled', 'string', 'max:100'],
            'capacity' => ['sometimes', 'filled', 'integer'],
            'is_active' => ['sometimes', 'filled', 'boolean'],
        ];
    }

    /**
     * Controller/DTOに渡す入力データを返す
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->validated();
    }
}
