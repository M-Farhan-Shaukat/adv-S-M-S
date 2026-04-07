<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\FeeVoucher;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fee_voucher_id' => 'required|exists:fee_vouchers,id',
            'amount' => 'required|numeric|min:1',
            'method' => 'nullable|string'
        ]);

        $voucher = FeeVoucher::findOrFail($request->fee_voucher_id);

        // ❗ prevent overpayment
        $remaining = $voucher->total_amount - $voucher->paid_amount;

        if ($request->amount > $remaining) {
            return response()->json([
                'message' => 'Amount exceeds remaining balance'
            ], 400);
        }

        // ✅ create payment
        $payment = Payment::create([
            'fee_voucher_id' => $voucher->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'paid_at' => now(),
        ]);

        // ✅ update voucher paid_amount
        $voucher->paid_amount += $request->amount;

        // ✅ update status
        if ($voucher->paid_amount == 0) {
            $voucher->status = 'unpaid';
        } elseif ($voucher->paid_amount < $voucher->total_amount) {
            $voucher->status = 'partial';
        } else {
            $voucher->status = 'paid';
        }

        $voucher->save();

        return response()->json([
            'message' => 'Payment recorded',
            'paid_amount' => $voucher->paid_amount,
            'status' => $voucher->status
        ]);
    }
}
