<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\TeacherPayroll;
use App\Models\StaffSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function income(Request $request)
    {
        $school = app('school');
        [$from, $to] = $this->getDateRange($request);

        $payments = Payment::with('feeVoucher.student')
            ->whereBetween('paid_at', [$from, $to])
            ->get();

        $totalIncome = $payments->sum('amount');
        $byMonth = $payments->groupBy(fn($p) => Carbon::parse($p->paid_at)->format('Y-m'))
            ->map(fn($g) => $g->sum('amount'));

        return view('school.reports.income', compact('payments', 'totalIncome', 'byMonth', 'from', 'to', 'school'));
    }

    public function expense(Request $request)
    {
        $school = app('school');
        [$from, $to] = $this->getDateRange($request);

        $expenses = Expense::with('category')
            ->whereBetween('date', [$from, $to])
            ->get();

        $totalExpense = $expenses->sum('amount');
        $byCategory = $expenses->groupBy('expense_category_id')
            ->map(fn($g) => ['name' => $g->first()->category?->name ?? 'Uncategorized', 'total' => $g->sum('amount')]);

        return view('school.reports.expense', compact('expenses', 'totalExpense', 'byCategory', 'from', 'to', 'school'));
    }

    public function incomeVsExpense(Request $request)
    {
        $school = app('school');
        [$from, $to] = $this->getDateRange($request);

        $income = Payment::whereBetween('paid_at', [$from, $to])->sum('amount');
        $expense = Expense::whereBetween('date', [$from, $to])->sum('amount');
        $profit = $income - $expense;

        // Monthly breakdown
        $months = [];
        $current = Carbon::parse($from)->startOfMonth();
        $end = Carbon::parse($to)->endOfMonth();

        while ($current <= $end) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd   = $current->copy()->endOfMonth();

            $months[] = [
                'label'   => $current->format('M Y'),
                'income'  => Payment::whereBetween('paid_at', [$monthStart, $monthEnd])->sum('amount'),
                'expense' => Expense::whereBetween('date', [$monthStart, $monthEnd])->sum('amount'),
            ];

            $current->addMonth();
        }

        return view('school.reports.income_vs_expense', compact('income', 'expense', 'profit', 'months', 'from', 'to', 'school'));
    }

    public function payroll(Request $request)
    {
        $school = app('school');
        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        $teacherPayrolls = TeacherPayroll::with('teacher')->where('month', $month)->where('year', $year)->get();
        $staffSalaries   = StaffSalary::with('staff')->where('month', $month)->where('year', $year)->get();

        $totalTeacher = $teacherPayrolls->sum('net_salary');
        $totalStaff   = $staffSalaries->sum('net_salary');

        return view('school.reports.payroll', compact('teacherPayrolls', 'staffSalaries', 'totalTeacher', 'totalStaff', 'month', 'year', 'school'));
    }

    private function getDateRange(Request $request): array
    {
        $period = $request->period ?? 'monthly';
        $from = $request->from;
        $to   = $request->to;

        if (!$from || !$to) {
            $from = match($period) {
                'weekly'    => now()->startOfWeek()->toDateString(),
                'monthly'   => now()->startOfMonth()->toDateString(),
                'biannual'  => now()->subMonths(6)->startOfMonth()->toDateString(),
                'annual'    => now()->startOfYear()->toDateString(),
                default     => now()->startOfMonth()->toDateString(),
            };
            $to = now()->toDateString();
        }

        return [$from, $to];
    }
}
