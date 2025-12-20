<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\FavoritePointDTO;
use App\Models\FavoritePoint;
use Illuminate\Pagination\LengthAwarePaginator;

final class FavoritePointService
{
    /**
     * @param int $perPage 1ページあたりの件数
     * @return LengthAwarePaginator<int, FavoritePoint>
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return FavoritePoint::query()
            ->orderBy('id')
            ->paginate($perPage);
    }

    /**
     * 新規作成
     */
    public function create(FavoritePointDTO $dto): FavoritePoint
    {
        return FavoritePoint::create($dto->toCreateArray());
    }

    /**
     * 更新
     */
    public function update(FavoritePoint $model, FavoritePointDTO $dto): FavoritePoint
    {
        $model->fill($dto->toUpdateArray());
        $model->save();

        return $model->refresh();
    }

    /**
     * 削除
     */
    public function delete(FavoritePoint $model): void
    {
        $model->delete();
    }
}