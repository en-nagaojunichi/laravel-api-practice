<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Post;

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
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'published_at_from' => ['nullable', 'date'],
            'published_at_to' => ['nullable', 'date', 'after_or_equal:published_at_from'],

            // 並び替え
            'sort_by' => ['nullable', 'in:id,created_at,user_id,title,slug,status,published_at'],
            'sort_order' => ['nullable', 'in:asc,desc'],

            // ページネーション
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
