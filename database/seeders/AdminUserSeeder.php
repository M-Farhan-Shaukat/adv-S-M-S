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
        // Ensure roles exist first
        $adminRole     = Role::firstOrCreate(['name' => 'admin',     'guard_name' => 'web']);
        $principalRole = Role::firstOrCreate(['name' => 'principal', 'guard_name' => 'web']);
        $teacherRole   = Role::firstOrCreate(['name' => 'teacher',   'guard_name' => 'web']);
        $staffRole     = Role::firstOrCreate(['name' => 'staff',     'guard_name' => 'web']);
        $studentRole   = Role::firstOrCreate(['name' => 'student',   'guard_name' => 'web']);
        $parentRole    = Role::firstOrCreate(['name' => 'parent',    'guard_name' => 'web']);

        // Super Admin (no school)
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('Temp123!'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]
        );
        $admin->syncRoles([$adminRole]);

        // Test user (student role)
        $user = User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name'              => 'Test User',
                'password'          => Hash::make('Temp123!'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]
        );
        $user->syncRoles([$studentRole]);
    }
}
