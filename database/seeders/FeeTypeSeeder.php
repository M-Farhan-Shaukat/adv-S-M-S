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
            'Transport Fee'
        ];

        foreach ($types as $type) {
            FeeType::create([
                'name' => $type,
                'school_id'=>1
            ]);
        }
    }
}
