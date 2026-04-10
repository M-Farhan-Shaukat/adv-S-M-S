<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SchoolSetupSeeder extends Seeder
{
    public function run(): void
    {
        // ===== SCHOOL =====
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

        // ===== SESSION =====
        $session = $school->sessions()->firstOrCreate(
            ['name' => '2025-2026'],
            ['start_date' => '2025-04-01', 'end_date' => '2026-03-31', 'status' => 'active']
        );

        // ===== CLASSES & SECTIONS =====
        $classData = [];
        foreach (['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5'] as $className) {
            $class = $session->classes()->firstOrCreate(
                ['name' => $className, 'school_id' => $school->id]
            );
            foreach (['A', 'B'] as $sec) {
                $class->sections()->firstOrCreate(['name' => $sec, 'school_id' => $school->id]);
            }
            $classData[$className] = $class;
        }

        // ===== SUBJECTS =====
        $subjectNames = ['Mathematics', 'English', 'Science', 'Urdu', 'Islamiat'];
        $subjects = [];
        foreach ($subjectNames as $sName) {
            $subjects[$sName] = \App\Models\Subject::firstOrCreate(
                ['name' => $sName, 'school_id' => $school->id]
            );
        }

        // ===== FEE TYPES =====
        $tuitionType = \App\Models\FeeType::firstOrCreate(
            ['name' => 'Tuition Fee', 'school_id' => $school->id]
        );
        $examType = \App\Models\FeeType::firstOrCreate(
            ['name' => 'Exam Fee', 'school_id' => $school->id]
        );

        // Fee structures per class
        foreach ($classData as $cName => $class) {
            $amount = match($cName) {
                'Grade 1', 'Grade 2' => 2000,
                'Grade 3', 'Grade 4' => 2500,
                default              => 3000,
            };
            \App\Models\FeeStructure::firstOrCreate(
                ['school_class_id' => $class->id, 'fee_type_id' => $tuitionType->id, 'school_id' => $school->id],
                ['name' => 'Monthly Tuition', 'amount' => $amount]
            );
        }

        // ===== PRINCIPAL =====
        $principalRole = Role::where('name', 'principal')->first();
        $principal = User::updateOrCreate(
            ['email' => 'principal@abcschool.com'],
            [
                'name'              => 'Mr. Ahmed (Principal)',
                'password'          => Hash::make('Principal@123'),
                'email_verified_at' => now(),
                'is_active'         => true,
                'school_id'         => $school->id,
            ]
        );
        $principal->syncRoles([$principalRole]);

        // ===== TEACHERS =====
        $teacherRole = Role::where('name', 'teacher')->first();
        $teachersData = [
            ['name' => 'Ms. Sara (Teacher)',  'email' => 'sara@abcschool.com',  'salary' => 25000],
            ['name' => 'Mr. Ali (Teacher)',   'email' => 'ali@abcschool.com',   'salary' => 28000],
        ];

        $teacherModels = [];
        foreach ($teachersData as $td) {
            $tUser = User::updateOrCreate(
                ['email' => $td['email']],
                [
                    'name'              => $td['name'],
                    'password'          => Hash::make('Teacher@123'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                    'school_id'         => $school->id,
                ]
            );
            $tUser->syncRoles([$teacherRole]);

            $teacherModels[] = Teacher::updateOrCreate(
                ['email' => $td['email'], 'school_id' => $school->id],
                [
                    'user_id'                => $tUser->id,
                    'name'                   => $td['name'],
                    'salary'                 => $td['salary'],
                    'daily_required_minutes' => 480,
                    'is_active'              => true,
                ]
            );
        }

        // ===== STAFF =====
        $staffRole = Role::where('name', 'staff')->first();
        $staffUser = User::updateOrCreate(
            ['email' => 'staff@abcschool.com'],
            [
                'name'              => 'Mr. Usman (Staff)',
                'password'          => Hash::make('Staff@123'),
                'email_verified_at' => now(),
                'is_active'         => true,
                'school_id'         => $school->id,
            ]
        );
        $staffUser->syncRoles([$staffRole]);

        Staff::updateOrCreate(
            ['email' => 'staff@abcschool.com', 'school_id' => $school->id],
            [
                'user_id'     => $staffUser->id,
                'name'        => 'Mr. Usman (Staff)',
                'designation' => 'Accountant',
                'salary'      => 18000,
                'is_active'   => true,
            ]
        );

        // ===== STUDENTS + PARENTS =====
        $studentRole = Role::where('name', 'student')->first();
        $parentRole  = Role::where('name', 'parent')->first();

        $studentsData = [
            [
                'name'           => 'Hamza Khan',
                'email'          => 'hamza@student.com',
                'roll_number'    => 'G1-001',
                'guardian_name'  => 'Mr. Imran Khan',
                'guardian_email' => 'imran@parent.com',
                'guardian_phone' => '0311-1111111',
                'class'          => 'Grade 1',
                'section'        => 'A',
            ],
            [
                'name'           => 'Fatima Malik',
                'email'          => 'fatima@student.com',
                'roll_number'    => 'G2-001',
                'guardian_name'  => 'Mrs. Sana Malik',
                'guardian_email' => 'sana@parent.com',
                'guardian_phone' => '0322-2222222',
                'class'          => 'Grade 2',
                'section'        => 'A',
            ],
        ];

        foreach ($studentsData as $sd) {
            // Student user
            $sUser = User::updateOrCreate(
                ['email' => $sd['email']],
                [
                    'name'              => $sd['name'],
                    'password'          => Hash::make('Student@123'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                    'school_id'         => $school->id,
                ]
            );
            $sUser->syncRoles([$studentRole]);

            // Student record
            $student = Student::updateOrCreate(
                ['email' => $sd['email'], 'school_id' => $school->id],
                [
                    'user_id'        => $sUser->id,
                    'name'           => $sd['name'],
                    'roll_number'    => $sd['roll_number'],
                    'guardian_name'  => $sd['guardian_name'],
                    'guardian_phone' => $sd['guardian_phone'],
                    'is_active'      => true,
                ]
            );

            // Enrollment
            $class   = $classData[$sd['class']];
            $section = $class->sections()->where('name', $sd['section'])->first();

            if ($section) {
                StudentEnrollment::updateOrCreate(
                    ['student_id' => $student->id, 'school_session_id' => $session->id],
                    [
                        'school_id'       => $school->id,
                        'school_class_id' => $class->id,
                        'section_id'      => $section->id,
                        'is_current'      => true,
                    ]
                );
            }

            // Parent user
            $pUser = User::updateOrCreate(
                ['email' => $sd['guardian_email']],
                [
                    'name'              => $sd['guardian_name'],
                    'password'          => Hash::make('Parent@123'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                    'school_id'         => $school->id,
                ]
            );
            $pUser->syncRoles([$parentRole]);

            // Link parent to student
            $student->update(['parent_user_id' => $pUser->id]);
        }

        // ===== SUBJECT ASSIGNMENTS =====
        $grade1 = $classData['Grade 1'];
        $grade1SectionA = $grade1->sections()->where('name', 'A')->first();
        if ($grade1SectionA && isset($teacherModels[0])) {
            \App\Models\SubjectAssignment::updateOrCreate(
                [
                    'school_id'         => $school->id,
                    'school_session_id' => $session->id,
                    'school_class_id'   => $grade1->id,
                    'section_id'        => $grade1SectionA->id,
                    'subject_id'        => $subjects['Mathematics']->id,
                ],
                ['teacher_id' => $teacherModels[0]->id]
            );
            \App\Models\SubjectAssignment::updateOrCreate(
                [
                    'school_id'         => $school->id,
                    'school_session_id' => $session->id,
                    'school_class_id'   => $grade1->id,
                    'section_id'        => $grade1SectionA->id,
                    'subject_id'        => $subjects['English']->id,
                ],
                ['teacher_id' => $teacherModels[1]->id ?? $teacherModels[0]->id]
            );
        }

        $this->command->info("\n✅ School '{$school->name}' setup complete!\n");
    }
}
