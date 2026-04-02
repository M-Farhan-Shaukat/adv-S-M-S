<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeVoucher extends Model
{
    protected $table = 'fees_voucher';
    protected $fillable = [
        'student_id',
        'school_id',
        'session_id',
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
