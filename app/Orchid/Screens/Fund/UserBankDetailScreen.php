<?php

namespace App\Orchid\Screens\Fund;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WalletWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserBankDetailScreen extends Screen
{
    public $walletWithdrawal;
    public $payment_details;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        // dd($request->route('WalletWithdrawal'));
        $this->walletWithdrawal = WalletWithdrawal::with('user.kycSubmission')
            ->findOrFail($request->route('WalletWithdrawal'));

        $this->payment_details = $this->walletWithdrawal->user->kycSubmission;

        return [
            'walletWithdrawal' => $this->walletWithdrawal,
            'payment_details' => $this->payment_details,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'User Bank Details';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Confirm Payment')
                ->modal('confirmPaymentModal')
                ->method('confirmPayment')
                ->icon('bs.check-circle'),
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
            Layout::legend('payment_details', [
                Sight::make('description', '')
                    ->render(fn() => '<p class="text-muted">Please transfer the amount to the following details:</p>'),

                Sight::make('bank_name', 'Bank Name')
                    ->render(fn($payment) => $payment->bank_name),

                Sight::make('bank_account_holder', 'Holder Name')
                    ->render(fn($payment) => $payment->bank_account_holder ?? 'Not Available'),

                Sight::make('bank_account_number', 'Account Number')
                    ->render(fn($payment) => $payment->bank_account_number),

                Sight::make('bank_ifsc', 'IFSC Code')
                    ->render(fn($payment) => $payment->bank_ifsc ?? 'Not Available'),

            ])->title('Payment Details'),

            Layout::modal('confirmPaymentModal', Layout::rows([
                Input::make('amount')
                    ->title('Amount')
                    ->type('number')
                    ->placeholder('Enter the amount you transferred')
                    ->required()
                    ->value(function () {
                        return $this->walletWithdrawal->amount;
                    }),

                Input::make('utr_number')
                    ->title('UTR / Transaction ID')
                    ->required(),

                Upload::make('payment_screenshot')
                    ->title('Payment Screenshot')
                    ->maxFiles(1)
                    ->acceptedFiles('image/*'),

            ]))->title('Confirm Payment')
                ->applyButton('Submit'),
        ];
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_screenshot' => 'required|array',
            'payment_screenshot.0' => 'required|integer|exists:attachments,id',
            'utr_number' => 'required|string',
        ]);

        $walletWithdrawal = $this->walletWithdrawal;
        $amount = $request->input('amount');
        $utr_number = $request->input('utr_number');
        $attachmentId = $request->input('payment_screenshot')[0];
        $attachment = Attachment::find($attachmentId);

        if ($attachment) {
            $fullPath = Storage::url($attachment->path . $attachment->name . '.' . $attachment->extension);

            $walletWithdrawal->update([
                'utr_number' => $utr_number,
                'screenshot' => $fullPath,
                'status' => 'completed',
            ]);

            // Create or update a new wallet transaction for this payment
            WalletTransaction::updateOrCreate(
                [
                    'id' => $walletWithdrawal->wallet_transaction_id ?: null,
                ],
                [
                    'user_id' => $walletWithdrawal->user_id,
                    'type' => 'withdraw',
                    'status' => 'completed',
                    'source' => 'wallet withdrawal',
                    'reference_id' => $walletWithdrawal->id,
                ],
                [
                    'amount' => $amount,
                    'note' => 'Withdrawal completed',
                ]
            );

            // Optionally, you can also update the user's wallet balance here
            Wallet::where('user_id', $walletWithdrawal->user_id)
                ->decrement('balance', $amount);

            Toast::success('Payment details submitted successfully. Please wait for admin approval.');
            
            return redirect()->route('platform.funds.payment_details');

            // Toast::info('Payment details submitted successfully. Please wait for admin approval.');
            // return redirect()->route('platform.wallet'); // Redirect to the funds screen
        } else {
            Toast::error('Attachment not found.');
            return back()->withInput();
        }
    }
}
