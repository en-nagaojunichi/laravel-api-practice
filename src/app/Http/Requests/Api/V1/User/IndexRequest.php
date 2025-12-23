<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

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
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'email_verified_at' => ['nullable', 'date'],
            'email_verified_at_from' => ['nullable', 'date'],
            'email_verified_at_to' => ['nullable', 'date', 'after_or_equal:email_verified_at_from'],

            // 並び替え
            'sort_by' => ['nullable', 'in:id,created_at,name,email,email_verified_at,password,remember_token'],
            'sort_order' => ['nullable', 'in:asc,desc'],

            // ページネーション
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
