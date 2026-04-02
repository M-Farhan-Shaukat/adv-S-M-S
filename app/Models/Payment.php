<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $table = 'payments';
    protected $fillable = [
        'fee_voucher_id',
        'amount',
        'method',
        'transaction_id',
        'paid_at',
    ];
    public function voucher()
    {
        return $this->belongsTo(FeeVoucher::class, 'fee_voucher_id');
    }
}
