<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SnippetController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\HealthCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User routes
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'profile']);

// Authentication Routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Snippet Routes
Route::apiResource('snippets', SnippetController::class);
Route::post('snippets/{id}/favorite', [SnippetController::class, 'toggleFavorite']);
Route::post('snippets/create-or-update/{id?}', [SnippetController::class, 'storeOrUpdate']);

// Tag Routes
Route::apiResource('tags', TagController::class);

// Health check route
Route::get('/test', [HealthCheckController::class, 'test']);
