<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\SchoolSession;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Services\CredentialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolStudentController extends Controller
{
    public function index(Request $request)
    {
        $school   = app('school');
        $students = Student::with('currentEnrollment.class', 'currentEnrollment.section')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->class_id, fn($q) => $q->whereHas('currentEnrollment',
                fn($e) => $e->where('school_class_id', $request->class_id)))
            ->when($request->status !== null && $request->status !== '',
                fn($q) => $q->where('is_active', $request->status === 'active'))
            ->paginate(20);

        $classes = SchoolClass::all();
        return view('school.students.index', compact('students', 'classes', 'school'));
    }

    public function create()
    {
        $school   = app('school');
        $classes  = SchoolClass::with('sections')->get();
        $sessions = SchoolSession::all();
        return view('school.students.create', compact('classes', 'sessions', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'email'             => 'nullable|email|unique:users,email',
            'phone'             => 'nullable|string',
            'dob'               => 'nullable|date',
            'gender'            => 'nullable|in:male,female,other',
            'roll_number'       => 'nullable|string',
            'address'           => 'nullable|string',
            // Parent info
            'guardian_name'     => 'required|string|max:100',
            'guardian_phone'    => 'nullable|string',
            'guardian_email'    => 'nullable|email|unique:users,email',
            // Enrollment
            'school_session_id' => 'required|exists:school_sessions,id',
            'school_class_id'   => 'required|exists:school_classes,id',
            'section_id'        => 'required|exists:sections,id',
        ]);

        $school = app('school');

        return DB::transaction(function () use ($data, $school) {
            $messages = [];

            // ===== Create Student User (if email given) =====
            $studentUser     = null;
            $studentPassword = null;

            if (!empty($data['email'])) {
                $studentPassword = CredentialService::generatePassword();
                $studentUser     = User::create([
                    'name'              => $data['name'],
                    'email'             => $data['email'],
                    'password'          => Hash::make($studentPassword),
                    'school_id'         => $school->id,
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]);
                $studentUser->assignRole('student');
            }

            // ===== Create Student Record =====
            $student = Student::create([
                'school_id'      => $school->id,
                'user_id'        => $studentUser?->id,
                'name'           => $data['name'],
                'email'          => $data['email'] ?? null,
                'phone'          => $data['phone'] ?? null,
                'dob'            => $data['dob'] ?? null,
                'gender'         => $data['gender'] ?? null,
                'roll_number'    => $data['roll_number'] ?? null,
                'address'        => $data['address'] ?? null,
                'guardian_name'  => $data['guardian_name'],
                'guardian_phone' => $data['guardian_phone'] ?? null,
            ]);

            // ===== Enrollment =====
            StudentEnrollment::create([
                'student_id'        => $student->id,
                'school_id'         => $school->id,
                'school_session_id' => $data['school_session_id'],
                'school_class_id'   => $data['school_class_id'],
                'section_id'        => $data['section_id'],
                'is_current'        => true,
            ]);

            // ===== Create Parent User (if guardian email given) =====
            $parentPassword = null;
            $parentUserId   = null;

            if (!empty($data['guardian_email'])) {
                $parentPassword = CredentialService::generatePassword();

                $parentUser = User::create([
                    'name'              => $data['guardian_name'],
                    'email'             => $data['guardian_email'],
                    'password'          => Hash::make($parentPassword),
                    'school_id'         => $school->id,
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]);
                $parentUser->assignRole('parent');
                $parentUserId = $parentUser->id;
            }

            // Link parent_user_id to student
            $student->update(['parent_user_id' => $parentUserId]);

            // ===== Send Student Credentials =====
            if ($studentUser && $studentPassword) {
                CredentialService::sendCredentials(
                    email:      $studentUser->email,
                    name:       $studentUser->name,
                    password:   $studentPassword,
                    role:       'Student',
                    schoolName: $school->name,
                    loginUrl:   url('/login'),
                    portalNote: "Your student portal: " . url("/{$school->slug}/student/dashboard")
                );
                $messages[] = "Student credentials sent to {$studentUser->email}";
            }

            // ===== Send Parent Credentials =====
            if (!empty($data['guardian_email']) && $parentPassword) {
                CredentialService::sendCredentials(
                    email:      $data['guardian_email'],
                    name:       $data['guardian_name'],
                    password:   $parentPassword,
                    role:       'Parent',
                    schoolName: $school->name,
                    loginUrl:   url('/login'),
                    portalNote: "Your child {$student->name} has been admitted. Access parent portal: " . url("/{$school->slug}/parent/dashboard")
                );
                $messages[] = "Parent credentials sent to {$data['guardian_email']}";
            }

            $successMsg = "Student '{$student->name}' admitted successfully.";
            if ($messages) {
                $successMsg .= ' ' . implode('. ', $messages) . '.';
            }

            return redirect()->route('school.students.index', $school->slug)
                ->with('success', $successMsg);
        });
    }

    public function show(string $school, Student $student)
    {
        $school  = app('school');
        $student->load('enrollments.class', 'enrollments.section', 'enrollments.session', 'currentEnrollment');
        return view('school.students.show', compact('student', 'school'));
    }

    public function edit(string $school, Student $student)
    {
        $school   = app('school');
        $classes  = SchoolClass::with('sections')->get();
        $sessions = SchoolSession::all();
        return view('school.students.edit', compact('student', 'classes', 'sessions', 'school'));
    }

    public function update(Request $request, string $school, Student $student)
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
        return redirect()->route('school.students.index', app('school')->slug)
            ->with('success', 'Student updated');
    }

    public function destroy(string $school, Student $student)
    {
        $student->delete();
        return redirect()->back()->with('success', 'Student deleted');
    }

    public function toggleStatus(string $school, Student $student)
    {
        $student->update(['is_active' => !$student->is_active]);
        return redirect()->back()->with('success', 'Status updated');
    }

    public function promote(Request $request, string $school, Student $student)
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

    public function setClassMonitor(Request $request, string $school, Student $student)
    {
        $enrollment = $student->currentEnrollment;
        if (!$enrollment) {
            return redirect()->back()->with('error', 'Student not enrolled');
        }

        StudentEnrollment::where('section_id', $enrollment->section_id)
            ->where('is_current', true)
            ->update(['is_class_monitor' => false]);

        $enrollment->update(['is_class_monitor' => true]);

        return redirect()->back()->with('success', "{$student->name} set as class monitor");
    }
}
