<?php

use App\Http\Controllers\Api\ApiPostController;
use App\Http\Controllers\Api\ApiFavoritePointController;
use Illuminate\Support\Facades\Route;

// 例：疎通確認
Route::get('/health', fn() => ['ok' => true]);

Route::apiResource('favorite-points', ApiFavoritePointController::class);

Route::apiResource('posts', ApiPostController::class);
