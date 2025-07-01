<?php

declare(strict_types=1);

use App\Orchid\Screens\User\ActivityHistoryScreen;
use App\Orchid\Screens\CryptoBasket\CryptoBasketEditScreen;
use App\Orchid\Screens\CryptoBasket\CryptoBasketListScreen;
use App\Orchid\Screens\CryptoBasket\OwnedBasketScreen;
use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\Fund\FundScreen;
use App\Orchid\Screens\Fund\PaymentDetailsScreen;
use App\Orchid\Screens\Fund\TransactionEditScreen;
use App\Orchid\Screens\Fund\UserBankDetailScreen;
use App\Orchid\Screens\Fund\WithdrawRequestsScreen;
use App\Orchid\Screens\Kyc\KycSubmissionListScreen;
use App\Orchid\Screens\PendingRequestScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\Kyc\KycSubmissionScreen;
use App\Orchid\Screens\Kyc\KycSubmissionViewScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn(Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn(Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

// Route::screen('idea', Idea::class, 'platform.screens.idea');
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
    ->name('platform.funds.payment_details')
    ->middleware('kyc.approved');

Route::screen('/funds/{transaction}/edit', TransactionEditScreen::class)
    ->name('platform.funds.edit');

Route::screen('/funds/activity_history', ActivityHistoryScreen::class)
    ->name('platform.funds.activity_history')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.wallet')
        ->push(__('Activity History'), route('platform.funds.activity_history')));

Route::screen('user/{activity}/activity_history', ActivityHistoryScreen::class)
    ->name('platform.user.activity_history');

Route::screen('/withdrawal-requests', WithdrawRequestsScreen::class)
    ->name('platform.fund.withdraw_requests')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.index')
        ->push(__('Withdrawal Requests'), route('platform.fund.withdraw_requests')));

Route::screen('/withdrawal-requests/{WalletWithdrawal}', UserBankDetailScreen::class)
    ->name('platform.fund.withdraw_requests.transfer')
    ->breadcrumbs(fn($trail, $WalletWithdrawal) => $trail
        ->parent('platform.fund.withdraw_requests')
        ->push(__('Transfer'), route('platform.fund.withdraw_requests.transfer', $WalletWithdrawal)));

Route::screen('/owned-baskets', OwnedBasketScreen::class)
    ->name('platform.owned-baskets')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.index')
        ->push(__('Owned Baskets'), route('platform.owned-baskets')));

Route::screen('fund-request', PendingRequestScreen::class)
    ->name('platform.systems.pending.requests')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.index')
        ->push(__('Fund Requests'), route('platform.systems.pending.requests')));

Route::screen('profile/kyc', KycSubmissionScreen::class)
    ->name('platform.user.kyc')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.profile')
        ->push(__('KYC'), route('platform.user.kyc')));

Route::screen('kyc-requests',KycSubmissionListScreen::class)
    ->name('platform.user.kyc.requests')
    ->breadcrumbs(fn($trail) => $trail
        ->parent('platform.index')
        ->push(__('KYC Requests'), route('platform.user.kyc.requests')));

Route::screen('kyc-requests/view/{id}', KycSubmissionViewScreen::class)
    ->name('platform.user.kyc.requests.view')
    ->breadcrumbs(fn($trail, $id) => $trail
        ->parent('platform.user.kyc.requests')
        ->push($id, route('platform.user.kyc.requests.view', $id)));