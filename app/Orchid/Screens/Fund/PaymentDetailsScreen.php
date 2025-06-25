<?php

namespace App\Orchid\Screens\Fund;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Orchid\Support\Color;

class PaymentDetailsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'showSuccessModal' => session()->get('showSuccessModal', false),
            'payment_details' => (object) [
                'bank_name' => 'XYZ Bank',
                'account_number' => '1234567890',
                'ifsc_code' => 'XYZ0000001',
                'upi_id' => 'admin@upi',
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Payment Details';
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
            // Layout::view('orchid.funds.payment_details'),
            Layout::legend('payment_details', [
                Sight::make('description', '')
                    ->render(fn () => '<p class="text-muted">Please transfer the amount to the following details:</p>'),
                
                Sight::make('bank_name', 'Bank Name')
                    ->render(fn ($payment) => $payment->bank_name),
                
                Sight::make('account_number', 'Account Number')
                    ->render(fn ($payment) => $payment->account_number),
                
                Sight::make('ifsc_code', 'IFSC Code')
                    ->render(fn ($payment) => $payment->ifsc_code),
                
                Sight::make('upi_id', 'UPI ID')
                    ->render(fn ($payment) => $payment->upi_id),
            ])->title('Payment Details'),

            Layout::modal('confirmPaymentModal', Layout::rows([
                Input::make('amount')
                    ->title('Amount')
                    ->type('number')
                    ->placeholder('Enter the amount you transferred')
                    ->required(),

                Input::make('utr_number')
                    ->title('UTR / Transaction ID')
                    ->required(),

                Upload::make('payment_screenshot')
                    ->title('Payment Screenshot')
                    ->maxFiles(1)
                    ->acceptedFiles('image/*'),

            ]))->title('Confirm Payment')
                ->applyButton('Submit'),

            
            Layout::modal('successNoticeModal', [
                Layout::view('orchid.funds.success_modal'),
            ])
                ->title('Payment Submitted')
                ->applyButton('OK')
                ->method('redirectToWallet')
                ->closeButton('')
                ->open(session()->get('showSuccessModal', false)),
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

        $attachmentId = $request->input('payment_screenshot')[0];
        $attachment = Attachment::find($attachmentId);

        if ($attachment) {
            $fullPath = Storage::url($attachment->path . $attachment->name . '.' . $attachment->extension);

            WalletTransaction::create([
                'user_id' => auth()->user()->id,
                'amount' => $request->get('amount'),
                'type' => 'deposit',
                'status' => 'pending',
                'source' => 'manual',
                'utr' => $request->input('utr_number'),
                'screenshot' => $fullPath, // Store the full path
            ]);

            session()->flash('showSuccessModal', true);
            return redirect()->route('platform.funds.payment_details');

            // Toast::info('Payment details submitted successfully. Please wait for admin approval.');
            // return redirect()->route('platform.wallet'); // Redirect to the funds screen
        } else {
            Toast::error('Attachment not found.');
            return back()->withInput();
        }
    }


    public function redirectToWallet()
    {
        return redirect()->route('platform.wallet');
    }
}