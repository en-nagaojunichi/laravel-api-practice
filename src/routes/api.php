<?php

use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\ApiRoomController;
use App\Http\Controllers\Api\ApiFavoritePointController;
use App\Http\Controllers\Api\ApiPost2Controller;
use App\Http\Controllers\Api\ApiPostController;
use Illuminate\Support\Facades\Route;

// 例：疎通確認
Route::get('/health', fn() => ['ok' => true]);

Route::apiResource('favorite-points', ApiFavoritePointController::class);

Route::apiResource('posts', ApiPostController::class);

Route::apiResource('post2s', ApiPost2Controller::class);

// Room API（複合キー: region, facility_code, room_number）
Route::prefix('rooms')->group(function () {
    Route::get('/', [ApiRoomController::class, 'index']);
    Route::post('/', [ApiRoomController::class, 'store']);
    Route::get('/{region}/{facilityCode}/{roomNumber}', [ApiRoomController::class, 'show']);
    Route::put('/{region}/{facilityCode}/{roomNumber}', [ApiRoomController::class, 'update']);
    Route::delete('/{region}/{facilityCode}/{roomNumber}', [ApiRoomController::class, 'destroy']);
});

Route::apiResource('users', ApiUserController::class);
