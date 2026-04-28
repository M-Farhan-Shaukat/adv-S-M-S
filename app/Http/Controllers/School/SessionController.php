<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $school   = app('school');
        $sessions = SchoolSession::orderBy('start_date', 'desc')->paginate(20);
        return view('school.sessions.index', compact('sessions', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'status'     => 'required|in:active,inactive,exam,completed',
        ]);
        $data['school_id'] = app('school')->id;

        SchoolSession::create($data);
        return redirect()->back()->with('success', 'Session created.');
    }

    public function edit(string $school, SchoolSession $session)
    {
        $school = app('school');
        return view('school.sessions.edit', compact('session', 'school'));
    }

    public function update(Request $request, string $school, SchoolSession $session)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'status'     => 'required|in:active,inactive,exam,completed',
        ]);

        $session->update($data);
        return redirect()->route('school.sessions.index', app('school')->slug)
            ->with('success', 'Session updated.');
    }

    public function destroy(string $school, SchoolSession $session)
    {
        $session->delete();
        return redirect()->back()->with('success', 'Session deleted.');
    }

    public function toggleStatus(string $school, SchoolSession $session)
    {
        $newStatus = $session->status === 'active' ? 'inactive' : 'active';
        $session->update(['status' => $newStatus]);
        return redirect()->back()->with('success', "Session marked as {$newStatus}.");
    }
}
