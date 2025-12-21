<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Post2;

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
            'user_id' => ['sometimes', 'filled', 'integer', 'min:0', 'exists:users,id'],
            'title' => ['sometimes', 'filled', 'string', 'max:255'],
            'slug' => ['sometimes', 'filled', 'string', 'max:255', 'unique:posts,slug,' . $this->route('post2')->id],
            'body' => ['sometimes', 'nullable', 'string', 'max:65535'],
            'status' => ['sometimes', 'filled', 'in:draft,published,archived'],
            'published_at' => ['sometimes', 'nullable', 'date'],
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