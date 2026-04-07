<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SchoolSession;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $school = app('school');
        $classes = SchoolClass::with('sections')->paginate(20);
        $sessions = SchoolSession::all();
        return view('school.classes.index', compact('classes', 'sessions', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:50',
            'code'              => 'nullable|string|max:20',
            'school_session_id' => 'required|exists:school_sessions,id',
        ]);

        $data['school_id'] = app('school')->id;
        SchoolClass::create($data);

        return redirect()->back()->with('success', 'Class created');
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate(['name' => 'required|string', 'code' => 'nullable|string']);
        $class->update($data);
        return redirect()->back()->with('success', 'Class updated');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->back()->with('success', 'Class deleted');
    }

    // Sections
    public function sections(SchoolClass $class)
    {
        $school = app('school');
        $sections = Section::where('school_class_id', $class->id)->paginate(20);
        return view('school.classes.sections', compact('class', 'sections', 'school'));
    }

    public function storeSection(Request $request, SchoolClass $class)
    {
        $data = $request->validate(['name' => 'required|string|max:50']);
        Section::create([
            'school_id'       => app('school')->id,
            'school_class_id' => $class->id,
            'name'            => $data['name'],
        ]);
        return redirect()->back()->with('success', 'Section created');
    }

    public function destroySection(Section $section)
    {
        $section->delete();
        return redirect()->back()->with('success', 'Section deleted');
    }
}
