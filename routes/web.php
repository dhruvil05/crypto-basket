<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Orchid\Screens\ReferralSettingsScreen;

Route::get('/register', function () {
    return view('auth.register');
});
Route::post('/register', [RegisterController::class, 'register'])->name('platform.register.auth');

Route::screen('/referral/settings', ReferralSettingsScreen::class)
    ->name('platform.systems.settings');