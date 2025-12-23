<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\V1\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\IndexRequest;
use App\Http\Requests\Api\V1\User\StoreRequest;
use App\Http\Requests\Api\V1\User\UpdateRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Services\V1\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final class ApiUserController extends Controller
{
    public function __construct(
        private readonly UserService $service
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

        return UserResource::collection($paginator);
    }

    /**
     * 詳細取得
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * 新規作成
     */
    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $dto = UserDTO::fromArray($request->payload());
        $created = $this->service->create($dto);

        return (new UserResource($created))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * 更新
     */
    public function update(UpdateRequest $request, User $user): UserResource
    {
        $dto = UserDTO::fromArray($request->payload());
        $updated = $this->service->update($user, $dto);

        return new UserResource($updated);
    }

    /**
     * 削除
     */
    public function destroy(User $user): \Illuminate\Http\Response
    {
        $this->service->delete($user);

        return response()->noContent();
    }
}
