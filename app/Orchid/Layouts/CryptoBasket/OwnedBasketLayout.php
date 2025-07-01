<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\CryptoBasket;

use App\Models\CryptoBasket;
use Carbon\Carbon;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Color;

class OwnedBasketLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'ownedBaskets';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('basket.name', 'Basket Name')
                ->render(fn($ownedBaskets) => $ownedBaskets->cryptoBasket->name ?? '-'),

            TD::make('amount', 'Invested Amount')
                ->render(fn($ownedBaskets) => '₹' . number_format((float)$ownedBaskets->amount, 2) . ' ($' . number_format(inr_to_usd((float)$ownedBaskets->amount), 2) . ')')
                ->sort(),

            TD::make('created_at', 'Invested On')
                ->render(fn($ownedBaskets) => $ownedBaskets->created_at->toDayDateTimeString()),

            TD::make('snapshot', 'Snapshot')
                ->render(function ($ownedBaskets) {
                    $snapshot = json_decode($ownedBaskets->snapshot, true);
                    if (!$snapshot || empty($snapshot['items'])) return '-';
                    return collect($snapshot['items'])->map(function ($item) {
                        return $item['symbol'] . ' (' . $item['percentage'] . '%)';
                    })->implode(', ');
                }),

            TD::make('return_cycle', 'Return Cycle')
                ->render(function ($ownedBaskets) {
                    $snapshot = json_decode($ownedBaskets->snapshot, true);
                    if (!$snapshot || empty($snapshot['return_cycles'])) return '-';
                    return collect($snapshot['return_cycles'])->map(function ($cycle) {
                        return $cycle['months'] . ' months (' . $cycle['return_percentage'] . '%)';
                    })->implode(', ');
                }),

            TD::make('withdraw', 'Withdrawal')
                ->render(function ($ownedBaskets) {
                    $canWithdraw = false;

                    // Decode snapshot JSON from the basket (or use $ownedBaskets->snapshot if available)
                    $snapshot = json_decode($ownedBaskets->snapshot, true);

                    if ($snapshot && isset($snapshot['return_cycles'][0]['months'])) {
                        $returnMonths = (int) $snapshot['return_cycles'][0]['months'];

                        $createdAt = Carbon::parse($ownedBaskets->created_at);
                        $unlockDate = $createdAt->addMonths($returnMonths);

                        $canWithdraw = now()->greaterThanOrEqualTo($unlockDate);
                    }

                    return Button::make('Withdraw')
                        ->icon('bs.currency-exchange')
                        ->class('badge bg-primary bg-opacity-10 text-primary rounded border border-primary px-3 py-2 btn fw-bold shadow')
                        ->size('sm')
                        ->method('withdraw', ['id' => $ownedBaskets->id])
                        ->confirm('Are you sure you want to withdraw from this basket?')
                        ->canSee($canWithdraw); // Disable if not yet eligible
                }),

        ];
    }
}
