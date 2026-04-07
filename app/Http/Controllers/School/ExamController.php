<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentMark;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        $school = app('school');
        $exams = Exam::with('schoolClass', 'session')->latest()->paginate(15);
        return view('school.exams.index', compact('exams', 'school'));
    }

    public function create()
    {
        $school = app('school');
        $classes = SchoolClass::all();
        return view('school.exams.create', compact('classes', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string',
            'type'            => 'required|in:quiz,mid_term,final_term,annual,other',
            'school_class_id' => 'required|exists:school_classes,id',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'description'     => 'nullable|string',
        ]);

        $school = app('school');
        $data['school_id'] = $school->id;
        $data['school_session_id'] = $school->activeSession->id;

        Exam::create($data);

        return redirect()->route('school.exams.index', $school->slug)->with('success', 'Exam created');
    }

    public function schedules(Exam $exam)
    {
        $subjects = Subject::all();
        $sections = Section::whereHas('schoolClass', fn($q) => $q->where('id', $exam->school_class_id))->get();
        $schedules = $exam->schedules()->with('subject', 'section')->get();
        return view('school.exams.schedules', compact('exam', 'subjects', 'sections', 'schedules'));
    }

    public function addSchedule(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'section_id'    => 'required|exists:sections,id',
            'exam_date'     => 'required|date',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'total_marks'   => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:1',
            'room'          => 'nullable|string',
        ]);

        $data['exam_id']   = $exam->id;
        $data['school_id'] = app('school')->id;

        ExamSchedule::create($data);

        return redirect()->back()->with('success', 'Schedule added');
    }

    public function enterMarks(ExamSchedule $schedule)
    {
        $students = Student::whereHas('currentEnrollment', fn($q) => $q->where('section_id', $schedule->section_id))->get();
        $marks = StudentMark::where('exam_schedule_id', $schedule->id)->get()->keyBy('student_id');
        return view('school.exams.marks', compact('schedule', 'students', 'marks'));
    }

    public function saveMarks(Request $request, ExamSchedule $schedule)
    {
        $request->validate([
            'marks'          => 'required|array',
            'marks.*.marks'  => 'nullable|numeric|min:0',
            'marks.*.absent' => 'nullable|boolean',
        ]);

        $school = app('school');

        foreach ($request->marks as $studentId => $data) {
            StudentMark::updateOrCreate(
                ['exam_schedule_id' => $schedule->id, 'student_id' => $studentId],
                [
                    'school_id'      => $school->id,
                    'obtained_marks' => $data['marks'] ?? 0,
                    'is_absent'      => isset($data['absent']) ? true : false,
                    'remarks'        => $data['remarks'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Marks saved');
    }

    public function publishMarks(ExamSchedule $schedule)
    {
        StudentMark::where('exam_schedule_id', $schedule->id)->update(['is_published' => true]);
        return redirect()->back()->with('success', 'Results published');
    }
}
