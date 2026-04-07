<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\FeeVoucher;

class SchoolDashboardController extends Controller
{
    public function index()
    {
        $school = app('school');

        $stats = [
            'students'         => Student::count(),
            'teachers'         => Teacher::where('is_active', true)->count(),
            'staff'            => Staff::where('is_active', true)->count(),
            'pending_fees'     => FeeVoucher::where('status', '!=', 'paid')->count(),
            'monthly_income'   => Payment::whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
            'monthly_expense'  => Expense::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount'),
            'open_complaints'  => Complaint::whereIn('status', ['pending', 'in_progress'])->count(),
        ];

        $recentPayments = Payment::with('feeVoucher.student')->latest('paid_at')->take(5)->get();
        $recentComplaints = Complaint::with('user')->latest()->take(5)->get();

        return view('school.dashboard', compact('school', 'stats', 'recentPayments', 'recentComplaints'));
    }
}
