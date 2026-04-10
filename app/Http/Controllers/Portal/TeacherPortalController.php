<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CourseRemark;
use App\Models\ExamSchedule;
use App\Models\StudentMark;
use App\Models\SubjectAssignment;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\TeacherPayroll;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherPortalController extends Controller
{
    private function getTeacher(): Teacher
    {
        // First try by user_id link
        $teacher = Teacher::where('user_id', auth()->id())->first();

        // Fallback: match by email
        if (!$teacher) {
            $teacher = Teacher::where('email', auth()->user()->email)->first();
        }

        if (!$teacher) {
            abort(404, 'Teacher profile not found. Please contact admin.');
        }

        return $teacher;
    }

    public function dashboard()
    {
        $school  = app('school');
        $teacher = $this->getTeacher();

        // Today's attendance
        $todayAttendance = TeacherAttendance::where('teacher_id', $teacher->id)
            ->where('date', today())->first();

        // Assigned subjects
        $assignments = SubjectAssignment::with('subject', 'schoolClass', 'section')
            ->where('teacher_id', $teacher->id)->get();

        // Latest payroll
        $latestPayroll = TeacherPayroll::where('teacher_id', $teacher->id)
            ->orderByDesc('year')->orderByDesc('month')->first();

        // Pending marks (schedules where marks not entered)
        $pendingMarks = ExamSchedule::whereHas('exam', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->whereDoesntHave('marks')
            ->whereIn('section_id', $assignments->pluck('section_id'))
            ->count();

        // Upcoming exams
        $upcomingExams = ExamSchedule::with('exam', 'subject', 'section')
            ->whereIn('section_id', $assignments->pluck('section_id'))
            ->where('exam_date', '>=', today())
            ->orderBy('exam_date')
            ->take(5)->get();

        return view('portal.teacher.dashboard', compact(
            'school', 'teacher', 'todayAttendance',
            'assignments', 'latestPayroll', 'pendingMarks', 'upcomingExams'
        ));
    }

    public function attendance(Request $request)
    {
        $school  = app('school');
        $teacher = $this->getTeacher();

        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        $attendances = TeacherAttendance::where('teacher_id', $teacher->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $stats = [
            'present'       => $attendances->where('status', 'present')->count(),
            'absent'        => $attendances->where('status', 'absent')->count(),
            'leave'         => $attendances->where('status', 'leave')->count(),
            'total_minutes' => $attendances->sum('working_minutes'),
        ];

        return view('portal.teacher.attendance', compact('school', 'teacher', 'attendances', 'stats', 'month', 'year'));
    }

    public function payroll(Request $request)
    {
        $school  = app('school');
        $teacher = $this->getTeacher();

        $payrolls = TeacherPayroll::where('teacher_id', $teacher->id)
            ->orderByDesc('year')->orderByDesc('month')
            ->paginate(12);

        return view('portal.teacher.payroll', compact('school', 'teacher', 'payrolls'));
    }

    public function subjects()
    {
        $school      = app('school');
        $teacher     = $this->getTeacher();
        $assignments = SubjectAssignment::with('subject', 'schoolClass', 'section', 'session')
            ->where('teacher_id', $teacher->id)->get();

        return view('portal.teacher.subjects', compact('school', 'teacher', 'assignments'));
    }

    public function examSchedule()
    {
        $school      = app('school');
        $teacher     = $this->getTeacher();
        $assignments = SubjectAssignment::where('teacher_id', $teacher->id)->get();

        $schedules = ExamSchedule::with('exam', 'subject', 'section')
            ->whereIn('section_id', $assignments->pluck('section_id'))
            ->where('exam_date', '>=', today())
            ->orderBy('exam_date')
            ->get();

        return view('portal.teacher.exam_schedule', compact('school', 'teacher', 'schedules'));
    }

    public function enterMarks(string $school, ExamSchedule $schedule)
    {
        $school   = app('school');
        $teacher  = $this->getTeacher();

        // Verify this teacher is assigned to this section/subject
        $assigned = SubjectAssignment::where('teacher_id', $teacher->id)
            ->where('section_id', $schedule->section_id)
            ->where('subject_id', $schedule->subject_id)
            ->exists();

        if (!$assigned) {
            return redirect()->back()->with('error', 'You are not assigned to this exam.');
        }

        $students = \App\Models\Student::whereHas('currentEnrollment',
            fn($q) => $q->where('section_id', $schedule->section_id)
        )->get();

        $marks = StudentMark::where('exam_schedule_id', $schedule->id)
            ->get()->keyBy('student_id');

        return view('portal.teacher.enter_marks', compact('school', 'teacher', 'schedule', 'students', 'marks'));
    }

    public function saveMarks(Request $request, string $school, ExamSchedule $schedule)
    {
        $school  = app('school');
        $teacher = $this->getTeacher();

        foreach ($request->input('marks', []) as $studentId => $data) {
            StudentMark::updateOrCreate(
                ['exam_schedule_id' => $schedule->id, 'student_id' => $studentId],
                [
                    'school_id'      => $school->id,
                    'obtained_marks' => $data['marks'] ?? 0,
                    'is_absent'      => !empty($data['absent']),
                    'remarks'        => $data['remarks'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Marks saved successfully');
    }

    public function remarks()
    {
        $school      = app('school');
        $teacher     = $this->getTeacher();
        $assignments = SubjectAssignment::where('teacher_id', $teacher->id)->pluck('subject_id');

        $remarks = CourseRemark::with('student', 'subject', 'user')
            ->whereIn('subject_id', $assignments)
            ->latest()->paginate(15);

        return view('portal.teacher.remarks', compact('school', 'teacher', 'remarks'));
    }

    public function respondRemark(Request $request, string $school, CourseRemark $remark)
    {
        $request->validate(['teacher_response' => 'required|string']);
        $remark->update(['teacher_response' => $request->teacher_response]);
        return redirect()->back()->with('success', 'Response submitted');
    }
}
