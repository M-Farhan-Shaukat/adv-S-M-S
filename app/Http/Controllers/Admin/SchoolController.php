<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Services\CredentialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::withCount(['classes', 'sessions'])
            ->whereHas('users.roles', fn($r) => $r->where('name', 'principal'))
            ->with([
                'users' => fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', 'principal'))
            ])
    ->latest()
        ->paginate(20);

        return view('admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('admin.schools.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:100',
            'slug'                   => 'required|string|unique:schools,slug|regex:/^[a-z0-9\-]+$/',
            'email'                  => 'nullable|email',
            'phone'                  => 'nullable|string',
            'address'                => 'nullable|string',
            'fee_voucher_day'        => 'required|integer|min:1|max:28',
            // Principal account
            'principal_name'         => 'required|string|max:100',
            'principal_email'        => 'required|email|unique:users,email',
        ]);

        // Create school
        $school = School::create([
            'name'            => $data['name'],
            'slug'            => $data['slug'],
            'email'           => $data['email'] ?? null,
            'phone'           => $data['phone'] ?? null,
            'address'         => $data['address'] ?? null,
            'fee_voucher_day' => $data['fee_voucher_day'],
            'is_active'       => true,
        ]);

        // Create principal user
        $password      = CredentialService::generatePassword();
        $principalRole = Role::where('name', 'principal')->first();

        $principal = User::create([
            'name'              => $data['principal_name'],
            'email'             => $data['principal_email'],
            'password'          => Hash::make($password),
            'school_id'         => $school->id,
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $principal->syncRoles([$principalRole]);

        // Send credentials
        CredentialService::sendCredentials(
            email:      $principal->email,
            name:       $principal->name,
            password:   $password,
            role:       'Principal',
            schoolName: $school->name,
            loginUrl:   url('/admin/login'),
            portalNote: "Your school dashboard: " . url("/admin/dashboard") .
                        "\nSchool URL: " . url("/{$school->slug}/dashboard"),
        );

        return redirect()->route('admin.schools.index')
            ->with('success', "School '{$school->name}' created. Principal credentials sent to {$principal->email}");
    }

    public function edit(School $school)
    {
        return view('admin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'nullable|email',
            'phone'           => 'nullable|string',
            'address'         => 'nullable|string',
            'fee_voucher_day' => 'required|integer|min:1|max:28',
        ]);

        $school->update($data);

        return redirect()->route('admin.schools.index')->with('success', 'School updated');
    }

    public function toggle(School $school)
    {
        $school->update(['is_active' => !$school->is_active]);
        return back()->with('success', 'School status updated');
    }

    public function destroy(School $school)
    {
        $school->delete();
        return redirect()->route('admin.schools.index')->with('success', 'School deleted');
    }
}
