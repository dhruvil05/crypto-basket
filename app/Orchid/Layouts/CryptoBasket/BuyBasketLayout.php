<?php

namespace App\Orchid\Layouts\CryptoBasket;

use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Facades\Layout;

class BuyBasketLayout extends Rows
{
    public function fields(): array
    {
        $returnCycles = $this->query->get('returnCycles', []);
        $checkboxes = [];

        foreach ($returnCycles as $i => $cycle) {
            $percentage = number_format($cycle['return_percentage'], 0) ?? 0;

            $placeholder = "{$cycle['months']} months- ($percentage%)";

            $checkboxes[] = CheckBox::make("return_cycles[{$cycle['id']}]")
                ->value($cycle['id'])
                ->title($i === 0 ? 'Return cycle (In months)' : '')
                ->placeholder($placeholder)
                ->checked(false);
        }

        $fields = [
            Input::make('amount')
                ->type('number')
                ->min(1)
                ->title('Investment Amount')
                ->required()
                ->help('Enter the amount you want to invest in this basket.'),

            Group::make($checkboxes)
                ->autoWidth()
                ->alignEnd(),
        ];



        return $fields;
    }
}
