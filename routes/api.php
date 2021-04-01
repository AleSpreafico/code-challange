<?php

use App\Http\Controllers\CommentEventsController;
use App\Http\Controllers\CommentNewsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('news', NewsController::class);
Route::apiResource('news.comments', CommentNewsController::class)
    ->shallow()
    ->except([
        'index',
        'show',
        'update'
    ]);

Route::apiResource('events', EventsController::class);
Route::apiResource('events.comments', CommentEventsController::class)
    ->shallow()
    ->except([
        'index',
        'show',
        'update'
    ]);
