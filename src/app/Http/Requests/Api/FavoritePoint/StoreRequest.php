<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\FavoritePoint;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRequest extends FormRequest
{
    /**
     * リクエストの認可
     *
     * 【カスタマイズ例】
     * - ログインユーザーのみ: return auth()->check();
     * - 特定ロール: return $this->user()?->hasRole('admin');
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
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
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
