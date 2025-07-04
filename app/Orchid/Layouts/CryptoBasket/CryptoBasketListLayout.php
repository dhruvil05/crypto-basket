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

class CryptoBasketListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'cryptoBaskets';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('currencies', __('Currencies (%)'))
                ->render(function (CryptoBasket $cryptoBaskets) {
                    $summary = $cryptoBaskets->items->map(function ($item) {
                        return $item->symbol . ' (' . $item->percentage . '%)';
                    })->implode(', ');

                    return "<small>{$summary}</small>";
                })
                ->sort()
                ->cantHide()
                ->filter(Input::make()),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            // TD::make(__('Purchase'))
            //     ->align(TD::ALIGN_CENTER)
            //     ->width('100px')
            //     ->render(function (CryptoBasket $cryptoBasket) {
            //         return ModalToggle::make('Buy')
            //             ->modal('buyBasketModal')
            //             ->method('buyBasket')
            //             ->modalTitle('Invest in ' . $cryptoBasket->name)
            //             ->icon('bs.cart-plus')
            //             ->class('badge bg-success bg-opacity-10 text-success rounded border border-success px-3 py-2 btn fw-bold shadow')
            //             ->asyncParameters([
            //                 'basket_id' => $cryptoBasket->id,
            //             ]);
            //     }),

            TD::make(__('Purchase'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(function (CryptoBasket $cryptoBasket) {
                    $buttonId = 'buy-btn-' . $cryptoBasket->id;
                    
                    $styles = "<style>
                        #{$buttonId} {
                            gap: 6px !important;
                            display: inline-flex !important;
                            align-items: center !important;
                        }
                        #{$buttonId}:hover {
                            background-color: rgba(25, 135, 84, 0.2) !important;
                        }
                    </style>";
                    
                    return $styles . ModalToggle::make('Buy')
                        ->modal('buyBasketModal')
                        ->method('buyBasket')
                        ->modalTitle('Invest in ' . $cryptoBasket->name)
                        ->icon('bs.cart-plus')
                        ->class('badge bg-success bg-opacity-10 text-success rounded border border-success px-3 py-2 btn fw-bold')
                        ->id($buttonId)
                        ->asyncParameters([
                            'basket_id' => $cryptoBasket->id,
                        ]);
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->canSee(auth()->user() && auth()->user()->hasAccess('platform.systems.users'))
                ->render(fn(CryptoBasket $cryptoBasket) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->route('platform.baskets.edit', [$cryptoBasket])
                            ->icon('bs.pencil'),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the basket is deleted, all of its resources and data will be permanently deleted. Before deleting your basket, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $cryptoBasket->id,
                            ]),
                    ])),


        ];
    }
}
