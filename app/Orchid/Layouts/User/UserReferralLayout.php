<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserReferralLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('user.referral_code')
                ->type('text')
                ->readonly()
                ->title(__('Referral Code'))
                ->rawAttributes([
                    'data-referral-code' => $this->query->get('user.referral_code') ?? '',
                ]),

        ];
    }
}
