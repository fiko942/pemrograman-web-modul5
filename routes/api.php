<?php

use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api.logger')->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('todos/{todo}/attachment', [TodoController::class, 'downloadAttachment'])->name('todos.attachment');
    Route::apiResource('todos', TodoController::class);
});
