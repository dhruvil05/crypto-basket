<?php

namespace App\Orchid\Screens\CryptoBasket;


use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use App\Models\CryptoBasket;
use App\Orchid\Layouts\CryptoBasket\CryptoBasketListLayout;
use Illuminate\Http\Request;

class CryptoBasketListScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $cryptoBaskets = CryptoBasket::with('creator')->latest()->paginate(5);
        return [
            'cryptoBaskets' => $cryptoBaskets,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Crypto Basket';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {   
        return [
            Link::make('Create Basket')
                ->icon('plus')
                ->route('platform.baskets.create')
                ->canSee(auth()->user() && auth()->user()->hasAccess('platform.systems.users')),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            CryptoBasketListLayout::class,
        ];
    }

    public function remove(Request $request): void
    {
        CryptoBasket::findOrFail($request->id)->delete();

        Toast::info(__('User was removed'));
    }

    public function PurchaseBasket(Request $request): void
    {
        Toast::info(__('(Pending) Purchase Basket functionality '));

    }
}
