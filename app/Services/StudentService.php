<?php
namespace App\Services;

use App\Models\Student;
use App\Models\StudentEnrollment;

class StudentService
{
    public function create(array $data)
    {
        // 1. student create
        $student = Student::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
        ]);

        // 2. enroll student
        StudentEnrollment::create([
            'student_id' => $student->id,
            'school_school_session_id' => $data['school_session_id'],
            'school_class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
        ]);

        return $student;
    }

    public function all()
    {
        return Student::with('enrollments')->get();
    }
}
