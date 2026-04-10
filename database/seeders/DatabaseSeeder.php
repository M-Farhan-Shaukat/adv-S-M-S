<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,           // 1. Roles
            PermissionSeeder::class,     // 2. Permissions
            RolePermissionSeeder::class, // 3. Role-Permission mapping
            AdminUserSeeder::class,      // 4. Super Admin
            SchoolSetupSeeder::class,    // 5. School + all users
            FeeTypeSeeder::class,        // 6. Fee types
        ]);

        $this->command->info("\n" . str_repeat('=', 55));
        $this->command->info('  ALL CREDENTIALS');
        $this->command->info(str_repeat('=', 55));

        $this->command->table(
            ['Role', 'Email', 'Password', 'Login URL'],
            [
                ['Super Admin',  'admin@sms.com',              'Admin@123',     '/admin/login'],
                ['Principal',    'principal@abcschool.com',    'Principal@123', '/admin/login'],
                ['Teacher',      'sara@abcschool.com',         'Teacher@123',   '/login'],
                ['Teacher',      'ali@abcschool.com',          'Teacher@123',   '/login'],
                ['Staff',        'staff@abcschool.com',        'Staff@123',     '/login'],
                ['Student',      'hamza@student.com',          'Student@123',   '/login'],
                ['Student',      'fatima@student.com',         'Student@123',   '/login'],
                ['Parent',       'imran@parent.com',           'Parent@123',    '/login'],
                ['Parent',       'sana@parent.com',            'Parent@123',    '/login'],
            ]
        );

        $this->command->info("\n  School URL: /abc-school/dashboard");
        $this->command->info(str_repeat('=', 55) . "\n");
    }
}
