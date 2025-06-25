<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Kyc;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Fields\Group;

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

            // Bank Details
            Input::make('kyc.bank_account_holder')
                ->type('text')
                ->required()
                ->title(__('Account Holder Name'))
                ->placeholder(__('Account Holder Name'))
                ->horizontal(),

            Input::make('kyc.bank_account_number')
                ->type('text')
                ->required()
                ->title(__('Account Number'))
                ->placeholder(__('Account Number'))
                ->horizontal(),

            Input::make('kyc.bank_ifsc')
                ->type('text')
                ->required()
                ->title(__('IFSC Code'))
                ->placeholder(__('IFSC Code'))
                ->horizontal(),

            Input::make('kyc.bank_name')
                ->type('text')
                ->required()
                ->title(__('Bank Name'))
                ->placeholder(__('Bank Name'))
                ->horizontal(),

            // Bank Book Image
            Group::make([
                Upload::make('kyc.bank_book_img')
                    ->required()
                    ->title(__('Bank Book Image'))
                    ->acceptedFiles('image/*')
                    ->maxFiles(1),

                // PAN Card Image
                Upload::make('kyc.pan_card_img')
                    ->required()
                    ->title(__('PAN Card Image'))
                    ->acceptedFiles('image/*')
                    ->maxFiles(1),
            ]),

            // Aadhar Card Image
            Group::make([
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
            ]),
        ];
    }
}
