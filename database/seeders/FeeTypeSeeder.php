<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeeType;

class FeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Admission Fee',
            'Tuition Fee',
            'Exam Fee',
            'Transport Fee',
            'Library Fee',
            'Sports Fee',
        ];

        // Get all active schools
        $schools = \App\Models\School::all();

        foreach ($schools as $school) {
            foreach ($types as $type) {
                FeeType::firstOrCreate([
                    'name'      => $type,
                    'school_id' => $school->id,
                ]);
            }
        }
    }
}
