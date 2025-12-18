<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api.logger')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        Route::middleware('auth:api')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    // Public Menu Routes
    Route::get('/menu-items', [MenuController::class, 'index']);
    Route::get('/menu-items/{id}', [MenuController::class, 'show']);

    // Protected Menu Routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/menu-items', [MenuController::class, 'store']);
        Route::match(['put', 'patch'], '/menu-items/{id}', [MenuController::class, 'update']);
        Route::delete('/menu-items/{id}', [MenuController::class, 'destroy']);
    });

    Route::get('todos/{todo}/attachment', [TodoController::class, 'downloadAttachment'])->name('todos.attachment');
    Route::apiResource('todos', TodoController::class);
});
