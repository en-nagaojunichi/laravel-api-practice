<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V2;

use App\DTOs\V2\RoomDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\Room\IndexRequest;
use App\Http\Requests\Api\V2\Room\StoreRequest;
use App\Http\Requests\Api\V2\Room\UpdateRequest;
use App\Http\Resources\V2\RoomResource;
use App\Services\V2\RoomService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ApiRoomController extends Controller
{
    public function __construct(
        private readonly RoomService $service
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

        return RoomResource::collection($paginator);
    }

    /**
     * 詳細取得（複合キー）
     */
    public function show(string $region, string $facilityCode, string $roomNumber): RoomResource
    {
        $model = $this->service->findByCompositeKey($region, $facilityCode, $roomNumber);

        if ($model === null) {
            throw new NotFoundHttpException('リソースが見つかりません。');
        }

        return new RoomResource($model);
    }

    /**
     * 新規作成
     */
    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $dto = RoomDTO::fromArray($request->payload());
        $created = $this->service->create($dto);

        return (new RoomResource($created))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * 更新（複合キー）
     */
    public function update(UpdateRequest $request, string $region, string $facilityCode, string $roomNumber): RoomResource
    {
        $model = $this->service->findByCompositeKey($region, $facilityCode, $roomNumber);

        if ($model === null) {
            throw new NotFoundHttpException('リソースが見つかりません。');
        }

        $dto = RoomDTO::fromArray($request->payload());
        $updated = $this->service->update($model, $dto);

        return new RoomResource($updated);
    }

    /**
     * 削除（複合キー）
     */
    public function destroy(string $region, string $facilityCode, string $roomNumber): \Illuminate\Http\Response
    {
        $model = $this->service->findByCompositeKey($region, $facilityCode, $roomNumber);

        if ($model === null) {
            throw new NotFoundHttpException('リソースが見つかりません。');
        }

        $this->service->delete($model);

        return response()->noContent();
    }
}
