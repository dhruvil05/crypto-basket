<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Orchid\Screens\ReferralSettingsScreen;

Route::get('/register', function () {
    return view('auth.register');
})->name('platform.register');

Route::post('/register', [RegisterController::class, 'register'])->name('platform.register.auth');

Route::screen('/referral/settings', ReferralSettingsScreen::class)
    ->name('platform.systems.settings');

Route::get('/', [HomeController::class, 'index'])
    ->name('platform.index')
    ->breadcrumbs(fn($trail) => $trail
        ->push(__('Home'), route('platform.index')));

