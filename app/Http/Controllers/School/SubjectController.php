<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectAssignment;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $school   = app('school');
        $subjects = Subject::withCount('assignments')->paginate(20);
        return view('school.subjects.index', compact('subjects', 'school'));
    }

    public function store(Request $request)
    {
        $data              = $request->validate(['name' => 'required|string|max:100']);
        $data['school_id'] = app('school')->id;
        Subject::create($data);
        return redirect()->back()->with('success', 'Subject created');
    }

    public function destroy(string $school, Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Subject deleted');
    }

    public function assignments()
    {
        $school      = app('school');
        $assignments = SubjectAssignment::with('subject', 'teacher', 'schoolClass', 'section')->paginate(20);
        $subjects    = Subject::all();
        $teachers    = Teacher::where('is_active', true)->get();
        $classes     = SchoolClass::with('sections')->get();
        $session     = $school->activeSession ?? $school->currentSession;
        return view('school.subjects.assignments', compact('assignments', 'subjects', 'teachers', 'classes', 'session', 'school'));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'subject_id'        => 'required|exists:subjects,id',
            'teacher_id'        => 'required|exists:teachers,id',
            'school_class_id'   => 'required|exists:school_classes,id',
            'section_id'        => 'required|exists:sections,id',
            'school_session_id' => 'required|exists:school_sessions,id',
        ]);
        $data['school_id'] = app('school')->id;

        SubjectAssignment::updateOrCreate(
            [
                'subject_id'        => $data['subject_id'],
                'school_class_id'   => $data['school_class_id'],
                'section_id'        => $data['section_id'],
                'school_session_id' => $data['school_session_id'],
            ],
            $data
        );

        return redirect()->back()->with('success', 'Subject assigned to teacher');
    }
}
