<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class UserService
{
    /**
     * 検索・絞込・並び替え付きページネーション
     *
     * @param array<string, mixed> $filters 絞込条件
     * @param string $sortBy 並び替えカラム
     * @param string $sortOrder 昇順(asc)/降順(desc)
     * @param int $perPage 1ページあたりの件数
     * @return LengthAwarePaginator<int, User>
     */
    public function search(
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = User::query();

        $this->applyFilters($query, $filters);
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * 絞込条件を適用
     *
     * @param Builder<User> $query
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

        // name（部分一致）
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // email（完全一致）
        if (isset($filters['email'])) {
            $query->where('email', $filters['email']);
        }

        // email_verified_at（日付範囲）
        if (isset($filters['email_verified_at'])) {
            $query->whereDate('email_verified_at', $filters['email_verified_at']);
        }
        if (isset($filters['email_verified_at_from'])) {
            $query->where('email_verified_at', '>=', $filters['email_verified_at_from'] . ' 00:00:00');
        }
        if (isset($filters['email_verified_at_to'])) {
            $query->where('email_verified_at', '<=', $filters['email_verified_at_to'] . ' 23:59:59');
        }

    }

    /**
     * 新規作成
     */
    public function create(UserDTO $dto): User
    {
        return User::create($dto->toCreateArray());
    }

    /**
     * 更新
     */
    public function update(User $model, UserDTO $dto): User
    {
        $model->fill($dto->toUpdateArray());
        $model->save();

        return $model->refresh();
    }

    /**
     * 削除
     */
    public function delete(User $model): void
    {
        $model->delete();
    }
}
