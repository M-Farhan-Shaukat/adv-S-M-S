<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\Exam;
use App\Models\FeeVoucher;
use App\Models\MeetingSchedule;
use App\Models\Payment;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ===== SYSTEM-WIDE STATS (Super Admin) =====
        $totalSchools   = School::count();
        $totalStudents  = Student::withoutGlobalScopes()->count();
        $totalTeachers  = Teacher::withoutGlobalScopes()->count();
        $totalStaff     = Staff::withoutGlobalScopes()->count();
        $totalUsers     = User::count();

        // Users by role (Spatie)
        $usersByRole = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();

        // Recent users
        $recentUsers = User::latest()->take(8)->get();

        // ===== FINANCIAL STATS =====
        $monthlyIncome  = Payment::withoutGlobalScopes()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $monthlyExpense = Expense::withoutGlobalScopes()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $pendingFees = FeeVoucher::withoutGlobalScopes()
            ->whereIn('status', ['unpaid', 'partial'])
            ->count();

        $totalFeeCollected = Payment::withoutGlobalScopes()
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        // ===== MONTHLY INCOME CHART (last 6 months) =====
        $incomeChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $incomeChart[] = [
                'label'  => $month->format('M'),
                'income' => Payment::withoutGlobalScopes()
                    ->whereMonth('paid_at', $month->month)
                    ->whereYear('paid_at', $month->year)
                    ->sum('amount'),
                'expense' => Expense::withoutGlobalScopes()
                    ->whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->sum('amount'),
            ];
        }

        // ===== COMPLAINTS =====
        $openComplaints = Complaint::withoutGlobalScopes()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $recentComplaints = Complaint::withoutGlobalScopes()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        // ===== UPCOMING MEETINGS =====
        $upcomingMeetings = MeetingSchedule::withoutGlobalScopes()
            ->where('meeting_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('meeting_date')
            ->take(4)
            ->get();

        // ===== TODAY'S TEACHER ATTENDANCE =====
        $todayPresent = TeacherAttendance::withoutGlobalScopes()
            ->where('date', today())
            ->where('status', 'present')
            ->count();

        $todayAbsent = TeacherAttendance::withoutGlobalScopes()
            ->where('date', today())
            ->where('status', 'absent')
            ->count();

        // ===== RECENT PAYMENTS =====
        $recentPayments = Payment::withoutGlobalScopes()
            ->with('feeVoucher.student')
            ->latest('paid_at')
            ->take(6)
            ->get();

        // ===== SCHOOLS LIST =====
        $schools = School::withCount([
            'classes',
            'sessions',
        ])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSchools',
            'totalStudents',
            'totalTeachers',
            'totalStaff',
            'totalUsers',
            'usersByRole',
            'recentUsers',
            'monthlyIncome',
            'monthlyExpense',
            'pendingFees',
            'totalFeeCollected',
            'incomeChart',
            'openComplaints',
            'recentComplaints',
            'upcomingMeetings',
            'todayPresent',
            'todayAbsent',
            'recentPayments',
            'schools',
        ));
    }
}
