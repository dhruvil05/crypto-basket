<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'permissions' => [
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
                ]
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'permissions' => [
                    'platform.systems.attachment' => true,
                    'platform.systems.settings' => false,
                    'platform.systems.roles' => false,
                    'platform.systems.users' => false,
                    'platform.owned-baskets' => true,
                    'platform.funds.wallet' => true,
                    'platform.funds.edit' => false,
                    'platform.index' => true,
                ]
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'permissions' => json_encode($role['permissions']),
                ]
            );
        }
    }
}
