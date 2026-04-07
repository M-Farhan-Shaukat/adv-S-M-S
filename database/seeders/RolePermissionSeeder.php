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
        $admin     = Role::where('name', 'admin')->first();
        $principal = Role::where('name', 'principal')->first();
        $teacher   = Role::where('name', 'teacher')->first();
        $staff     = Role::where('name', 'staff')->first();
        $student   = Role::where('name', 'student')->first();
        $parent    = Role::where('name', 'parent')->first();

        // ================= ADMIN =================
        if ($admin) $admin->givePermissionTo(Permission::all());

        // ================= PRINCIPAL =================
        if ($principal) $principal->givePermissionTo(Permission::all());

        // ================= TEACHER =================
        if ($teacher) $teacher->givePermissionTo([
            'view student',
            'mark student attendance',
            'view student attendance',
            'enter marks',
            'view exam',
            'view result',
            'view teacher attendance',
        ]);

        // ================= STAFF =================
        if ($staff) $staff->givePermissionTo([
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
        if ($student) $student->givePermissionTo([
            'view result',
            'request recheck',
            'view student attendance',
        ]);

        // ================= PARENT =================
        if ($parent) $parent->givePermissionTo([
            'view result',
            'view fee',
            'create complaint',
            'view meeting',
        ]);
    }
}
