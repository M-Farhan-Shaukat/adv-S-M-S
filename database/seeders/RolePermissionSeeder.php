<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $principal = Role::where('name', 'principal')->first();
        $teacher   = Role::where('name', 'teacher')->first();
        $staff     = Role::where('name', 'staff')->first();
        $student   = Role::where('name', 'student')->first();
        $parent    = Role::where('name', 'parent')->first();

        // ================= PRINCIPAL =================
        // Full Access
        $principal->givePermissionTo(Permission::all());

        // ================= TEACHER =================
        $teacher->givePermissionTo([
            'view student',
            'mark student attendance',
            'view student attendance',
            'enter marks',
            'view exam',
            'view result',
            'view teacher attendance',
        ]);

        // ================= STAFF =================
        $staff->givePermissionTo([
            'view student',
            'view teacher',
            'mark teacher attendance',
            'view teacher attendance',
            'collect fee',
            'view fee',
            'create expense',
            'view expense',
        ]);

        // ================= STUDENT =================
        $student->givePermissionTo([
            'view result',
            'request recheck',
            'view student attendance',
        ]);

        // ================= PARENT =================
        $parent->givePermissionTo([
            'view result',
            'view fee',
            'create complaint',
            'view meeting',
        ]);
    }
}
