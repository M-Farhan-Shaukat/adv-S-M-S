<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeVoucherItem extends Model
{
    use SoftDeletes;

    protected $table = 'fee_voucher_items';
    protected $fillable = ['fee_voucher_id', 'title', 'total_amount'];

    public function voucher() { return $this->belongsTo(FeeVoucher::class, 'fee_voucher_id'); }
}
