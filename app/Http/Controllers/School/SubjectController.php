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
        $classes  = SchoolClass::with('sections')->get();
        $subjects = Subject::with('schoolClass')
            ->withCount('assignments')
            ->paginate(30);

        return view('school.subjects.index', compact('subjects', 'classes', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'school_class_id' => 'required|exists:school_classes,id',
        ]);

        $data['school_id'] = app('school')->id;

        // Prevent duplicate subject in same class
        $exists = Subject::where('school_id', $data['school_id'])
            ->where('school_class_id', $data['school_class_id'])
            ->where('name', $data['name'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This subject already exists for this class.');
        }

        Subject::create($data);
        return redirect()->back()->with('success', 'Subject created');
    }

    public function destroy(string $school, Subject $subject)
    {
        $subject->delete();
        return redirect()->back()->with('success', 'Subject deleted');
    }

    public function edit(string $school, Subject $subject)
    {
        $school  = app('school');
        $classes = SchoolClass::with('sections')->get();
        return view('school.subjects.edit', compact('subject', 'classes', 'school'));
    }

    public function update(Request $request, string $school, Subject $subject)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'school_class_id' => 'required|exists:school_classes,id',
        ]);
        $subject->update($data);
        return redirect()->route('school.subjects.index', app('school')->slug)
            ->with('success', 'Subject updated.');
    }

    // AJAX: get subjects by class
    public function byClass(Request $request)
    {
        $subjects = Subject::where('school_id', app('school')->id)
            ->where('school_class_id', $request->class_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($subjects);
    }

    // Assignments
    public function assignments(Request $request)
    {
        $school   = app('school');
        $classes  = SchoolClass::with('sections')->get();
        $teachers = Teacher::where('is_active', true)->get();
        $session  = $school->activeSession ?? $school->currentSession;

        $classId     = $request->class_id;
        $assignments = SubjectAssignment::with('subject', 'teacher', 'schoolClass', 'section')
            ->when($classId, fn($q) => $q->where('school_class_id', $classId))
            ->latest()
            ->paginate(20)
            ->appends($request->only('class_id'));

        return view('school.subjects.assignments', compact(
            'assignments', 'teachers', 'classes', 'session', 'school', 'classId'
        ));
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
