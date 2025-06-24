<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class KycSubmission extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'user_id',
        'pan_card_img',
        'aadhar_card_img',
        'passport_img',
        'bank_book_img',
        'bank_account_holder',
        'bank_account_number',
        'bank_ifsc',
        'bank_name',
        'status',
    ];

    /**
     * The user that owns the KYC submission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the status label for the KYC submission.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }
}
