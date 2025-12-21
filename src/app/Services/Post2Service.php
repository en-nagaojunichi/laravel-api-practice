<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Post2DTO;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

final class Post2Service
{
    /**
     * @param int $perPage 1ページあたりの件数
     * @return LengthAwarePaginator<int, Post>
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Post::query()
            ->orderBy('id')
            ->paginate($perPage);
    }

    /**
     * 新規作成
     */
    public function create(Post2DTO $dto): Post
    {
        return Post::create($dto->toCreateArray());
    }

    /**
     * 更新
     */
    public function update(Post $model, Post2DTO $dto): Post
    {
        $model->fill($dto->toUpdateArray());
        $model->save();

        return $model->refresh();
    }

    /**
     * 削除
     */
    public function delete(Post $model): void
    {
        $model->delete();
    }
}
