<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Fund;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class TransactionStatusLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Select::make('transaction.status')
                ->title('Status')
                ->options([
                    'pending'   => 'Pending',
                    'approved'  => 'Approved',
                    'rejected'  => 'Rejected',
                ])
                ->required()
                ->id('status-select'),

                TextArea::make('transaction.admin_comment')
                ->title('Rejection Comment')
                ->placeholder('Enter rejection reason...')
                ->rows(4)
                ->id('admin-comment') // We will toggle this field using JS

        ];
    }
}
