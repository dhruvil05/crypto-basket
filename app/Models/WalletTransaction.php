<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class WalletTransaction extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'user_id',
        'type',             // credit | debit
        'amount',
        'utr',
        'screenshot',
        'status',           // pending | approved | rejected
        'admin_comment',
        'reviewed_at',
        'reviewed_by',
        'source',           // manual | basket_purchase
        'reference_id',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    /**
     * The user who owns this wallet transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin who reviewed this transaction (if applicable).
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope for approved transactions.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for manual user-submitted transactions.
     */
    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    
}
