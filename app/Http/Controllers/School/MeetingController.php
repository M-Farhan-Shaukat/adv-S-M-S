<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\MeetingParticipant;
use App\Models\MeetingSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        $school = app('school');
        $meetings = MeetingSchedule::with('organizer', 'participants.user')
            ->latest('meeting_date')->paginate(15);

        return view('school.meetings.index', compact('meetings', 'school'));
    }

    public function create()
    {
        $school = app('school');
        $users = User::where('school_id', $school->id)->get();
        return view('school.meetings.create', compact('users', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:200',
            'description'      => 'nullable|string',
            'meeting_date'     => 'required|date',
            'duration_minutes' => 'required|integer|min:15',
            'venue'            => 'nullable|string',
            'type'             => 'required|in:parent_teacher,staff,general,emergency',
            'participants'     => 'nullable|array',
            'participants.*'   => 'exists:users,id',
        ]);

        $school = app('school');
        $meeting = MeetingSchedule::create([
            'school_id'        => $school->id,
            'scheduled_by'     => auth()->id(),
            'title'            => $data['title'],
            'description'      => $data['description'] ?? null,
            'meeting_date'     => $data['meeting_date'],
            'duration_minutes' => $data['duration_minutes'],
            'venue'            => $data['venue'] ?? null,
            'type'             => $data['type'],
        ]);

        if (!empty($data['participants'])) {
            foreach ($data['participants'] as $userId) {
                MeetingParticipant::create([
                    'meeting_schedule_id' => $meeting->id,
                    'user_id'             => $userId,
                ]);
            }
        }

        return redirect()->route('school.meetings.index', $school->slug)->with('success', 'Meeting scheduled');
    }

    public function updateStatus(Request $request, MeetingSchedule $meeting)
    {
        $request->validate(['status' => 'required|in:scheduled,completed,cancelled']);
        $meeting->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Meeting status updated');
    }
}
