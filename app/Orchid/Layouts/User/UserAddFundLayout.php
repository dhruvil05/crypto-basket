<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserAddFundLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('funds')
                ->type('number')
                ->min(0)
                ->required()
                ->title(__('Funds'))
                ->placeholder(__('Enter amount to add')),

        ];
    }
}
