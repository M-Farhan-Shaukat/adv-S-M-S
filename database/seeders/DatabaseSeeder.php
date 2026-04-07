<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,           // 1. Roles pehle
            PermissionSeeder::class,     // 2. Permissions
            RolePermissionSeeder::class, // 3. Role-Permission mapping
            AdminUserSeeder::class,      // 4. Admin user (roles ke baad)
            SchoolSetupSeeder::class,    // 5. School + principal + teacher
            FeeTypeSeeder::class,        // 6. Fee types
        ]);

        $this->command->info('All seeders completed successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['admin',     'admin@gmail.com',           'Temp123!'],
                ['principal', 'principal@abcschool.com',   'Temp123!'],
                ['teacher',   'teacher@abcschool.com',     'Temp123!'],
            ]
        );
    }
}
