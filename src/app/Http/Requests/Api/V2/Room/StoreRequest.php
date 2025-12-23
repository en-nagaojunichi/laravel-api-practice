<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V2\Room;

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
            'region' => ['required', 'string', 'max:50', 'unique:rooms,region'],
            'facility_code' => ['required', 'string', 'max:10', 'unique:rooms,facility_code'],
            'room_number' => ['required', 'string', 'max:10', 'unique:rooms,room_number'],
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer'],
            'is_active' => ['required', 'boolean'],
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
