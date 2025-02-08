<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);

    Route::get('/ods/categories', function (): JsonResponse {
        return response()->json(Category::all());
    });


    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('/activities', ActivityController::class);

    Route::put('/activities/{id}', [ActivityController::class, 'update']);
    Route::patch('/activities/{id}', [ActivityController::class, 'update']);

    Route::get('/ranking', [PointController::class, 'index']);
    Route::get('/me/points', [PointController::class, 'myPoints']);
});
