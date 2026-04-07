<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $school = app('school');
        $complaints = Complaint::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()->paginate(15);

        return view('school.complaints.index', compact('complaints', 'school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:200',
            'description' => 'required|string',
            'type'        => 'required|in:academic,behavioral,facility,staff,other',
        ]);

        $school = app('school');
        $data['school_id'] = $school->id;
        $data['user_id']   = auth()->id();

        Complaint::create($data);

        return redirect()->back()->with('success', 'Complaint submitted');
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $request->validate(['resolution' => 'required|string']);

        $complaint->update([
            'status'      => 'resolved',
            'resolution'  => $request->resolution,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Complaint resolved');
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,resolved,rejected']);
        $complaint->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Status updated');
    }
}
