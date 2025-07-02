<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Fund;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WalletWithdrawal;
use App\Orchid\Layouts\Fund\FundLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class FundScreen extends Screen
{
    public $balance;
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
            ->whereIn('type', ['credit', 'debit', 'deposit', 'refund', 'withdraw'])
            ->whereNotNull('status')
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
                ->icon('bs.cash-stack')
                ->route('platform.funds.payment_details')
                ->canSee(auth()->user()?->hasAccess('platform.funds.wallet')),

            ModalToggle::make('Withdraw Funds')
                ->modal('withdrawalModal')
                ->method('withdrawalRequest')
                ->icon('bs.check-circle'),

            Link::make(__('Activity History'))
                ->icon('bs.clock-history')
                ->route('platform.funds.activity_history')
                ->canSee(auth()->user()?->hasAccess('platform.funds.wallet')),


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
            Layout::modal('withdrawalModal', Layout::rows([
                Input::make('amount')
                    ->title('Amount')
                    ->type('number')
                    ->placeholder('Enter the amount you want to withdraw')
                    ->required(),

            ]))->title('Withdrawal')
                ->applyButton('Submit'),
        ];
    }

    public function withdrawalRequest(Request $request)
    {

        $user = auth()->user();
        $withdrawalAmount = $request->input('amount');

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        if ($withdrawalAmount > $this->balance) {
            return redirect()->back()->withErrors(['amount' => 'Insufficient balance']);
        }

        // Create a new wallet transaction for withdrawal
        $WalletTransaction = WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $withdrawalAmount,
            'type' => 'withdraw',
            'note' => 'Withdrawal request',
            'status' => 'pending',
            'source' => 'wallet withdrawal'
        ]);
        // Add wallet withdrawal request logic here
        WalletWithdrawal::create([
            'user_id' => $user->id,
            'amount' => $withdrawalAmount,
            'status' => 'pending',
            'note' => 'Withdrawal request',
            'wallet_transaction_id' => $WalletTransaction->id,
        ]);

        

        Toast::success('Withdrawal request submitted successfully. Your request is under review.');

        return redirect()->route('platform.wallet');

    }
}
