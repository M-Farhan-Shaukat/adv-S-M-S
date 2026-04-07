<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\FeeVoucher;
use App\Models\FeeVoucherItem;
use App\Models\FeeStructure;

class FeeVoucherController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        // ✅ Get student with current enrollment
        $student = Student::with('currentEnrollment')->findOrFail($request->student_id);

        if (!$student->currentEnrollment) {
            return response()->json([
                'message' => 'Student not enrolled'
            ], 400);
        }

        $classId = $student->currentEnrollment->school_class_id;
        $sessionId = $student->currentEnrollment->school_session_id;

        // ✅ Prevent duplicate voucher
        $exists = FeeVoucher::where([
            'student_id' => $student->id,
            'school_session_id' => $sessionId,
            'month' => $request->month,
            'year' => $request->year,
        ])->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Voucher already exists'
            ], 400);
        }

        // ✅ Get fee structures
        $fees = FeeStructure::where('school_class_id', $classId)->get();

        if ($fees->isEmpty()) {
            return response()->json([
                'message' => 'No fee structure found for class'
            ], 400);
        }

        // ✅ Create Voucher
        $voucher = FeeVoucher::create([
            'student_id' => $student->id,
            'school_session_id' => $sessionId,
            'school_class_id' => $classId,
            'month' => $request->month,
            'year' => $request->year,
            'total_amount' => 0,
            'due_date' => now()->addDays(10),
        ]);

        $total = 0;

        // ✅ Create Items
        foreach ($fees as $fee) {
            FeeVoucherItem::create([
                'fee_voucher_id' => $voucher->id,
                'title'          => $fee->name,
                'total_amount'   => $fee->amount,
            ]);

            $total += $fee->amount;
        }

        // ✅ Update total
        $voucher->update([
            'total_amount' => $total
        ]);

        return response()->json([
            'message' => 'Voucher generated successfully',
            'voucher_id' => $voucher->id,
            'total' => $total
        ]);
    }
}
