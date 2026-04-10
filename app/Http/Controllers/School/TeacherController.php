<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Services\CredentialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $school   = app('school');
        $teachers = Teacher::with('user')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->status !== null && $request->status !== '',
                fn($q) => $q->where('is_active', $request->status === 'active'))
            ->paginate(15);

        return view('school.teachers.index', compact('teachers', 'school'));
    }

    public function create()
    {
        $school = app('school');
        return view('school.teachers.create', compact('school'));
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
            $user     = null;
            $password = null;

            if (!empty($data['email'])) {
                $password = CredentialService::generatePassword();

                $user = User::create([
                    'name'              => $data['name'],
                    'email'             => $data['email'],
                    'password'          => Hash::make($password),
                    'school_id'         => $school->id,
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]);
                $user->assignRole('teacher');
            }

            $teacher = Teacher::create([
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

            // Send credentials email
            if ($user && $password) {
                CredentialService::sendCredentials(
                    email:      $user->email,
                    name:       $user->name,
                    password:   $password,
                    role:       'Teacher',
                    schoolName: $school->name,
                    loginUrl:   url('/login'),
                    portalNote: "After login, go to: " . url("/{$school->slug}/teacher-portal/dashboard")
                );
            }

            $msg = 'Teacher added successfully.';
            if ($user && $password) {
                $msg .= ' Login credentials sent to ' . $user->email;
            }

            return redirect()->route('school.teachers.index', $school->slug)->with('success', $msg);
        });
    }

    public function edit(string $school, Teacher $teacher)
    {
        $school = app('school');
        return view('school.teachers.edit', compact('teacher', 'school'));
    }

    public function update(Request $request, string $school, Teacher $teacher)
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:100',
            'phone'                  => 'nullable|string',
            'salary'                 => 'required|numeric|min:0',
            'daily_required_minutes' => 'required|integer|min:60',
            'qualification'          => 'nullable|string',
            'joining_date'           => 'nullable|date',
        ]);

        $data['is_active'] = $request->input('is_active', '1') == '1';
        $teacher->update($data);

        return redirect()->route('school.teachers.index', app('school')->slug)
            ->with('success', 'Teacher updated');
    }

    public function destroy(string $school, Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('school.teachers.index', app('school')->slug)
            ->with('success', 'Teacher deleted');
    }

    public function toggleStatus(string $school, Teacher $teacher)
    {
        $teacher->update(['is_active' => !$teacher->is_active]);
        return redirect()->back()->with('success', 'Status updated');
    }
}
