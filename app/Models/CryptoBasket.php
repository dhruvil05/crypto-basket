<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Screen\AsSource;

class CryptoBasket extends Model
{
    use HasFactory, AsSource, SoftDeletes;

    protected $fillable = ['id', 'name', 'created_by'];

    protected $table = 'crypto_baskets';

    protected $dates = ['deleted_at'];

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function items()
    {
        return $this->hasMany(CryptoBasketItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchases()
    {
        return $this->hasMany(BasketPurchase::class);
    }
}
