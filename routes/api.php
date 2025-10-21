<?php

use App\Helpers\ResponseApi;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return ResponseApi::success($request->user());
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function () {
    Route::get('/google/redirect', [LoginController::class, 'redirect']);
    Route::get('/google/callback', [LoginController::class, 'callback']);
    Route::get('/google/get-token', [LoginController::class, 'getToken']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/list-song', [SongController::class, 'listSong']);
    Route::get('/update-high-score', [SongController::class, 'updateHighScore']);

    Route::get('/save-game', [UserController::class, 'saveGame']);
    Route::post('/add-new-song', [SongController::class, 'addNewSong']);
});