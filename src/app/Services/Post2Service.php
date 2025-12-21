<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Post2DTO;
use App\Models\Post2;
use Illuminate\Pagination\LengthAwarePaginator;

final class Post2Service
{
    /**
     * @param int $perPage 1ページあたりの件数
     * @return LengthAwarePaginator<int, Post2>
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Post2::query()
            ->orderBy('id')
            ->paginate($perPage);
    }

    /**
     * 新規作成
     */
    public function create(Post2DTO $dto): Post2
    {
        return Post2::create($dto->toCreateArray());
    }

    /**
     * 更新
     */
    public function update(Post2 $model, Post2DTO $dto): Post2
    {
        $model->fill($dto->toUpdateArray());
        $model->save();

        return $model->refresh();
    }

    /**
     * 削除
     */
    public function delete(Post2 $model): void
    {
        $model->delete();
    }
}