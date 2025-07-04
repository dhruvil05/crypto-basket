<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Fund;

use App\Models\WalletWithdrawal;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class WithdrawRequestsLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'WalletWithdrawal';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('User'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn($WalletWithdrawal) => $WalletWithdrawal->user->name ?? __('Unknown')),

            TD::make('amount', __('Amount'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn($WalletWithdrawal) => '₹' . number_format((float)$WalletWithdrawal->amount, 2) . ' ($' . number_format(inr_to_usd((float)$WalletWithdrawal->amount), 2) . ')'),

            TD::make('status', __('Status'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(function ($WalletWithdrawal) {
                    $status = ucfirst($WalletWithdrawal->status); // Assuming status is: 'approved', 'pending', 'rejected'

                    $colorClass = match ($WalletWithdrawal->status) {
                        'approved' => 'badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2',
                        'completed' => 'badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2',
                        'pending'  => 'badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2',
                        'rejected' => 'badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2',
                        default    => 'badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2',
                    };

                    return "<span class=\"px-3 py-1 rounded-full text-xs font-semibold {$colorClass}\">{$status}</span>";
                })->align(TD::ALIGN_CENTER),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(WalletWithdrawal $walletWithdrawal) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Transfer'))
                            ->icon('bs.cash')
                            ->route('platform.fund.withdraw_requests.transfer', $walletWithdrawal),
                    ])),

        ];
    }
}
