<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\WalletTransaction;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ActivityHistoryLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'activityHistory';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('type', __('Type'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('amount', __('Amount'))
                ->render(fn(WalletTransaction $tx) => number_format((float)$tx->amount, 2))
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make('source', __('Source'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('note', __('Note'))
                ->width('200px')
                ->render(fn(WalletTransaction $tx) => e($tx->note)),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),
        ];
    }
}
