<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Fund;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Orchid\Layouts\Fund\FundLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;


class FundScreen extends Screen
{
    protected $balance;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $this->balance = Wallet::where('user_id', auth()->user()?->id)
            ->value('balance');
        if ($this->balance === null) {

            $this->balance = 0;
        }
        $walletTransactions = WalletTransaction::where('user_id', auth()->user()?->id)
            ->latest()
            ->paginate(5);

        return [
            'balance' => $this->balance ?? 0,
            'transactions' => $walletTransactions
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Wallet';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add Funds'))
                ->icon('bs.plus-circle')
                ->route('platform.funds.payment_details')
                ->canSee( auth()->user()?->hasAccess('platform.funds.wallet')),

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::split([
                FundLayout::class,
                Layout::view('platform::layouts.wallet_balance'),
            ])->ratio('70/30'),

            // Layout::tabs([
            //     'Total cash' => Layout::split([
            //         Layout::view('platform::layouts.wallet_balance'),
            //         FundLayout::class,
            //     ])->ratio(('30/70')),
            //     'Recent Transactions' =>  FundLayout::class,
            // ]),
        ];
    }
}
