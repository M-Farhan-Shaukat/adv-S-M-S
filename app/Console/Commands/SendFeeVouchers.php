<?php

namespace App\Console\Commands;

use App\Mail\FeeVoucherMail;
use App\Models\FeeVoucher;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendFeeVouchers extends Command
{
    protected $signature   = 'fees:send-vouchers';
    protected $description = 'Send fee vouchers to students on the configured day of month';

    public function handle(): void
    {
        $today = now()->day;

        $schools = School::where('is_active', true)->get();

        foreach ($schools as $school) {
            if ($school->fee_voucher_day != $today) {
                continue;
            }

            app()->instance('school', $school);

            $vouchers = FeeVoucher::where('school_id', $school->id)
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->where('status', '!=', 'paid')
                ->with('student')
                ->get();

            foreach ($vouchers as $voucher) {
                if ($voucher->student?->email) {
                    try {
                        Mail::to($voucher->student->email)->queue(new FeeVoucherMail($voucher));
                        $this->info("Sent to: {$voucher->student->email}");
                    } catch (\Exception $e) {
                        $this->error("Failed: {$voucher->student->email} - {$e->getMessage()}");
                    }
                }
            }
        }

        $this->info('Fee voucher sending complete.');
    }
}
