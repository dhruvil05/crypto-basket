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
                ->render(fn($WalletWithdrawal) => $WalletWithdrawal->status == 'completed' ? __('Completed') : __('Pending')),

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
