<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V2\Room;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 一覧取得・検索用リクエスト
 *
 * 検索条件・並び替え・ページネーションのバリデーション
 * 不要なルールは削除
 */
final class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // キーワード検索
            'keyword' => ['nullable', 'string', 'max:100'],

            // フィールド別絞込
            'region' => ['nullable', 'string', 'max:50'],
            'facility_code' => ['nullable', 'string', 'max:10'],
            'room_number' => ['nullable', 'string', 'max:10'],
            'name' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer'],
            'capacity_min' => ['nullable', 'integer'],
            'capacity_max' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],

            // 並び替え
            'sort_by' => ['nullable', 'in:created_at,region,facility_code,room_number,name,capacity,is_active'],
            'sort_order' => ['nullable', 'in:asc,desc'],

            // ページネーション
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
