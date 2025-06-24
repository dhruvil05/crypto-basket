<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Kyc;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class KycStatusLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Select::make('kyc_data.status')
                ->title('Status')
                ->options([
                    'pending'   => 'Pending',
                    'approved'  => 'Approved',
                    'rejected'  => 'Rejected',
                ])
                ->required()
                ->id('status-select'),
        ];
    }
}
