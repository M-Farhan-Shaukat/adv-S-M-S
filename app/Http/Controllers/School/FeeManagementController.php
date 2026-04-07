<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\FeeVoucher;
use App\Models\FeeVoucherItem;
use App\Models\Payment;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FeeManagementController extends Controller
{
    // =================== FEE TYPES ===================

    public function feeTypes()
    {
        $school = app('school');
        $types = FeeType::paginate(20);
        return view('school.fees.types', compact('types', 'school'));
    }

    public function storeFeeType(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $data['school_id'] = app('school')->id;
        FeeType::create($data);
        return redirect()->back()->with('success', 'Fee type added');
    }

    // =================== FEE STRUCTURES ===================

    public function feeStructures()
    {
        $school = app('school');
        $structures = FeeStructure::with('feeType', 'schoolClass')->paginate(20);
        $classes = SchoolClass::all();
        $types   = FeeType::all();
        return view('school.fees.structures', compact('structures', 'classes', 'types', 'school'));
    }

    public function storeFeeStructure(Request $request)
    {
        $data = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'fee_type_id'     => 'required|exists:fee_types,id',
            'name'            => 'required|string',
            'amount'          => 'required|numeric|min:0',
        ]);

        $data['school_id'] = app('school')->id;
        FeeStructure::create($data);
        return redirect()->back()->with('success', 'Fee structure added');
    }

    public function destroyFeeStructure(FeeStructure $feeStructure)
    {
        $feeStructure->delete();
        return redirect()->back()->with('success', 'Fee structure deleted');
    }

    // =================== FEE VOUCHERS ===================

    public function vouchers(Request $request)
    {
        $school = app('school');
        $vouchers = FeeVoucher::with('student', 'items')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->month, fn($q) => $q->where('month', $request->month))
            ->when($request->year, fn($q) => $q->where('year', $request->year))
            ->latest()->paginate(20);

        return view('school.fees.vouchers', compact('vouchers', 'school'));
    }

    public function generateVouchers(Request $request)
    {
        $request->validate([
            'month'           => 'required|integer|min:1|max:12',
            'year'            => 'required|integer',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'due_date'        => 'required|date',
        ]);

        $school = app('school');
        $session = $school->activeSession;

        $query = Student::with('currentEnrollment')->where('is_active', true);
        if ($request->school_class_id) {
            $query->whereHas('currentEnrollment', fn($q) => $q->where('school_class_id', $request->school_class_id));
        }

        $students = $query->get();
        $generated = 0;

        DB::transaction(function () use ($students, $request, $school, $session, &$generated) {
            foreach ($students as $student) {
                if (!$student->currentEnrollment) continue;

                $classId = $student->currentEnrollment->school_class_id;

                if (FeeVoucher::where(['student_id' => $student->id, 'school_session_id' => $session->id, 'month' => $request->month, 'year' => $request->year])->exists()) {
                    continue;
                }

                $fees = FeeStructure::where('school_class_id', $classId)->get();
                if ($fees->isEmpty()) continue;

                $voucher = FeeVoucher::create([
                    'school_id'         => $school->id,
                    'student_id'        => $student->id,
                    'school_session_id' => $session->id,
                    'school_class_id'   => $classId,
                    'month'             => $request->month,
                    'year'              => $request->year,
                    'total_amount'      => 0,
                    'due_date'          => $request->due_date,
                ]);

                $total = 0;
                foreach ($fees as $fee) {
                    FeeVoucherItem::create([
                        'fee_voucher_id' => $voucher->id,
                        'title'          => $fee->name,
                        'total_amount'   => $fee->amount,
                    ]);
                    $total += $fee->amount;
                }

                $voucher->update(['total_amount' => $total]);
                $generated++;
            }
        });

        return redirect()->back()->with('success', "Generated {$generated} vouchers");
    }

    public function sendVouchers(Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year'  => 'required|integer',
        ]);

        $school = app('school');
        $vouchers = FeeVoucher::with('student')
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->where('status', '!=', 'paid')
            ->get();

        $sent = 0;
        foreach ($vouchers as $voucher) {
            if ($voucher->student->email) {
                try {
                    Mail::to($voucher->student->email)->send(new \App\Mail\FeeVoucherMail($voucher));
                    $sent++;
                } catch (\Exception $e) {
                    // log and continue
                }
            }
        }

        return redirect()->back()->with('success', "Vouchers sent to {$sent} students");
    }

    public function showVoucher(FeeVoucher $feeVoucher)
    {
        $feeVoucher->load('student', 'items', 'payments');
        return view('school.fees.voucher_detail', compact('feeVoucher'));
    }

    // =================== PAYMENTS ===================

    public function payments(Request $request)
    {
        $school = app('school');
        $payments = Payment::with('feeVoucher.student')
            ->when($request->from, fn($q) => $q->whereDate('paid_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('paid_at', '<=', $request->to))
            ->latest('paid_at')->paginate(20);

        return view('school.fees.payments', compact('payments', 'school'));
    }

    public function collectPayment(Request $request)
    {
        $request->validate([
            'fee_voucher_id' => 'required|exists:fee_vouchers,id',
            'amount'         => 'required|numeric|min:1',
            'method'         => 'required|in:cash,bank,jazzcash,easypaisa',
        ]);

        $voucher = FeeVoucher::findOrFail($request->fee_voucher_id);
        $remaining = $voucher->total_amount - $voucher->paid_amount;

        if ($request->amount > $remaining) {
            return redirect()->back()->with('error', 'Amount exceeds remaining balance');
        }

        Payment::create([
            'fee_voucher_id' => $voucher->id,
            'school_id'      => app('school')->id,
            'amount'         => $request->amount,
            'method'         => $request->method,
            'paid_at'        => now(),
        ]);

        $voucher->paid_amount += $request->amount;
        $voucher->status = $voucher->paid_amount >= $voucher->total_amount ? 'paid'
            : ($voucher->paid_amount > 0 ? 'partial' : 'unpaid');
        $voucher->save();

        return redirect()->back()->with('success', 'Payment recorded');
    }
}
