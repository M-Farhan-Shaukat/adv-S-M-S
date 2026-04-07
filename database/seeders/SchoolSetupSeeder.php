<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SchoolSetupSeeder extends Seeder
{
    public function run(): void
    {
        // Create School
        $school = School::firstOrCreate(
            ['slug' => 'abc-school'],
            [
                'name'            => 'ABC School',
                'email'           => 'info@abcschool.com',
                'phone'           => '0300-1234567',
                'address'         => 'Main Road, Lahore',
                'is_active'       => true,
                'fee_voucher_day' => 1,
            ]
        );

        app()->instance('school', $school);

        // Sessions
        $sessions = [
            ['name' => '2024-2025', 'start_date' => '2024-04-01', 'end_date' => '2025-03-31', 'status' => 'active'],
            ['name' => '2025-2026', 'start_date' => '2025-04-01', 'end_date' => '2026-03-31', 'status' => 'active'],
        ];

        foreach ($sessions as $sessionData) {
            $session = $school->sessions()->firstOrCreate(
                ['name' => $sessionData['name']],
                $sessionData
            );

            $classes = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5'];

            foreach ($classes as $className) {
                $class = $session->classes()->firstOrCreate(
                    ['name' => $className, 'school_id' => $school->id]
                );

                foreach (['A', 'B', 'C'] as $sectionName) {
                    $class->sections()->firstOrCreate(
                        ['name' => $sectionName, 'school_id' => $school->id]
                    );
                }
            }
        }

        // Create Principal for this school
        $principalRole = Role::where('name', 'principal')->first();
        if ($principalRole) {
            $principal = User::updateOrCreate(
                ['email' => 'principal@abcschool.com'],
                [
                    'name'              => 'ABC Principal',
                    'password'          => Hash::make('Temp123!'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                    'school_id'         => $school->id,
                ]
            );
            $principal->syncRoles([$principalRole]);
        }

        // Create a test teacher
        $teacherRole = Role::where('name', 'teacher')->first();
        if ($teacherRole) {
            $teacher = User::updateOrCreate(
                ['email' => 'teacher@abcschool.com'],
                [
                    'name'              => 'Test Teacher',
                    'password'          => Hash::make('Temp123!'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                    'school_id'         => $school->id,
                ]
            );
            $teacher->syncRoles([$teacherRole]);
        }

        $this->command->info("School '{$school->name}' setup complete.");
    }
}
