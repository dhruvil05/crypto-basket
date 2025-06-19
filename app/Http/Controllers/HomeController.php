<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $coinData = Cache::remember('coin_metadata_all', 86400, function () {
                $allCoins = [];
                for ($page = 1; $page <= 4; $page++) {
                    $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
                        'vs_currency' => 'usd',
                        'order' => 'market_cap_desc',
                        'per_page' => 250,
                        'page' => $page,
                        'sparkline' => false,
                    ]);

                    if ($response->failed()) {
                        Log::error("CoinGecko API failed on page $page at " . now());
                        break;
                    }

                    $allCoins = array_merge($allCoins, $response->json());
                }

                return $allCoins;
            });

            if (empty($coinData)) {
                Log::warning("CoinGecko returned empty data at " . now());
            }

            $coinMap = collect($coinData)->mapWithKeys(function ($coin) {
                return [
                    strtoupper($coin['symbol']) => [
                        'name' => $coin['name'],
                        'logo' => $coin['image'],
                        'market_cap' => $coin['market_cap'],
                    ],
                ];
            });

        } catch (\Exception $e) {
            Log::error('Exception fetching CoinGecko data: ' . $e->getMessage());
            $coinMap = collect();
        }

        return view('vendor.platform.home', ['coinMap' => $coinMap]);
    }
}