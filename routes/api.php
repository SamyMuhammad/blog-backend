<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Resources\UserResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('guest:sanctum')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('api.register');
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function (Request $request) {
        return ['data' => new UserResource($request->user()), 'token' => $request->bearerToken()];
    });

    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('article.comment', CommentController::class)
    ->shallow()
    ->only(['store', 'update', 'destroy']);
});

Route::get('my-articles', [ArticleController::class, 'myArticles'])->name('articles.my-articles');
Route::apiResource('article', ArticleController::class);

