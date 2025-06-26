<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\CryptoBasket;

use App\Models\CryptoBasket;
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
        ];
    }
}
