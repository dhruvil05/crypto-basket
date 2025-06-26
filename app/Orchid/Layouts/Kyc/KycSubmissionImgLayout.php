<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Kyc;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Layouts\Rows;

class KycSubmissionImgLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Picture::make('kyc_data.bank_book_img')
                ->title('Bank Book Image')
                ->readonly(),

            Picture::make('kyc_data.pan_card_img')
                ->title('PAN Card Image')
                ->readonly(),

            Picture::make('kyc_data.aadhar_card_img')
                ->title('Aadhar Card Image')
                ->readonly(),

            Picture::make('kyc_data.passport_img')
                ->title('Passport Image')
                ->readonly(),
        ];
    }
}
