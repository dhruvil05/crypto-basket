<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@yopmail.com',
            'password' => bcrypt('admin@123'), // Ensure you use a secure password
            'email_verified_at' => now(),
            'permissions' => [
                'platform.systems.roles' => true,
                'platform.systems.users' => true,
                'platform.systems.settings' => true,
                'platform.systems.index' => true,
                'platform.systems.attachment' => true,
                'platform.systems.logs' => true,
                'platform.systems.tools' => true,
            ],
        ]);

        $this->call(ReferralSettingSeeder::class);

    }
}
