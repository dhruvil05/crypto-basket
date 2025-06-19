<?php

namespace App\Orchid\Screens\CryptoBasket;

use App\Models\BasketPurchase;
use App\Orchid\Layouts\CryptoBasket\OwnedBasketLayout;
use Orchid\Screen\Screen;

class OwnedBasketScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $user = auth()->user();
        $ownedBaskets = BasketPurchase::with('cryptoBasket')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(5);

        return [
            'ownedBaskets' => $ownedBaskets,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Owned Baskets';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            OwnedBasketLayout::class,
        ];
    }
}
