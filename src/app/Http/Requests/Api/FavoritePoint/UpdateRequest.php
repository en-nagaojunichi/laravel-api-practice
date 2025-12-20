<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\FavoritePoint;

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
            'name' => ['sometimes', 'filled', 'string', 'max:100'],
            'is_active' => ['sometimes', 'filled', 'boolean'],
            'sort_order' => ['sometimes', 'filled', 'integer', 'min:0'],
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