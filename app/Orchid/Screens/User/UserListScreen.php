<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserFiltersLayout;
use App\Orchid\Layouts\User\UserListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Orchid\Layouts\User\UserAddFundLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'users' => User::with('roles')
                ->filters(UserFiltersLayout::class)
                ->defaultSort('id', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'User Management';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'A comprehensive list of all registered users, including their profiles and privileges.';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('bs.plus-circle')
                ->route('platform.systems.users.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            UserFiltersLayout::class,
            UserListLayout::class,

            Layout::modal('editUserModal', UserEditLayout::class)
                ->deferred('loadUserOnOpenModal'),

            Layout::modal('addFundsModal', UserAddFundLayout::class)
                ->deferred('loadUserOnOpenModal')
                ->title(__('Add Funds'))
                ->applyButton(__('Add Funds'))
                ->closeButton(__('Close')),
        ];
    }

    /**
     * Loads user data when opening the modal window.
     *
     * @return array
     */
    public function loadUserOnOpenModal(User $user): array
    {
        return [
            'user' => $user,
        ];
    }

    public function saveUser(Request $request, User $user): void
    {
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $user->fill($request->input('user'))->save();

        Toast::info(__('User was saved.'));
    }

    public function remove(Request $request): void
    {
        User::findOrFail($request->get('id'))->delete();

        Toast::info(__('User was removed'));
    }

    public function addFunds(Request $request, User $user): void
    {
        $request->validate([
            'funds' => 'required|numeric|min:1',
        ]);

        $amount = $request->input('funds');
        $wallet = Wallet::where('user_id', $user->id)->first();

        if ($wallet) {
            $wallet->increment('balance', $amount);
        } else {
            // Optionally, create a wallet if it doesn't exist
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => $amount,
            ]);
        }

        // Add to transaction history if applicable
        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'note' => "Added funds to wallet by Admin",
            'source' => 'manual',
            'status' => 'approved',
        ]);

        // Notify the user or perform any other actions as needed


        Toast::info("₹{$amount} has been added to {$user->name}'s wallet.");
    }
}
