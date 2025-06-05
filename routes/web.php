<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    return response()->json(['error' => 'Unauthenticated.'], 401)->header('Content-Type', 'application/json');
})->name('login');