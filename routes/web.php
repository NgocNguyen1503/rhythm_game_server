<?php

use App\Helpers\ResponseApi;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => ResponseApi::unauthorized())
    ->name('login');

Route::get('/auth', fn() => view('auth'));

Route::get('/{route}', function () {
    return view('app');
});