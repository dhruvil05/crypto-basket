<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Orchid\Platform\Models\Role;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_users')->insert([
            'user_id' => 1, // Assuming the admin user has ID 1
            'role_id' => Role::where('slug', 'admin')->first()->id,
        ]);
    }
}
