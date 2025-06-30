<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class BasketWithdrawal extends Model
{
    use HasFactory, AsSource;

    protected $fillable = ['user_id', 'basket_purchase_id', 'amount', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function basketPurchase()
    {
        return $this->belongsTo(BasketPurchase::class);
    }
}
