<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToSchool;
    protected $table = 'payments';
    protected $fillable = [
        'fee_voucher_id',
        'amount',
        'method',
        'transaction_id',
        'paid_at',
        'school_id'
    ];
    public function voucher()
    {
        return $this->belongsTo(FeeVoucher::class, 'fee_voucher_id');
    }
}
