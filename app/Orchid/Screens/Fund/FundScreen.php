<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Fund;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Orchid\Layouts\Fund\FundLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Color;


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
            Layout::tabs([
                'Total cash' => Layout::split([
                    Layout::view('platform::layouts.wallet_balance'),
                    Layout::rows([
                        Link::make('Add Funds')
                            ->route('platform.funds.payment_details')
                            ->icon('bs.plus-circle')
                            ->type(Color::DEFAULT)
                            ->class('btn btn-success')
                            ->style('color: white; width: 100%;'),
                        // Link::make('Withdraw Funds')
                        //     ->type(Color::DEFAULT)
                        //     ->class('btn btn-secondary')
                        //     ->style('color: white; width: 100%;'),
                    ])->canSee(auth()->user() && auth()->user()->hasAccess('platform.systems.users')),
                ])->ratio(('70/30')),
                'Recent Transactions' =>  FundLayout::class,
            ]),
        ];
    }
}
