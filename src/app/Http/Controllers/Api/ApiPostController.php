<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\PostDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Post\IndexRequest;
use App\Http\Requests\Api\Post\StoreRequest;
use App\Http\Requests\Api\Post\UpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final class ApiPostController extends Controller
{
    public function __construct(
        private readonly PostService $service
    ) {
    }

    /**
     * 一覧取得（検索・絞込・並び替え・ページネーション）
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $paginator = $this->service->search(
            filters: $request->validated(),
            sortBy: $request->validated('sort_by', 'created_at'),
            sortOrder: $request->validated('sort_order', 'desc'),
            perPage: (int) $request->validated('per_page', 15)
        );

        return PostResource::collection($paginator);
    }

    /**
     * 詳細取得
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    /**
     * 新規作成
     */
    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $dto = PostDTO::fromArray($request->payload());
        $created = $this->service->create($dto);

        return (new PostResource($created))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * 更新
     */
    public function update(UpdateRequest $request, Post $post): PostResource
    {
        $dto = PostDTO::fromArray($request->payload());
        $updated = $this->service->update($post, $dto);

        return new PostResource($updated);
    }

    /**
     * 削除
     */
    public function destroy(Post $post): \Illuminate\Http\Response
    {
        $this->service->delete($post);

        return response()->noContent();
    }
}
