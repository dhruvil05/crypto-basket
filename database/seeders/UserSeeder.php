<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'platform.systems.roles' => true,
            'platform.systems.users' => true,
            'platform.systems.settings' => true,
            'platform.index' => true,
            'platform.systems.attachment' => true,
            'platform.systems.logs' => true,
            'platform.systems.tools' => true,
            'platform.systems.pending.requests' => true,
            'platform.owned-baskets' => true,
            'platform.funds.wallet' => true,
            'platform.funds.edit' => true,
            'platform.user.kyc.requests' => true,
            'platform.funds.direct.add' => true,
            'platform.fund.withdraw_requests' => true,

        ];

        User::updateOrInsert([
            'id' => 1, // Ensure the admin user has a specific ID
            'name' => 'Admin',
            'email' => 'admin@yopmail.com',
            'password' => bcrypt('admin@123'), // Ensure you use a secure password
            'email_verified_at' => now(),
            'referral_code' => '123ADMIN', // Example referral code
            'permissions' => json_encode($permissions),
        ]);
    }
}
