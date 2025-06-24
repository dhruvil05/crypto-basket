<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class BasketReturnCycle extends Model
{
    use HasFactory, AsSource;
    
    protected $fillable = ['crypto_basket_id', 'months', 'return_percentage'];

    public function basket()
    {
        return $this->belongsTo(CryptoBasket::class, 'crypto_basket_id');
    }
}
