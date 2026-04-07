<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\CourseRemark;
use App\Models\ExamSchedule;
use App\Models\FeeVoucher;
use App\Models\MeetingSchedule;
use App\Models\Student;
use App\Models\StudentMark;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    private function getChildren()
    {
        $school = app('school');
        return Student::where('user_id', auth()->id())->get();
    }

    public function dashboard()
    {
        $school = app('school');
        $children = $this->getChildren();

        $pendingFees = FeeVoucher::whereIn('student_id', $children->pluck('id'))
            ->where('status', '!=', 'paid')->count();

        $meetings = MeetingSchedule::where('type', 'parent_teacher')
            ->where('meeting_date', '>=', now())
            ->where('status', 'scheduled')
            ->take(3)->get();

        return view('portal.parent.dashboard', compact('school', 'children', 'pendingFees', 'meetings'));
    }

    public function results(Request $request)
    {
        $school = app('school');
        $children = $this->getChildren();
        $studentId = $request->student_id ?? $children->first()?->id;

        $marks = StudentMark::with('examSchedule.exam', 'examSchedule.subject')
            ->where('student_id', $studentId)
            ->where('is_published', true)
            ->get();

        return view('portal.parent.results', compact('school', 'children', 'marks', 'studentId'));
    }

    public function feeVouchers(Request $request)
    {
        $school = app('school');
        $children = $this->getChildren();
        $studentId = $request->student_id ?? $children->first()?->id;

        $vouchers = FeeVoucher::with('items', 'payments')
            ->where('student_id', $studentId)
            ->latest()->paginate(10);

        return view('portal.parent.fee_vouchers', compact('school', 'children', 'vouchers', 'studentId'));
    }

    public function examSchedule(Request $request)
    {
        $school = app('school');
        $children = $this->getChildren();
        $studentId = $request->student_id ?? $children->first()?->id;

        $student = Student::with('currentEnrollment')->find($studentId);
        $schedules = collect();

        if ($student?->currentEnrollment) {
            $schedules = ExamSchedule::with('exam', 'subject')
                ->where('section_id', $student->currentEnrollment->section_id)
                ->where('exam_date', '>=', today())
                ->orderBy('exam_date')
                ->get();
        }

        return view('portal.parent.exam_schedule', compact('school', 'children', 'schedules', 'studentId'));
    }

    public function complaints()
    {
        $school = app('school');
        $complaints = Complaint::where('user_id', auth()->id())->latest()->paginate(10);
        return view('portal.parent.complaints', compact('school', 'complaints'));
    }

    public function submitComplaint(Request $request)
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:200',
            'description' => 'required|string',
            'type'        => 'required|in:academic,behavioral,facility,staff,other',
        ]);

        Complaint::create([
            'school_id'   => app('school')->id,
            'user_id'     => auth()->id(),
            'subject'     => $data['subject'],
            'description' => $data['description'],
            'type'        => $data['type'],
        ]);

        return redirect()->back()->with('success', 'Complaint submitted');
    }

    public function meetings()
    {
        $school = app('school');
        $meetings = MeetingSchedule::whereHas('participants', fn($q) => $q->where('user_id', auth()->id()))
            ->orWhere('type', 'parent_teacher')
            ->latest('meeting_date')->paginate(10);

        return view('portal.parent.meetings', compact('school', 'meetings'));
    }

    public function submitRemark(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'student_id' => 'required|exists:students,id',
            'remark'     => 'required|string',
            'type'       => 'required|in:positive,negative,suggestion,query',
        ]);

        CourseRemark::create([
            'school_id'  => app('school')->id,
            'user_id'    => auth()->id(),
            'subject_id' => $data['subject_id'],
            'student_id' => $data['student_id'],
            'remark'     => $data['remark'],
            'type'       => $data['type'],
        ]);

        return redirect()->back()->with('success', 'Remark submitted');
    }
}
