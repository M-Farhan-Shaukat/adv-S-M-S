<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\FeeVoucher;
use App\Models\RecheckRequest;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentMark;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    private function getStudent()
    {
        return Student::where('user_id', auth()->id())->with('currentEnrollment')->firstOrFail();
    }

    public function dashboard()
    {
        $school = app('school');
        $student = $this->getStudent();

        $pendingFees = FeeVoucher::where('student_id', $student->id)->where('status', '!=', 'paid')->count();
        $recentMarks = StudentMark::with('examSchedule.subject')
            ->where('student_id', $student->id)->where('is_published', true)->latest()->take(5)->get();

        return view('portal.student.dashboard', compact('school', 'student', 'pendingFees', 'recentMarks'));
    }

    public function results()
    {
        $school = app('school');
        $student = $this->getStudent();

        $marks = StudentMark::with('examSchedule.exam', 'examSchedule.subject', 'recheckRequest')
            ->where('student_id', $student->id)
            ->where('is_published', true)
            ->get();

        return view('portal.student.results', compact('school', 'student', 'marks'));
    }

    public function requestRecheck(Request $request)
    {
        $data = $request->validate([
            'student_marks_id' => 'required|exists:student_marks,id',
            'reason'           => 'required|string|min:20',
        ]);

        $student = $this->getStudent();

        // Check if already requested
        if (RecheckRequest::where('student_marks_id', $data['student_marks_id'])->exists()) {
            return redirect()->back()->with('error', 'Recheck already requested');
        }

        RecheckRequest::create([
            'school_id'        => app('school')->id,
            'student_id'       => $student->id,
            'student_marks_id' => $data['student_marks_id'],
            'reason'           => $data['reason'],
        ]);

        return redirect()->back()->with('success', 'Recheck request submitted');
    }

    public function examSchedule()
    {
        $school = app('school');
        $student = $this->getStudent();
        $schedules = collect();

        if ($student->currentEnrollment) {
            $schedules = ExamSchedule::with('exam', 'subject')
                ->where('section_id', $student->currentEnrollment->section_id)
                ->where('exam_date', '>=', today())
                ->orderBy('exam_date')
                ->get();
        }

        return view('portal.student.exam_schedule', compact('school', 'student', 'schedules'));
    }

    public function attendance()
    {
        $school = app('school');
        $student = $this->getStudent();

        $attendances = StudentAttendance::where('student_id', $student->id)
            ->orderByDesc('date')->paginate(30);

        $stats = [
            'present' => StudentAttendance::where('student_id', $student->id)->where('status', 'present')->count(),
            'absent'  => StudentAttendance::where('student_id', $student->id)->where('status', 'absent')->count(),
            'leave'   => StudentAttendance::where('student_id', $student->id)->where('status', 'leave')->count(),
        ];

        return view('portal.student.attendance', compact('school', 'student', 'attendances', 'stats'));
    }

    public function feeVouchers()
    {
        $school = app('school');
        $student = $this->getStudent();

        $vouchers = FeeVoucher::with('items', 'payments')
            ->where('student_id', $student->id)
            ->latest()->paginate(10);

        return view('portal.student.fee_vouchers', compact('school', 'student', 'vouchers'));
    }
}
