<?php

namespace App\Orchid\Screens\CryptoBasket;

use App\Models\BasketPurchase;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use App\Models\CryptoBasket;
use App\Models\Wallet;
use App\Orchid\Layouts\CryptoBasket\BuyBasketLayout;
use App\Orchid\Layouts\CryptoBasket\CryptoBasketListLayout;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Layout;

class CryptoBasketListScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $cryptoBaskets = CryptoBasket::with('items')->latest()->paginate(5);
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
            Layout::modal('buyBasketModal', BuyBasketLayout::class)
                ->title(__('Buy Basket'))
                ->applyButton(__('Buy'))
                ->closeButton(__('Close')),
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

    public function buyBasket(Request $request)
    {
        $basketId = $request->input('basket_id');
        $amount = $request->input('amount');
        $user = auth()->user();

        // Validate the basket ID and amount
        $request->validate([
            'basket_id' => 'required|exists:crypto_baskets,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->balance < $amount) {
            Toast::error('Insufficient wallet balance. Please add funds to your wallet.');
            return redirect()->route('platform.wallet');
        }

        $wallet->balance -= $amount;
        $wallet->save();

        $snapshot = CryptoBasket::with(['items' => function ($q) {
            $q->select('crypto_basket_id', 'symbol', 'percentage'); // adjust fields as needed
        }])->findOrFail($basketId)->toArray();
        
        // Process the investment (store in DB, etc.)
        BasketPurchase::create([
            'user_id' => $user->id,
            'crypto_basket_id' => $basketId,
            'snapshot' => json_encode($snapshot),
            'amount' => $amount,
        ]);

        Toast::success('Investment successful! You have invested ' . $amount . ' in the basket.');
        return redirect()->route('platform.baskets');
    }
}
