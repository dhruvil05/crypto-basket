<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class WalletWithdrawal extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'utr_number', // Unique Transaction Reference number
        'screenshot', // Path to the screenshot of the transaction
        'admin_note',
        'wallet_transaction_id', // Reference to the wallet transaction
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
