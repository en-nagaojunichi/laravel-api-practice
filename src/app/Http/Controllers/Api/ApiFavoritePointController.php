<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\FavoritePointDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FavoritePoint\StoreRequest;
use App\Http\Requests\Api\FavoritePoint\UpdateRequest;
use App\Http\Resources\FavoritePointResource;
use App\Models\FavoritePoint;
use App\Services\FavoritePointService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final class ApiFavoritePointController extends Controller
{
    public function __construct(
        private readonly FavoritePointService $service
    ) {}

    /**
     * 一覧取得（ページネーション）
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 20);
        $paginator = $this->service->paginate($perPage);

        return FavoritePointResource::collection($paginator);
    }

    /**
     * 詳細取得
     */
    public function show(FavoritePoint $favorite_point): FavoritePointResource
    {
        return new FavoritePointResource($favorite_point);
    }

    /**
     * 新規作成
     */
    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $dto = FavoritePointDTO::fromArray($request->payload());
        $created = $this->service->create($dto);

        return (new FavoritePointResource($created))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * 更新
     */
    public function update(UpdateRequest $request, FavoritePoint $favorite_point): FavoritePointResource
    {
        $dto = FavoritePointDTO::fromArray($request->payload());
        $updated = $this->service->update($favorite_point, $dto);

        return new FavoritePointResource($updated);
    }

    /**
     * 削除
     */
    public function destroy(FavoritePoint $favorite_point): \Illuminate\Http\Response
    {
        $this->service->delete($favorite_point);

        return response()->noContent();
    }
}