<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $school = app('school');
        $teachers = Teacher::with('user')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('is_active', $request->status === 'active'))
            ->paginate(15);

        return view('school.teachers.index', compact('teachers', 'school'));
    }

    public function create()
    {
        return view('school.teachers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:100',
            'email'                  => 'nullable|email|unique:users,email',
            'phone'                  => 'nullable|string',
            'salary'                 => 'required|numeric|min:0',
            'daily_required_minutes' => 'required|integer|min:60',
            'qualification'          => 'nullable|string',
            'joining_date'           => 'nullable|date',
        ]);

        $school = app('school');

        return DB::transaction(function () use ($data, $school) {
            $user = null;
            if (!empty($data['email'])) {
                $user = User::create([
                    'name'      => $data['name'],
                    'email'     => $data['email'],
                    'password'  => Hash::make('password123'),
                    'school_id' => $school->id,
                ]);
                $user->assignRole('teacher');
            }

            Teacher::create([
                'school_id'              => $school->id,
                'user_id'                => $user?->id,
                'name'                   => $data['name'],
                'email'                  => $data['email'] ?? null,
                'phone'                  => $data['phone'] ?? null,
                'salary'                 => $data['salary'],
                'daily_required_minutes' => $data['daily_required_minutes'],
                'qualification'          => $data['qualification'] ?? null,
                'joining_date'           => $data['joining_date'] ?? null,
            ]);

            return redirect()->route('school.teachers.index', $school->slug)
                ->with('success', 'Teacher added successfully');
        });
    }

    public function edit(Teacher $teacher)
    {
        return view('school.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:100',
            'phone'                  => 'nullable|string',
            'salary'                 => 'required|numeric|min:0',
            'daily_required_minutes' => 'required|integer|min:60',
            'qualification'          => 'nullable|string',
            'joining_date'           => 'nullable|date',
            'is_active'              => 'boolean',
        ]);

        $teacher->update($data);

        return redirect()->route('school.teachers.index', app('school')->slug)
            ->with('success', 'Teacher updated successfully');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->back()->with('success', 'Teacher deleted');
    }

    public function toggleStatus(Teacher $teacher)
    {
        $teacher->update(['is_active' => !$teacher->is_active]);
        return redirect()->back()->with('success', 'Status updated');
    }
}
