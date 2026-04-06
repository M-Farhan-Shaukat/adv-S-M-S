<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class FeeVoucher extends Model
{
    use BelongsToSchool;
    protected $table = 'fee_vouchers';
    protected $fillable = [
        'student_id',
        'school_id',
        'school_session_id',
        'month',
        'year',
        'total_amount',
        'paid_amount',
        'due_date',
        'status',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function items()
    {
        return $this->hasMany(FeeVoucherItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
