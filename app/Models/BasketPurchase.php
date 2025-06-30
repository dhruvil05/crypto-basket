<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Screen\AsSource;

class BasketPurchase extends Model
{
    use HasFactory, AsSource, SoftDeletes;

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

    protected $dates = ['deleted_at'];

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

    public function withdrawals()
    {
        return $this->hasOne(BasketWithdrawal::class);
    }
}
