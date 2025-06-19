<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Fund;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Layouts\Rows;

class TransactionDetailsLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('transaction.utr')
                ->title('UTR Number')
                ->readonly(),

            Picture::make('transaction.screenshot')
                ->title('Transaction Screenshot')
                ->readonly(),
        ];
    }
}
