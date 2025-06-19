<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Orchid\Screens\CryptoBasket\CryptoBasketEditScreen;
use App\Orchid\Screens\CryptoBasket\CryptoBasketListScreen;
use App\Orchid\Screens\Fund\FundScreen;
use App\Orchid\Screens\Fund\PaymentDetailsScreen;
use App\Orchid\Screens\Fund\TransactionEditScreen;
use App\Orchid\Screens\ReferralSettingsScreen;

Route::get('/register', function () {
    return view('auth.register');
});
Route::post('/register', [RegisterController::class, 'register'])->name('platform.register.auth');

Route::screen('/referral/settings', ReferralSettingsScreen::class)
    ->name('platform.systems.settings');

// List all baskets
Route::screen('/crypto-basket', CryptoBasketListScreen::class)
    ->name('platform.baskets')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.index')
        ->push(__('Crypto Baskets'), route('platform.baskets')));

// Create new basket
Route::screen('/crypto-basket/create', CryptoBasketEditScreen::class)
    ->name('platform.baskets.create')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.baskets')
        ->push(__('Create'), route('platform.baskets.create')));

// Edit existing basket (uses route model binding)
Route::screen('/crypto-basket/{cryptoBasket}/edit', CryptoBasketEditScreen::class)
    ->name('platform.baskets.edit')
    ->breadcrumbs(fn($trail, $crypto_basket) => $trail
        ->parent('platform.baskets')
        ->push(__('Edit'), route('platform.baskets.edit', $crypto_basket)));


Route::screen('/wallet', FundScreen::class)
    ->name('platform.wallet')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.index')
        ->push(__('Wallet'), route('platform.wallet')));

Route::screen('/funds/payment_details', PaymentDetailsScreen::class)
    ->name('platform.funds.payment_details');

Route::screen('/funds/{transaction}/edit', TransactionEditScreen::class)
    ->name('platform.funds.edit');
