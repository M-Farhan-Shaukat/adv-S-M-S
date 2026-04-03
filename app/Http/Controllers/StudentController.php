<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{

    public function index(Request $request)
    {
        $query = \App\Models\Student::with([
            'currentEnrollment.class',
            'currentEnrollment.section'
        ]);

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->paginate(10);
    }

    public function show($id)
    {
        $student = \App\Models\Student::with([
            'currentEnrollment.class',
            'currentEnrollment.section',
            'currentEnrollment.session',
            'enrollments.class',
            'enrollments.section',
            'enrollments.session',
        ])->findOrFail($id);

        return response()->json($student);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',

            'school_id' => 'required|exists:schools,id',
            'school_session_id' => 'required|exists:school_sessions,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        return DB::transaction(function () use ($data) {

            // ✅ Create Student
            $student = Student::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'school_id' => $data['school_id'],
            ]);

            // ❗ IMPORTANT: old enrollments inactive (safety)
            StudentEnrollment::where('student_id', $student->id)
                ->update(['is_current' => false]);

            // ✅ Create Enrollment
            StudentEnrollment::create([
                'student_id' => $student->id,
                'school_id' => $data['school_id'],
                'school_session_id' => $data['school_session_id'],
                'school_class_id' => $data['school_class_id'],
                'section_id' => $data['section_id'],
                'is_current' => true,
            ]);

            return response()->json([
                'message' => 'Student admitted successfully',
                'data' => $student->load('currentEnrollment.class', 'currentEnrollment.section')
            ]);
        });
    }

    public function promote(Request $request, $studentId)
    {
        $data = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'school_session_id' => 'required|exists:school_sessions,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        return DB::transaction(function () use ($studentId, $data) {

            // 🔹 Get current enrollment
            $current = StudentEnrollment::where('student_id', $studentId)
                ->where('is_current', true)
                ->first();

            if (!$current) {
                return response()->json([
                    'message' => 'No current enrollment found'
                ], 404);
            }

            // 🔻 Old ko inactive karo
            $current->update([
                'is_current' => false
            ]);

            // 🔹 New enrollment (promotion)
            $newEnrollment = StudentEnrollment::create([
                'student_id' => $studentId,
                'school_id' => $data['school_id'],
                'school_session_id' => $data['school_session_id'],
                'school_class_id' => $data['school_class_id'],
                'section_id' => $data['section_id'],
                'is_current' => true,
            ]);

            return response()->json([
                'message' => 'Student promoted successfully',
                'data' => $newEnrollment->load('class', 'section', 'session')
            ]);
        });
    }

    public function bulkPromote(Request $request)
    {
        $data = $request->validate([
            'from_class_id' => 'required|exists:school_classes,id',
            'to_class_id' => 'required|exists:school_classes,id',
            'school_session_id' => 'required|exists:school_sessions,id',
        ]);

        return DB::transaction(function () use ($data) {

            // 🔹 get all current students of class
            $enrollments = \App\Models\StudentEnrollment::where('school_class_id', $data['from_class_id'])
                ->where('is_current', true)
                ->get();

            foreach ($enrollments as $enrollment) {

                // 🔻 old inactive
                $enrollment->update([
                    'is_current' => false
                ]);

                // 🔹 new enrollment
                \App\Models\StudentEnrollment::create([
                    'student_id' => $enrollment->student_id,
                    'school_session_id' => $data['school_session_id'],
                    'school_class_id' => $data['to_class_id'],
                    'section_id' => $enrollment->section_id, // same section
                    'is_current' => true,
                ]);
            }

            return response()->json([
                'message' => 'Bulk promotion done',
                'total_students' => $enrollments->count()
            ]);
        });
    }
}
