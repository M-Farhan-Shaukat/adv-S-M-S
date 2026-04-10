<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin (no school - system owner)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::updateOrCreate(
            ['email' => 'admin@sms.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('Admin@123'),
                'email_verified_at' => now(),
                'is_active'         => true,
                'school_id'         => null,
            ]
        );
        $admin->syncRoles([$adminRole]);
    }
}
