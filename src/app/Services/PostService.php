<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\PostDTO;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class PostService
{
    /**
     * 検索・絞込・並び替え付きページネーション
     *
     * @param array<string, mixed> $filters 絞込条件
     * @param string $sortBy 並び替えカラム
     * @param string $sortOrder 昇順(asc)/降順(desc)
     * @param int $perPage 1ページあたりの件数
     * @return LengthAwarePaginator<int, Post>
     */
    public function search(
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortOrder = 'desc',
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Post::query();

        $this->applyFilters($query, $filters);
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * 絞込条件を適用
     *
     * @param Builder<Post> $query
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

        // user_id（外部キー）
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // status（ステータス）
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // published_at（日付範囲）
        if (isset($filters['published_at'])) {
            $query->whereDate('published_at', $filters['published_at']);
        }
        if (isset($filters['published_at_from'])) {
            $query->where('published_at', '>=', $filters['published_at_from'] . ' 00:00:00');
        }
        if (isset($filters['published_at_to'])) {
            $query->where('published_at', '<=', $filters['published_at_to'] . ' 23:59:59');
        }

    }

    /**
     * 新規作成
     */
    public function create(PostDTO $dto): Post
    {
        return Post::create($dto->toCreateArray());
    }

    /**
     * 更新
     */
    public function update(Post $model, PostDTO $dto): Post
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
