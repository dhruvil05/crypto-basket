<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\CryptoBasket;

use App\Models\CryptoBasket;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
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

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

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

            TD::make(__('Purchase'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(CryptoBasket $cryptoBasket) => Button::make(__('Buy'))
                    ->type(Color::SUCCESS)
                    ->icon('bs.purchase')
                    ->method('PurchaseBasket', [
                        'id' => $cryptoBasket->id,
                    ]),),
        ];
    }
}
