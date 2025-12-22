<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\RoomDTO;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class RoomService
{
    /**
     * 検索・絞込・並び替え付きページネーション
     *
     * @param array<string, mixed> $filters 絞込条件
     * @param string $sortBy 並び替えカラム
     * @param string $sortOrder 昇順(asc)/降順(desc)
     * @param int $perPage 1ページあたりの件数
     * @return LengthAwarePaginator<int, Room>
     */
    public function search(
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Room::query();

        $this->applyFilters($query, $filters);
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * 絞込条件を適用
     *
     * @param Builder<Room> $query
     * @param array<string, mixed> $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        // キーワード検索（必要に応じてコメントを外して使用）
        // if (! empty($filters['keyword'])) {
        //     $keyword = $filters['keyword'];
        //     $query->where(function (Builder $q) use ($keyword) {
        //         $q->where('title', 'like', "%{$keyword}%")
        //           ->orWhere('body', 'like', "%{$keyword}%");
        //     });
        // }

        // region（完全一致）
        if (isset($filters['region'])) {
            $query->where('region', $filters['region']);
        }

        // facility_code（完全一致）
        if (isset($filters['facility_code'])) {
            $query->where('facility_code', $filters['facility_code']);
        }

        // room_number（完全一致）
        if (isset($filters['room_number'])) {
            $query->where('room_number', $filters['room_number']);
        }

        // name（完全一致）
        if (isset($filters['name'])) {
            $query->where('name', $filters['name']);
        }

        // capacity（数値範囲）
        if (isset($filters['capacity'])) {
            $query->where('capacity', $filters['capacity']);
        }
        if (isset($filters['capacity_min'])) {
            $query->where('capacity', '>=', $filters['capacity_min']);
        }
        if (isset($filters['capacity_max'])) {
            $query->where('capacity', '<=', $filters['capacity_max']);
        }

        // is_active（フラグ）
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

    }

    /**
     * 複合キーで検索
     */
    public function findByCompositeKey(string $region, string $facilityCode, string $roomNumber): ?Room
    {
        return Room::query()
            ->where('region', $region)
            ->where('facility_code', $facilityCode)
            ->where('room_number', $roomNumber)
            ->first();
    }

    /**
     * 新規作成
     */
    public function create(RoomDTO $dto): Room
    {
        return Room::create($dto->toCreateArray());
    }

    /**
     * 更新（複合キー対応）
     */
    public function update(Room $model, RoomDTO $dto): Room
    {
        Room::query()
            ->where('region', $model->region)
            ->where('facility_code', $model->facility_code)
            ->where('room_number', $model->room_number)
            ->update($dto->toUpdateArray());

        return $this->findByCompositeKey($model->region, $model->facility_code, $model->room_number) ?? $model;
    }

    /**
     * 削除（複合キー対応）
     */
    public function delete(Room $model): void
    {
        Room::query()
            ->where('region', $model->region)
            ->where('facility_code', $model->facility_code)
            ->where('room_number', $model->room_number)
            ->delete();
    }
}
