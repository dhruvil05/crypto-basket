<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoBasketItem extends Model
{
    use HasFactory;

    protected $fillable = ['crypto_basket_id', 'coin_id', 'symbol', 'name', 'percentage'];

    public function basket()
    {
        return $this->belongsTo(CryptoBasket::class, 'crypto_basket_id');
    }
}
