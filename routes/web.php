<?php

use App\Helpers\ResponseApi;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'))
    ->name('login');

Route::get('/success', fn() => view('success'));

Route::get('/{route}', function () {
    return view('app');
});