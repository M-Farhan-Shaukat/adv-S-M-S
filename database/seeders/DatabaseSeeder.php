<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        if ($this->command->confirm('Do you wish to refresh migration before seeding, it will clear all old data ?')) {
//            Schema::disableForeignKeyConstraints();
//            $this->command->call('migrate:fresh');
//            Schema::enableForeignKeyConstraints();
//            $this->command->warn("Data cleared, starting from blank database.");
//        }
        $this->call([
            AdminUserSeeder::class,
        RoleSeeder::class,
        PermissionSeeder::class,
        RolePermissionSeeder::class,
            SchoolSetupSeeder::class,
            FeeTypeSeeder::class
            ]);

        $this->command->warn('All done :)');
    }
}
