<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SchoolSession;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolStudentController extends Controller
{
    public function index(Request $request)
    {
        $school = app('school');
        $students = Student::with('currentEnrollment.class', 'currentEnrollment.section')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->class_id, fn($q) => $q->whereHas('currentEnrollment', fn($e) => $e->where('school_class_id', $request->class_id)))
            ->when($request->status, fn($q) => $q->where('is_active', $request->status === 'active'))
            ->paginate(20);

        $classes = SchoolClass::all();
        return view('school.students.index', compact('students', 'classes', 'school'));
    }

    public function create()
    {
        $school = app('school');
        $classes  = SchoolClass::with('sections')->get();
        $sessions = SchoolSession::all();
        return view('school.students.create', compact('classes', 'sessions', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'email'             => 'nullable|email',
            'phone'             => 'nullable|string',
            'dob'               => 'nullable|date',
            'gender'            => 'nullable|in:male,female,other',
            'roll_number'       => 'nullable|string',
            'address'           => 'nullable|string',
            'guardian_name'     => 'nullable|string',
            'guardian_phone'    => 'nullable|string',
            'school_session_id' => 'required|exists:school_sessions,id',
            'school_class_id'   => 'required|exists:school_classes,id',
            'section_id'        => 'required|exists:sections,id',
        ]);

        $school = app('school');

        return DB::transaction(function () use ($data, $school) {
            $student = Student::create([
                'school_id'      => $school->id,
                'name'           => $data['name'],
                'email'          => $data['email'] ?? null,
                'phone'          => $data['phone'] ?? null,
                'dob'            => $data['dob'] ?? null,
                'gender'         => $data['gender'] ?? null,
                'roll_number'    => $data['roll_number'] ?? null,
                'address'        => $data['address'] ?? null,
                'guardian_name'  => $data['guardian_name'] ?? null,
                'guardian_phone' => $data['guardian_phone'] ?? null,
            ]);

            StudentEnrollment::create([
                'student_id'        => $student->id,
                'school_id'         => $school->id,
                'school_session_id' => $data['school_session_id'],
                'school_class_id'   => $data['school_class_id'],
                'section_id'        => $data['section_id'],
                'is_current'        => true,
            ]);

            return redirect()->route('school.students.index', $school->slug)->with('success', 'Student admitted');
        });
    }

    public function show(Student $student)
    {
        $school = app('school');
        $student->load('enrollments.class', 'enrollments.section', 'enrollments.session', 'currentEnrollment');
        return view('school.students.show', compact('student', 'school'));
    }

    public function edit(Student $student)
    {
        $school = app('school');
        $classes  = SchoolClass::with('sections')->get();
        $sessions = SchoolSession::all();
        return view('school.students.edit', compact('student', 'classes', 'sessions', 'school'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'name'           => 'required|string',
            'phone'          => 'nullable|string',
            'dob'            => 'nullable|date',
            'gender'         => 'nullable|in:male,female,other',
            'address'        => 'nullable|string',
            'guardian_name'  => 'nullable|string',
            'guardian_phone' => 'nullable|string',
        ]);

        $student->update($data);
        return redirect()->route('school.students.index', app('school')->slug)->with('success', 'Student updated');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->back()->with('success', 'Student deleted');
    }

    public function toggleStatus(Student $student)
    {
        $student->update(['is_active' => !$student->is_active]);
        return redirect()->back()->with('success', 'Status updated');
    }

    public function promote(Request $request, Student $student)
    {
        $data = $request->validate([
            'school_session_id' => 'required|exists:school_sessions,id',
            'school_class_id'   => 'required|exists:school_classes,id',
            'section_id'        => 'required|exists:sections,id',
        ]);

        $school = app('school');

        DB::transaction(function () use ($student, $data, $school) {
            StudentEnrollment::where('student_id', $student->id)->update(['is_current' => false]);
            StudentEnrollment::create([
                'student_id'        => $student->id,
                'school_id'         => $school->id,
                'school_session_id' => $data['school_session_id'],
                'school_class_id'   => $data['school_class_id'],
                'section_id'        => $data['section_id'],
                'is_current'        => true,
            ]);
        });

        return redirect()->back()->with('success', 'Student promoted');
    }

    public function setClassMonitor(Request $request, Student $student)
    {
        $enrollment = $student->currentEnrollment;
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Student not enrolled');
        }

        // Remove existing monitor in same section
        StudentEnrollment::where('section_id', $enrollment->section_id)
            ->where('is_current', true)
            ->update(['is_class_monitor' => false]);

        $enrollment->update(['is_class_monitor' => true]);

        return redirect()->back()->with('success', "{$student->name} set as class monitor");
    }
}
