<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // =================== TEACHER ATTENDANCE ===================

    public function teacherIndex(Request $request)
    {
        $school      = app('school');
        $date        = $request->date ?? today()->toDateString();
        $teachers    = Teacher::where('is_active', true)->get();
        $attendances = TeacherAttendance::where('date', $date)->get()->keyBy('teacher_id');

        return view('school.attendance.teacher', compact('teachers', 'attendances', 'date', 'school'));
    }

    public function teacherCheckIn(Request $request)
    {
        $request->validate(['teacher_id' => 'required|exists:teachers,id']);

        $school  = app('school');
        $session = $school->activeSession ?? $school->currentSession;

        if (!$session) {
            return redirect()->back()->with('error', 'No active session found.');
        }

        $attendance = TeacherAttendance::firstOrCreate(
            ['teacher_id' => $request->teacher_id, 'date' => today()],
            [
                'school_id'         => $school->id,
                'school_session_id' => $session->id,
                'status'            => 'present',
            ]
        );

        if (!$attendance->check_in) {
            $attendance->update(['check_in' => now()->toTimeString()]);
        }

        return redirect()->back()->with('success', 'Check-in recorded');
    }

    public function teacherCheckOut(Request $request)
    {
        $request->validate(['teacher_id' => 'required|exists:teachers,id']);

        $attendance = TeacherAttendance::where('teacher_id', $request->teacher_id)
            ->where('date', today())
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No check-in found for today.');
        }

        if (!$attendance->check_in) {
            return redirect()->back()->with('error', 'Teacher has not checked in yet.');
        }

        $checkIn  = Carbon::parse($attendance->check_in);
        $checkOut = now();
        $minutes  = $checkIn->diffInMinutes($checkOut);

        $attendance->update([
            'check_out'       => $checkOut->toTimeString(),
            'working_minutes' => $minutes,
        ]);

        return redirect()->back()->with('success', 'Check-out recorded');
    }

    public function teacherMarkAbsent(Request $request)
    {
        $request->validate(['teacher_id' => 'required|exists:teachers,id']);

        $school  = app('school');
        $session = $school->activeSession ?? $school->currentSession;

        if (!$session) {
            return redirect()->back()->with('error', 'No active session found.');
        }

        TeacherAttendance::updateOrCreate(
            ['teacher_id' => $request->teacher_id, 'date' => today()],
            [
                'school_id'         => $school->id,
                'school_session_id' => $session->id,
                'status'            => 'absent',
                'working_minutes'   => 0,
            ]
        );

        return redirect()->back()->with('success', 'Marked absent');
    }

    // =================== STUDENT ATTENDANCE ===================

    public function studentIndex(Request $request)
    {
        $school      = app('school');
        $sections    = Section::with('schoolClass')->get();
        $date        = $request->date ?? today()->toDateString();
        $sectionId   = $request->section_id;
        $students    = collect();
        $attendances = collect();

        if ($sectionId) {
            $students    = Student::whereHas('currentEnrollment', fn($q) => $q->where('section_id', $sectionId))->get();
            $attendances = StudentAttendance::where('date', $date)
                ->where('section_id', $sectionId)
                ->get()->keyBy('student_id');
        }

        return view('school.attendance.student', compact('sections', 'students', 'attendances', 'date', 'sectionId', 'school'));
    }

    public function studentMark(Request $request)
    {
        $request->validate([
            'section_id'   => 'required|exists:sections,id',
            'date'         => 'required|date',
            'attendance'   => 'required|array',
            'attendance.*' => 'in:present,absent,leave,late',
        ]);

        $school  = app('school');
        $session = $school->activeSession ?? $school->currentSession;

        if (!$session) {
            return redirect()->back()->with('error', 'No active session found.');
        }

        foreach ($request->attendance as $studentId => $status) {
            StudentAttendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $request->date],
                [
                    'school_id'         => $school->id,
                    'school_session_id' => $session->id,
                    'section_id'        => $request->section_id,
                    'status'            => $status,
                ]
            );
        }

        return redirect()->back()->with('success', 'Attendance saved');
    }
}
