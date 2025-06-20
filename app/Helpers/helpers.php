<?php


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('inr_to_usd')) {
    function inr_to_usd(float $inr): float
    {
        try {
            $response = Http::get('https://open.er-api.com/v6/latest/INR');

            if ($response->ok() && isset($response['rates']['USD'])) {
                $rate = $response['rates']['USD'];
                return round($inr * $rate, 2);
            }
        } catch (\Throwable $e) {
            \Log::error('Conversion failed: ' . $e->getMessage());
        }

        return 0.0;
    }
}
