<?php

namespace App\Orchid\Screens\CryptoBasket;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Http;
use App\Models\CryptoBasket;
use App\Models\CryptoBasketItem;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Group;

class CryptoBasketEditScreen extends Screen
{
    public $cryptoBasket;
    protected array $cryptos = [];
    protected array $selected = [];
    protected array $returnCycles = [];
    /**
     * Only admins can access this screen.
     */
    public function permission(): ?iterable
    {
        return [];
    }

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(CryptoBasket $cryptoBasket): iterable
    {
        $this->cryptoBasket = $cryptoBasket ?? new CryptoBasket();

        try {
            $response = Http::get('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc');
            if ($response->ok()) {
                $data = $response->json();
                $this->cryptos = collect($data)
                    ->mapWithKeys(function ($item) {
                        $symbol = strtoupper($item['symbol']);
                        return [$symbol => [
                            'label' => "($symbol) {$item['name']}",
                            'coin_id' => $item['id'],
                            'name' => $item['name'],
                        ]];
                    })
                    ->unique()
                    ->toArray();
            }
        } catch (\Exception $e) {
            $this->cryptos = [];
        }

        // For edit: get existing items
        $this->selected = [
            'cryptocurrencies' => [],
            'percentages' => [],
            'coin_ids' => [],
            'names' => [],
        ];
        if ($cryptoBasket && $cryptoBasket->exists) {
            $items = $cryptoBasket->items()->get();
            foreach ($items as $item) {
                $this->selected['cryptocurrencies'][] = $item->symbol;
                $this->selected['percentages'][] = $item->percentage;
                $this->selected['coin_ids'][] = $item->coin_id;
                $this->selected['names'][] = $item->name;
            }
        }

        if ($cryptoBasket && $cryptoBasket->exists) {
            $this->returnCycles = $cryptoBasket->returnCycles()
                ->orderBy('months')
                ->get()
                ->map(function ($cycle) {
                    return [
                        'months' => $cycle->months,
                        'return_percentage' => $cycle->return_percentage,
                    ];
                })
                ->values()
                ->toArray();
        }

        return [
            'cryptoBasket' => $cryptoBasket,
            'cryptos' => $this->cryptos,
            'selected' => $this->selected,
            'returnCycles' => $this->returnCycles
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->cryptoBasket && $this->cryptoBasket->exists
            ? 'Edit Crypto Basket'
            : 'Create Crypto Basket';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Save')
                ->icon('bs.save')
                ->method('save')
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
            Layout::rows([
                Input::make('basket.name')
                    ->title('Basket Name')
                    ->required()
                    ->value($this->cryptoBasket->name ?? ''),

                Group::make([
                    Input::make('return_cycles[0][months]')
                        ->title('Months')
                        ->readonly()
                        ->value(3),
                    Input::make('return_cycles[0][return_percentage]')
                        ->title('Return %')
                        ->value($this->returnCycles[0]['return_percentage'] ?? ''),

                    Input::make('return_cycles[1][months]')
                        ->title('Months')
                        ->readonly()
                        ->value(6),
                    Input::make('return_cycles[1][return_percentage]')
                        ->title('Return %')
                        ->value($this->returnCycles[1]['return_percentage'] ?? ''),

                    Input::make('return_cycles[2][months]')
                        ->title('Months')
                        ->readonly()
                        ->value(9),
                    Input::make('return_cycles[2][return_percentage]')
                        ->title('Return %')
                        ->value($this->returnCycles[2]['return_percentage'] ?? ''),

                    Input::make('return_cycles[3][months]')
                        ->title('Months')
                        ->readonly()
                        ->value(12),
                    Input::make('return_cycles[3][return_percentage]')
                        ->title('Return %')
                        ->value($this->returnCycles[3]['return_percentage'] ?? ''),
                ]),
            ]),


            Layout::view('vendor.platform.partials.crypto-basket-dynamic', [
                'cryptos' => $this->cryptos ?? [],
                'selected' => $this->selected ?? [],
            ]),
        ];
    }

    /**
     * Handle the save action.
     */
    public function save(CryptoBasket $cryptoBasket, Request $request)
    {
        $data = $request->input(['basket']);
        $return_cycles = $request->input('return_cycles', []);
        // Validate input
        $validated = $request->validate([
            'basket.name' => 'required|string|max:255',
            'basket.cryptocurrencies' => 'required|array|min:1',
            'basket.percentages' => 'required|array|min:1',
            'basket.coin_ids' => 'required|array|min:1',
            'basket.names' => 'required|array|min:1',
            'return_cycles.*.months' => 'required|integer',
            'return_cycles.*.return_percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Calculate total percentage
        $totalPercentage = collect($data['percentages'])->sum();

        // Validate total percentage
        if ($totalPercentage != 100) {
            Toast::error('Total percentage must be 100%.');
            return back()->withInput();
        }

        // Create or update the basket
        $cryptoBasket->name = $data['name'];
        $cryptoBasket->created_by = $cryptoBasket->created_by ?? auth()->id();
        $cryptoBasket->save();

        // Remove old items if editing
        $cryptoBasket->items()->delete();

        // Save each currency/percentage pair
        foreach ($data['cryptocurrencies'] as $idx => $symbol) {
            $percentage = $data['percentages'][$idx] ?? 0;
            $coin_id = $data['coin_ids'][$idx] ?? '';
            $name = $data['names'][$idx] ?? '';
            CryptoBasketItem::create([
                'crypto_basket_id' => $cryptoBasket->id,
                'symbol' => $symbol,
                'coin_id' => $coin_id,
                'name' => $name,
                'percentage' => $percentage,
            ]);
        }

        // Save return cycle
        $cryptoBasket->returnCycles()->delete();

        foreach ($return_cycles as $cycle) {
            if (isset($cycle['months']) && isset($cycle['return_percentage'])) {
                $cryptoBasket->returnCycles()->create([
                    'crypto_basket_id' => $cryptoBasket->id,
                    'months' => $cycle['months'],
                    'return_percentage' => $cycle['return_percentage'],
                ]);
            }
        }

        Toast::info('Crypto Basket saved!');
        return redirect()->route('platform.baskets');
    }
}
