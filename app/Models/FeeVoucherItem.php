<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeVoucherItem extends Model
{
    protected $table = 'fee_voucher_items';
    protected $fillable = [
        'fee_voucher_id',
        'title',
        'amount',
    ];
}
