<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\Post2DTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Post2\StoreRequest;
use App\Http\Requests\Api\Post2\UpdateRequest;
use App\Http\Resources\Post2Resource;
use App\Models\Post;
use App\Services\Post2Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final class ApiPost2Controller extends Controller
{
    public function __construct(
        private readonly Post2Service $service
    ) {
    }

    /**
     * 一覧取得（ページネーション）
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 20);
        $paginator = $this->service->paginate($perPage);

        return Post2Resource::collection($paginator);
    }

    /**
     * 詳細取得
     */
    public function show(Post $post2): Post2Resource
    {
        return new Post2Resource($post2);
    }

    /**
     * 新規作成
     */
    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $dto = Post2DTO::fromArray($request->payload());
        $created = $this->service->create($dto);

        return (new Post2Resource($created))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * 更新
     */
    public function update(UpdateRequest $request, Post $post2): Post2Resource
    {
        $dto = Post2DTO::fromArray($request->payload());
        $updated = $this->service->update($post2, $dto);

        return new Post2Resource($updated);
    }

    /**
     * 削除
     */
    public function destroy(Post $post2): \Illuminate\Http\Response
    {
        $this->service->delete($post2);

        return response()->noContent();
    }
}
