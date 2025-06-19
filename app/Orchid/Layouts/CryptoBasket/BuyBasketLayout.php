<?php

namespace App\Orchid\Layouts\CryptoBasket;

use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class BuyBasketLayout extends Rows
{
    public function fields(): array
    {
        return [
            Input::make('amount')
                ->type('number')
                ->min(1)
                ->title('Investment Amount')
                ->required()
                ->help('Enter the amount you want to invest in this basket.'),
        ];
    }
}