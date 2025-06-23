<?php

namespace App\Orchid\Screens\Fund;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Orchid\Layouts\Fund\AmountDetailLayout;
use App\Orchid\Layouts\Fund\TransactionDetailsLayout;
use App\Orchid\Layouts\Fund\TransactionStatusLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Events\WalletDepositRejected;

class TransactionEditScreen extends Screen
{
    protected $transaction;
    protected $wallet;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {   
        $user = Auth::user();
        
        if($user && $user->inRole('admin')){
            $userID = request()->get('transaction');
        }else{
            $userID = $user?->id;
        }

        $this->transaction = WalletTransaction::where('id', request()->route('transaction'))
        ->first();
        
        $this->wallet = Wallet::where('user_id', $userID)->first();

        return [
            'transaction' => $this->transaction,
            'wallet' => $this->wallet,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Edit Transaction';
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
            Layout::block(TransactionDetailsLayout::class)
                ->title(__('Screenshot / UTR'))
                ->description(__("Transaction details, such as screenshot or UTR number.")),

            Layout::block(AmountDetailLayout::class)
                ->title(__('Amount Details'))
                ->description(__("Update the amount for this transaction."))
                ->commands([
                    Button::make(__('Save'))
                        ->icon('check')
                        ->method('updateTransactionAmount')
                        ->confirm(__('Are you sure you want to update this transaction?'))
                        ->parameters(['transaction_id' => request()->route('transaction')]),
                ])
                ->canSee(auth()->user()?->hasAccess('platform.funds.edit')),

            Layout::block(TransactionStatusLayout::class)
                ->title(__('Status'))
                ->description(__("Update the status of the transaction."))
                ->commands([
                    Button::make(__('Save'))
                        ->icon('check')
                        ->method('transactionStatus')
                        ->confirm(__('Are you sure you want to update this transaction?'))
                        ->parameters(['transaction_id' => request()->route('transaction')]),
                ])
                ->canSee(auth()->user()?->hasAccess('platform.funds.edit')),

            Layout::view('vendor.platform.script.admin_comment_script'),
        ];
    }

    public function transactionStatus(Request $request)
    {
        $status = $request->get('transaction')['status'];
        $request->validate([
            'transaction.status' => 'required|in:approved,rejected,pending', // adjust statuses as needed
            'transaction.admin_comment' => $status === 'rejected' ? 'required|string|min:3' : 'nullable',
        ], 
        [
            'transaction.admin_comment.required' => __('Admin comment is required for rejection.'),
            'transaction.admin_comment.min' => __('Admin comment must be at least 3 characters long.'),
        ]);

        $transactionId = request()->route('transaction');

        $transaction = WalletTransaction::find($transactionId);

        if ($transaction) {
            $oldStatus = $transaction->status;

            $wallet = Wallet::where('user_id', $transaction->user_id)->first();
            // check if the wallet exists
            if (!$wallet) {
                Wallet::create([
                    'user_id' => $transaction->user_id,
                    'balance' => 0,
                ]);
                $wallet = Wallet::where('user_id', $transaction->user_id)->first();
            }

            if ($status === 'approved' && $oldStatus !== 'approved' && !$transaction->amount_added) {

                if ($wallet) {
                    $wallet->balance += $transaction->amount;
                    $wallet->save();

                    $transaction->amount_added = true; // Set the flag to true
                    Toast::success(__('Transaction approved and amount added to wallet.'));
                }
            } elseif ($status === 'rejected' && $oldStatus === 'approved' && $transaction->amount_added) {
                // Subtract amount if it's being rejected after approval
                if ($wallet) {
                    $wallet->balance -= $transaction->amount;
                    $wallet->save();

                    $transaction->amount_added = false; // Reset the flag
                    Toast::info(__('Transaction rejected and amount subtracted from wallet.'));
                }
            } else {
                Toast::info(__('Transaction status updated.'));
            }

            $isStatusChangedToRejected = $status === 'rejected' && $oldStatus !== 'rejected';

            $transaction->status = $status;
            $transaction->admin_comment = $status === 'rejected'
                ? ($request->get('transaction')['admin_comment'] ?? null)
                : null;
            $transaction->reviewed_by = auth()->user()?->id;
            $transaction->reviewed_at = now();
            $transaction->save();

            if ($isStatusChangedToRejected) {
                event(new WalletDepositRejected($transaction));
            }

            Toast::success(__('Transaction status updated successfully.'));
        } else {
            Toast::error(__('Transaction not found.'));
        }

        return redirect()->route('platform.wallet');
    }

    public function updateTransactionAmount(Request $request)
    {
        $validated = $request->validate([
            'transaction.amount' => 'required|numeric|min:1',
        ]);

        $amount = $validated['transaction']['amount'];
        $transactionId = request()->route('transaction');
        $walletTransaction = WalletTransaction::find($transactionId);

        if ($walletTransaction) {
            $walletTransaction->amount = $amount;
            $walletTransaction->save();


            Toast::success(__('Transaction amount updated successfully.'));
        } else {
            Toast::error(__('Transaction not found.'));
            return redirect()->route('platform.wallet');
        }
    }
}
