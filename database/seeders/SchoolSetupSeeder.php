<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\Session;

class SchoolSetupSeeder extends Seeder
{
    public function run(): void
    {
        // Create School
        $school = School::create([
            'name' => 'ABC School',
            'slug' => 'abc-school'
        ]);

        // Create Session
        $session = $school->sessions()->create([
            'name' => '2025-2026',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'status' => 'active'
        ]);

        // Classes List
        $classes = ['Grade 1', 'Grade 2', 'Grade 3'];

        foreach ($classes as $className) {
            $class = $session->classes()->create([
                'name' => $className
            ]);

            // Sections for each class
            foreach (['A', 'B'] as $sectionName) {
                $class->sections()->create([
                    'name' => $sectionName
                ]);
            }
        }
    }
}
