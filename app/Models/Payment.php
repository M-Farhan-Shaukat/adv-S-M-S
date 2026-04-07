<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'payments';
    protected $fillable = [
        'fee_voucher_id', 'school_id', 'amount',
        'method', 'transaction_id', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function feeVoucher() { return $this->belongsTo(FeeVoucher::class, 'fee_voucher_id'); }
    public function school()     { return $this->belongsTo(School::class); }

    // Alias
    public function voucher() { return $this->belongsTo(FeeVoucher::class, 'fee_voucher_id'); }
}
