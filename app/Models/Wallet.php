<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Relationship to the owning user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add funds to the wallet.
     */
    public function credit(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    /**
     * Deduct funds from the wallet (if sufficient balance).
     */
    public function debit(float $amount): bool
    {
        if ($this->balance < $amount) {
            return false;
        }

        $this->decrement('balance', $amount);
        return true;
    }

    /**
     * Scope for transactions related to basket purchases.
     */
    public function transaction()
    {
        return $this->hasMany(WalletTransaction::class);
    }
    
}
