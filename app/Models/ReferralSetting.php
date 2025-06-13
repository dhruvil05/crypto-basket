<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{

    protected $fillable = [
        'referrer_discount',
        'referee_reward',
        'is_active',
    ];
    
    protected $casts = [
        'referrer_discount' => 'decimal:2',
        'referee_reward' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    /**
     * Get the referral settings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSettings()
    {
        return self::firstOrCreate([
            'id' => 1, // Assuming there's only one set of settings
        ], [
            'referrer_discount' => 10.00,
            'referee_reward' => 10.00,
            'is_active' => true,
        ]);
    }
    /**
     * Update the referral settings.
     *
     * @param array $data
     * @return bool
     */
    public function updateSettings(array $data)
    {
        $this->fill($data);
        return $this->save();
    }
    /**
     * Check if the referral program is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }
}
