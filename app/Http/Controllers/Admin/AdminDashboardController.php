<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\FeeVoucher;
use App\Models\MeetingSchedule;
use App\Models\Payment;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\TeacherAttendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));

        if ($roles->contains('principal') && !$roles->contains('admin')) {
            return $this->principalDashboard($user);
        }

        return $this->superAdminDashboard();
    }

    // -------------------------------------------------------
    // SUPER ADMIN — system-wide, no school scope
    // -------------------------------------------------------
    private function superAdminDashboard()
    {
        $totalSchools  = School::count();
        $activeSchools = School::where('is_active', true)->count();
        $totalStudents = Student::withoutGlobalScopes()->count();
        $totalTeachers = Teacher::withoutGlobalScopes()->count();
        $totalStaff    = Staff::withoutGlobalScopes()->count();
        $totalUsers    = User::count();

        $schools = School::withCount(['students', 'teachers', 'staff', 'classes', 'users'])
            ->with(['users' => fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', 'principal'))])
            ->latest()->get();

        $usersByRole = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();

        return view('admin.dashboard_admin', compact(
            'totalSchools', 'activeSchools', 'totalStudents', 'totalTeachers',
            'totalStaff', 'totalUsers', 'schools', 'usersByRole',
        ));
    }

    // -------------------------------------------------------
    // PRINCIPAL — school-scoped
    // -------------------------------------------------------
    private function principalDashboard(User $user)
    {
        $school = $user->school;

        if (!$school) {
            return view('admin.principal.no_school');
        }

        app()->instance('school', $school);

        $totalStudents = Student::count();
        $totalTeachers = Teacher::where('is_active', true)->count();
        $totalStaff    = Staff::where('is_active', true)->count();
        $pendingFees   = FeeVoucher::whereIn('status', ['unpaid', 'partial'])->count();

        $monthlyIncome  = Payment::whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount');
        $monthlyExpense = Expense::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount');

        $incomeChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $incomeChart[] = [
                'label'   => $m->format('M'),
                'income'  => Payment::whereMonth('paid_at', $m->month)->whereYear('paid_at', $m->year)->sum('amount'),
                'expense' => Expense::whereMonth('date', $m->month)->whereYear('date', $m->year)->sum('amount'),
            ];
        }

        $openComplaints   = Complaint::whereIn('status', ['pending', 'in_progress'])->count();
        $recentComplaints = Complaint::with('user')->latest()->take(5)->get();
        $upcomingMeetings = MeetingSchedule::where('meeting_date', '>=', now())->where('status', 'scheduled')->orderBy('meeting_date')->take(4)->get();
        $todayPresent     = TeacherAttendance::where('date', today())->where('status', 'present')->count();
        $todayAbsent      = TeacherAttendance::where('date', today())->where('status', 'absent')->count();
        $recentPayments   = Payment::with('feeVoucher.student')->latest('paid_at')->take(6)->get();

        $totalUsers  = User::where('school_id', $school->id)->count();
        $usersByRole = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('users.school_id', $school->id)
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();

        $recentUsers = User::where('school_id', $school->id)->latest()->take(8)->get();
        $schools     = collect([$school]);

        return view('admin.dashboard', compact(
            'totalStudents', 'totalTeachers', 'totalStaff', 'pendingFees',
            'monthlyIncome', 'monthlyExpense', 'incomeChart',
            'openComplaints', 'recentComplaints', 'upcomingMeetings',
            'todayPresent', 'todayAbsent', 'recentPayments',
            'totalUsers', 'usersByRole', 'recentUsers', 'schools',
        ) + ['totalSchools' => 1]);
    }
}
