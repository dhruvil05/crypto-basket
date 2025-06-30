<?php

namespace App\Orchid\Screens\Fund;

use App\Orchid\Layouts\Fund\WithdrawRequestsLayout;
use Orchid\Screen\Screen;

class WithdrawRequestsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $withdrawRequests = auth()->user()
            ->walletWithdrawals()
            ->where('status', 'pending') // Exclude pending requests
            ->with('user')
            ->latest()
            ->paginate(10);

        return [
            'WalletWithdrawal' => $withdrawRequests
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Withdrawal Requests';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            WithdrawRequestsLayout::class,
        ];
    }
}
