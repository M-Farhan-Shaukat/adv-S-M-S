<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeVoucher extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'fee_vouchers';
    protected $fillable = [
        'student_id', 'school_id', 'school_session_id', 'school_class_id',
        'month', 'year', 'total_amount', 'paid_amount', 'due_date', 'status',
    ];

    public function student()     { return $this->belongsTo(Student::class); }
    public function school()      { return $this->belongsTo(School::class); }
    public function session()     { return $this->belongsTo(SchoolSession::class, 'school_session_id'); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function items()       { return $this->hasMany(FeeVoucherItem::class); }
    public function payments()    { return $this->hasMany(Payment::class); }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }
}
