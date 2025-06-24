<?php

namespace App\Orchid\Layouts\CryptoBasket;

use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class BuyBasketLayout extends Rows
{
    public function fields(): array
    {
        $returnCycles = $this->query->get('returnCycles', []);

        $fields = [
            Input::make('amount')
                ->type('number')
                ->min(1)
                ->title('Investment Amount')
                ->required()
                ->help('Enter the amount you want to invest in this basket.')
        ];

        foreach ($returnCycles as $cycle) {
            $fields[] = CheckBox::make("return_cycles[{$cycle['id']}]")
                ->value($cycle['id'])
                ->title("{$cycle['months']} Months ({$cycle['return_percentage']}%)")
                ->checked(false);
        }

        return $fields;
    }
}
