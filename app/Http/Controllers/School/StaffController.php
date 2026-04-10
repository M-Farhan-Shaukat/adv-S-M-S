<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffSalary;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $school = app('school');
        $staff  = Staff::when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->paginate(15);
        return view('school.staff.index', compact('staff', 'school'));
    }

    public function create()
    {
        $school = app('school');
        return view('school.staff.create', compact('school'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string',
            'designation'  => 'required|string',
            'salary'       => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
        ]);
        $data['school_id'] = app('school')->id;
        Staff::create($data);
        return redirect()->route('school.staff.index', app('school')->slug)->with('success', 'Staff added');
    }

    public function edit(string $school, Staff $staff)
    {
        $school = app('school');
        return view('school.staff.edit', compact('staff', 'school'));
    }

    public function update(Request $request, string $school, Staff $staff)
    {
        $data = $request->validate([
            'name'        => 'required|string',
            'phone'       => 'nullable|string',
            'designation' => 'required|string',
            'salary'      => 'required|numeric|min:0',
        ]);
        $data['is_active'] = $request->input('is_active', '1') == '1';
        $staff->update($data);
        return redirect()->route('school.staff.index', app('school')->slug)->with('success', 'Staff updated');
    }

    public function destroy(string $school, Staff $staff)
    {
        $staff->delete();
        return redirect()->back()->with('success', 'Staff deleted');
    }

    public function salaries(Request $request)
    {
        $school    = app('school');
        $month     = $request->month ?? now()->month;
        $year      = $request->year  ?? now()->year;
        $salaries  = StaffSalary::with('staff')->where('month', $month)->where('year', $year)->paginate(20);
        $staffList = Staff::where('is_active', true)->get();
        return view('school.staff.salaries', compact('salaries', 'staffList', 'month', 'year', 'school'));
    }

    public function generateSalary(Request $request)
    {
        $request->validate(['month' => 'required|integer', 'year' => 'required|integer']);
        $school    = app('school');
        $staffList = Staff::where('is_active', true)->get();
        $generated = 0;
        foreach ($staffList as $s) {
            if (StaffSalary::where(['staff_id' => $s->id, 'month' => $request->month, 'year' => $request->year])->exists()) continue;
            StaffSalary::create([
                'school_id'    => $school->id,
                'staff_id'     => $s->id,
                'month'        => $request->month,
                'year'         => $request->year,
                'basic_salary' => $s->salary,
                'allowances'   => 0,
                'deductions'   => 0,
                'net_salary'   => $s->salary,
            ]);
            $generated++;
        }
        return redirect()->back()->with('success', "Salary generated for {$generated} staff");
    }

    public function markSalaryPaid(string $school, StaffSalary $salary)
    {
        $salary->update(['status' => 'paid', 'paid_date' => today()]);
        return redirect()->back()->with('success', 'Salary marked as paid');
    }
}
