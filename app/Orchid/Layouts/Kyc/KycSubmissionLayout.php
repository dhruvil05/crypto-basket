<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Kyc;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class KycSubmissionLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [

            // PAN Card Image
            Upload::make('kyc.pan_card_img')
                ->required()
                ->title(__('PAN Card Image'))
                ->acceptedFiles('image/*')
                ->maxFiles(1),

            // Aadhar Card Image
            Upload::make('kyc.aadhar_card_img')
                ->required()
                ->title(__('Aadhar Card Image'))
                ->acceptedFiles('image/*')
                ->maxFiles(1),

            // Passport Size Image
            Upload::make('kyc.passport_img')
                ->required()
                ->title(__('Passport Size Image'))
                ->acceptedFiles('image/*')
                ->maxFiles(1),

            // Bank Details
            Input::make('kyc.bank_account_holder')
                ->type('text')
                ->required()
                ->title(__('Account Holder Name'))
                ->placeholder(__('Account Holder Name')),

            Input::make('kyc.bank_account_number')
                ->type('text')
                ->required()
                ->title(__('Account Number'))
                ->placeholder(__('Account Number')),

            Input::make('kyc.bank_ifsc')
                ->type('text')
                ->required()
                ->title(__('IFSC Code'))
                ->placeholder(__('IFSC Code')),

            Input::make('kyc.bank_name')
                ->type('text')
                ->required()
                ->title(__('Bank Name'))
                ->placeholder(__('Bank Name')),

            // Bank Book Image
            Upload::make('kyc.bank_book_img')
                ->required()
                ->title(__('Bank Book Image'))
                ->acceptedFiles('image/*')
                ->maxFiles(1),
        ];
    }
}
