<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;


function inrToUsd(float $inrAmount): float
{
    try {
        $response = Http::get('https://api.exchangerate.host/convert', [
            'from' => 'INR',
            'to' => 'USD',
            'amount' => $inrAmount,
        ]);

        if ($response->ok() && isset($response['result'])) {
            return round($response['result'], 2);
        }
    } catch (\Exception $e) {
        // Log or handle error
    }

    // Fallback value or error handling
    return 0.0;
}
