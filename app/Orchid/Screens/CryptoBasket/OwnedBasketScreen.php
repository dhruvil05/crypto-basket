<?php

namespace App\Orchid\Screens\CryptoBasket;

use App\Models\BasketPurchase;
use App\Models\BasketWithdrawal; // Add this at the top with your other use statements
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Orchid\Layouts\CryptoBasket\OwnedBasketLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

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
            ->where('is_withdrawn', false) // Only show baskets that have not been withdrawn
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

    public function withdraw(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:basket_purchases,id',
        ]); 

        $basketPurchase = BasketPurchase::findOrFail($request->input('id'));
        $user = auth()->user();

        // Prevent double withdrawal
        if ($basketPurchase->is_withdrawn) {
            Toast::error('This basket has already been withdrawn.');
            return redirect()->route('platform.owned-baskets');
        }

        // Get invested amount
        $investedAmount = (float) $basketPurchase->amount;

        // Get snapshot and return cycle data
        $snapshot = json_decode($basketPurchase->snapshot, true);

        // Assume only one return cycle is selected per purchase
        $returnCycle = $snapshot['return_cycles'][0] ?? null;

        if (!$returnCycle || !isset($returnCycle['return_percentage'])) {
            Toast::error('Return cycle data not found.');
            return redirect()->back();
        }

        $returnPercentage = (float) $returnCycle['return_percentage'];

        // Calculate return amount and total withdrawal amount
        $returnAmount = $investedAmount * ($returnPercentage / 100);
        $withdrawalAmount = $investedAmount + $returnAmount;

        // Mark as withdrawn
        $basketPurchase->is_withdrawn = true;
        $basketPurchase->withdrawn_at = now();
        $basketPurchase->save();

        // Add entry to basket_withdrawals table
        BasketWithdrawal::create([
            'user_id' => $user->id,
            'basket_purchase_id' => $basketPurchase->id,
            'amount' => $withdrawalAmount,
            'status' => 'approved',
        ]);

        // Add wallet transaction
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $withdrawalAmount,
            'type' => 'cash out',
            'note' => 'Cash out from basket: ' . $basketPurchase->cryptoBasket->name,
            'status' => 'completed',
            'source' => 'basket_withdrawal',
            'reference_id' => $basketPurchase->id,

        ]);

        // Add amount to wallet funds
        $wallet->increment('balance', $withdrawalAmount);

        Toast::success("Withdrawal amount: ₹" . number_format($withdrawalAmount, 2) . "($". number_format(inr_to_usd($withdrawalAmount), 2) . ")" . " (Invested: ₹" . number_format($investedAmount, 2) . "$". number_format(inr_to_usd($investedAmount), 2).", Return: ₹" . number_format($returnAmount, 2) . "$". number_format(inr_to_usd($returnAmount), 2).")");
        return redirect()->route('platform.owned-baskets');
    }
}
