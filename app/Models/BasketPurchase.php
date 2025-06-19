<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class BasketPurchase extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'user_id',
        'crypto_basket_id',
        'amount',
        'snapshot',
        'status',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'amount'   => 'decimal:8',
    ];

    // 🔁 Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cryptoBasket()
    {
        return $this->belongsTo(CryptoBasket::class);
    }

    // public function sale()
    // {
    //     return $this->hasOne(BasketSale::class);
    // }

    // 📌 Optional Helper: Is Sold?
    public function isSold(): bool
    {
        return $this->sale()->exists();
    }
}
