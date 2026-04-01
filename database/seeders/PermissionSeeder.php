<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // ================= STUDENT =================
            'create student',
            'view student',
            'update student',
            'delete student',
            'promote student',
            'assign student section',
            'assign class monitor',

            // ================= TEACHER =================
            'create teacher',
            'view teacher',
            'update teacher',
            'delete teacher',
            'assign subject',
            'assign class',

            // ================= STAFF =================
            'create staff',
            'view staff',
            'update staff',
            'delete staff',

            // ================= CLASS & SECTION =================
            'create class',
            'view class',
            'update class',
            'delete class',

            'create section',
            'view section',
            'update section',
            'delete section',

            // ================= SUBJECT =================
            'create subject',
            'view subject',
            'update subject',
            'delete subject',
            'assign subject teacher',

            // ================= ATTENDANCE =================
            'mark student attendance',
            'view student attendance',

            'mark teacher attendance',
            'view teacher attendance',
            'approve attendance',

            // ================= PAYROLL =================
            'generate payroll',
            'view payroll',
            'approve payroll',
            'manage salary structure',

            // ================= FEES =================
            'create fee structure',
            'generate fee voucher',
            'send fee voucher',
            'view fee',
            'collect fee',
            'verify payment',

            // ================= EXAMS =================
            'create exam',
            'view exam',
            'update exam',
            'delete exam',

            'enter marks',
            'publish result',
            'view result',
            'request recheck',
            'approve recheck',

            // ================= REPORTS =================
            'view income report',
            'view expense report',
            'view profit loss report',

            // ================= INVENTORY =================
            'manage inventory',
            'view inventory',

            // ================= EXPENSE =================
            'create expense',
            'view expense',
            'update expense',
            'delete expense',

            // ================= COMMUNICATION =================
            'send sms',
            'send email',
            'send notification',

            // ================= COMPLAINT =================
            'create complaint',
            'view complaint',
            'resolve complaint',

            // ================= MEETING =================
            'schedule meeting',
            'view meeting',

            // ================= SETTINGS =================
            'manage settings',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission,  'guard_name' => 'web']);
        }
    }
}
