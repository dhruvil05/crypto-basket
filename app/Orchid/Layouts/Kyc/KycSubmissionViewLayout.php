<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Kyc;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class KycSubmissionViewLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [

            Input::make('kyc_data.bank_account_holder')
                ->type('text')
                ->readonly()
                ->title(__('Bank Account Holder'))
                ->placeholder(__('Enter your bank account holder name')),

            Input::make('kyc_data.bank_account_number')
                ->type('text')
                ->readonly()
                ->title(__('Bank Account Number'))
                ->placeholder(__('Enter your bank account number')),

            Input::make('kyc_data.bank_ifsc')
                ->type('text')
                ->readonly()
                ->title(__('Bank IFSC Code'))
                ->placeholder(__('Enter your bank IFSC code')),

            Input::make('kyc_data.bank_name')
                ->type('text')
                ->readonly()
                ->title(__('Bank Name'))
                ->placeholder(__('Enter your bank name')),

        ];
    }
}
