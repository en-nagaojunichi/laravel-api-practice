<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ApiFavoritePointController;
use App\Http\Controllers\Api\ApiPost2Controller;
use App\Http\Controllers\Api\ApiPostController;
use Illuminate\Support\Facades\Route;

// 例：疎通確認
Route::get('/health', fn () => ['ok' => true]);

Route::apiResource('favorite-points', ApiFavoritePointController::class);

Route::apiResource('posts', ApiPostController::class);

Route::apiResource('post2s', ApiPost2Controller::class);
