<?php

use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function () {
    Route::get('/google/redirect', [LoginController::class, 'redirect']);
    Route::get('/google/callback', [LoginController::class, 'callback']);
});