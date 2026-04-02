<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class FeeVoucherItem extends Model
{
    use BelongsToSchool;
    protected $table = 'fee_voucher_items';
    protected $fillable = [
        'fee_voucher_id',
        'title',
        'amount',
    ];
}
