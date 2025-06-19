<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Fund;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class AmountDetailLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('transaction.amount')
                ->type('number')
                ->required()
                ->title(__('Amount'))
                ->placeholder(__('Amount')),
        ];
    }
}
