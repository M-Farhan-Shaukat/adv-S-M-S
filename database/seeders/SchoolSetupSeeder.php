<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Create School
        $school = School::create([
            'name' => 'ABC School',
            'slug' => 'abc-school'
        ]);
        app()->instance('school', $school); // 👈 important
        // 🔹 Create Sessions
        $sessions = [
            [
                'name' => '2024-2025',
                'start_date' => now()->subYear(),
                'end_date' => now(),
                'status' => 'active',
            ],
            [
                'name' => '2025-2026',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => 'active',
            ]
        ];

        foreach ($sessions as $sessionData) {

            $session = $school->sessions()->create($sessionData);

            // 🔹 Classes
            $classes = [
                'Grade 1',
                'Grade 2',
                'Grade 3',
                'Grade 4',
                'Grade 5'
            ];

            foreach ($classes as $className) {

                $class = $session->classes()->create([
                    'name' => $className,
                    'school_id' => $school->id
                ]);

                // 🔹 Sections
                foreach (['A', 'B', 'C'] as $sectionName) {
                    $class->sections()->create([
                        'name' => $sectionName,
                        'school_id' => $school->id
                    ]);
                }
            }
        }
    }
}
