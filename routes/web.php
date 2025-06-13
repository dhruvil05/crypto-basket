<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/register', function () {
    return view('auth.register');
});
Route::post('/register', [RegisterController::class, 'register'])->name('platform.register.auth');
