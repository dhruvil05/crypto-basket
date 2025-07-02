<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Fund;

use App\Models\WalletTransaction;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Illuminate\Support\Facades\Route;

class FundLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'transactions';

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

            TD::make('status', __('Status'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(function ($model) {
                    $status = ucfirst($model->status); // Assuming status is: 'approved', 'pending', 'rejected'

                    $colorClass = match ($model->status) {
                        'approved' => 'badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2',
                        'completed' => 'badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2',
                        'pending'  => 'badge bg-warning bg-opacity-25 text-warning rounded-pill px-3 py-2',
                        'rejected' => 'badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2',
                        default    => 'badge bg-secondary bg-opacity-25 text-secondary rounded-pill px-3 py-2',
                    };

                    return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold {$colorClass}\">{$status}</span>";
                })->align(TD::ALIGN_CENTER),

            TD::make('source', __('Source'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(WalletTransaction $transaction) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.funds.edit', $transaction->id) // Define this route
                            ->icon('bs.pencil'),
                    ])),
        ];
    }
}
