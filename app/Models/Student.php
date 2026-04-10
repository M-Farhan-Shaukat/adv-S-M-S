<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'students';
    protected $fillable = [
        'name', 'email', 'phone', 'dob', 'gender',
        'school_id', 'user_id', 'parent_user_id', 'is_active',
        'roll_number', 'address', 'guardian_name', 'guardian_phone', 'photo',
    ];

    public function school()          { return $this->belongsTo(School::class); }
    public function user()            { return $this->belongsTo(User::class); }
    public function parentUser()      { return $this->belongsTo(User::class, 'parent_user_id'); }
    public function enrollments()     { return $this->hasMany(StudentEnrollment::class); }
    public function vouchers()        { return $this->hasMany(FeeVoucher::class); }
    public function marks()           { return $this->hasMany(StudentMark::class); }
    public function attendances()     { return $this->hasMany(StudentAttendance::class); }
    public function recheckRequests() { return $this->hasMany(RecheckRequest::class); }

    public function currentEnrollment()
    {
        return $this->hasOne(StudentEnrollment::class)->where('is_current', true);
    }
}
