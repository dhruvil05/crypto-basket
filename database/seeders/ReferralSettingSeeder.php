<?php

namespace Database\Seeders;

use App\Models\ReferralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReferralSetting::firstOrCreate([], [
            'referrer_discount' => 10.00,
            'referee_reward' => 10.00,
            'is_active' => true,
        ]);
    }
}
