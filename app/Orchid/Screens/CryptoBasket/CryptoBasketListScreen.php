<?php

namespace App\Orchid\Screens\CryptoBasket;

use App\Models\BasketPurchase;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\Link;
use App\Models\CryptoBasket;
use App\Models\Wallet;
use App\Models\WalletTransaction;
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
        $cryptoBaskets = CryptoBasket::with(['items', 'returnCycles'])->latest()->paginate(5);

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
        return 'Crypto Baskets';
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
                ->async('asyncBuyBasketModal')
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
        $basketId = $request?->input('basket_id');
        $amount = $request?->input('amount');
        $selectedCycles = array_keys($request->input('return_cycles', []));
        $user = auth()?->user();

        if (!isKycApproved($user->id)) {
            Toast::error('KYC must be approved to purchase a basket.');
            return redirect()->route('platform.profile');
        }
        // Validate the basket ID and amount
        $request->validate([
            'basket_id' => 'required|exists:crypto_baskets,id',
            'amount' => 'required|numeric|min:1',
            'return_cycles' => 'array',
            'return_cycles.*' => 'exists:basket_return_cycles,id',
        ]);

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->balance < $amount) {
            Toast::error('Insufficient wallet balance. Please add funds to your wallet.');
            return redirect()->route('platform.wallet');
        }
        
        $basket = CryptoBasket::with('items')->findOrFail($basketId);

        $selectedReturnCycles = $basket->returnCycles
            ->whereIn('id', $selectedCycles)
            ->values()
            ->map(function ($cycle) {
                return [
                    'id' => $cycle->id,
                    'months' => $cycle->months,
                    'return_percentage' => $cycle->return_percentage,
                ];
            })
            ->toArray();

        $snapshot = [
            'id' => $basket->id,
            'name' => $basket->name,
            'items' => $basket->items->map(function ($item) {
                // Get current price from CoinGecko or your own service
                return [
                    'symbol' => $item->symbol,
                    'percentage' => (float) $item->percentage,
                ];
            })->toArray(),
            'return_cycles' => $selectedReturnCycles,
        ];

        $wallet->balance -= $amount;
        $wallet->save();

        // Transaction logic: Deduct the amount from the user's wallet
        $walletTransaction = new WalletTransaction();
        $walletTransaction->user_id = $user->id;
        $walletTransaction->type = 'purchase';
        $walletTransaction->amount = $amount;
        $walletTransaction->source = 'basket_purchase';
        $walletTransaction->reference_id = $basketId; // Reference to the basket purchased
        $walletTransaction->note = 'Purchased crypto basket: ' . $snapshot['name'];
        $walletTransaction->save();

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

    public function asyncBuyBasketModal(Request $request)
    {
        $basket = CryptoBasket::with('returnCycles')->find($request->get('basket_id'));

        return [
            'basket' => $basket,
            'returnCycles' => $basket?->returnCycles ?? [],
        ];
    }
}
