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
                ->render(fn(WalletTransaction $tx) => '₹' . number_format((float)$tx->amount, 2) . ' ($' . number_format(inr_to_usd((float)$tx->amount), 2) . ')')
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make('source', __('Source'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('note', __('Note'))
                ->width('200px')
                ->render(fn(WalletTransaction $tx) => e($tx->note)),

            TD::make('status', __('Status'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(function ($tx) {
                    if (is_null($tx->status)) {
                        return '';
                    }
                    $status = ucfirst($tx->status); // Assuming status is: 'approved', 'pending', 'rejected'

                    $colorClass = match ($tx->status) {
                        'approved' => 'badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2',
                        'completed' => 'badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2',
                        'pending'  => 'badge bg-warning bg-opacity-25 text-warning rounded-pill px-3 py-2',
                        'rejected' => 'badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2',
                        default    => 'badge bg-secondary bg-opacity-25 text-secondary rounded-pill px-3 py-2',
                    };

                    return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold {$colorClass}\">{$status}</span>";
                })->align(TD::ALIGN_CENTER),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),
        ];
    }
}
