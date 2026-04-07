<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\TeacherPayroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $school = app('school');
        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        $payrolls = TeacherPayroll::with('teacher')
            ->where('month', $month)->where('year', $year)
            ->paginate(20);

        $teachers = Teacher::where('is_active', true)->get();

        return view('school.payroll.index', compact('payrolls', 'teachers', 'month', 'year', 'school'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020',
        ]);

        $school = app('school');
        $session = $school->activeSession;
        $teachers = Teacher::where('is_active', true)->get();
        $generated = 0;

        foreach ($teachers as $teacher) {
            // Skip if already generated
            if (TeacherPayroll::where(['teacher_id' => $teacher->id, 'month' => $request->month, 'year' => $request->year])->exists()) {
                continue;
            }

            // Get attendance for this month
            $attendances = TeacherAttendance::where('teacher_id', $teacher->id)
                ->whereMonth('date', $request->month)
                ->whereYear('date', $request->year)
                ->get();

            $totalMinutes    = $attendances->sum('working_minutes');
            $workingDays     = $attendances->where('status', 'present')->count();
            $requiredMinutes = $workingDays * $teacher->daily_required_minutes;
            $shortMinutes    = max(0, $requiredMinutes - $totalMinutes);

            // Per-minute salary
            $monthlyMinutes = 26 * $teacher->daily_required_minutes; // 26 working days
            $perMinute      = $monthlyMinutes > 0 ? $teacher->salary / $monthlyMinutes : 0;

            $grossSalary = $totalMinutes * $perMinute;
            $deduction   = $shortMinutes * $perMinute;
            $netSalary   = max(0, $grossSalary - $deduction);

            TeacherPayroll::create([
                'teacher_id'        => $teacher->id,
                'school_id'         => $school->id,
                'school_session_id' => $session->id,
                'month'             => $request->month,
                'year'              => $request->year,
                'total_minutes'     => $totalMinutes,
                'gross_salary'      => round($grossSalary, 2),
                'deduction'         => round($deduction, 2),
                'net_salary'        => round($netSalary, 2),
                'required_minutes'  => $requiredMinutes,
                'short_minutes'     => $shortMinutes,
            ]);

            $generated++;
        }

        return redirect()->route('school.payroll.index', $school->slug)
            ->with('success', "Payroll generated for {$generated} teachers");
    }

    public function show(TeacherPayroll $payroll)
    {
        $payroll->load('teacher', 'school', 'session');
        return view('school.payroll.show', compact('payroll'));
    }
}
