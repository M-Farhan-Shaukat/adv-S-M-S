<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use BelongsToSchool;
    protected $table = 'students';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'dob',
        'gender',
        'school_id',
        'is_active',
    ];
    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function currentEnrollment()
    {
        return $this->hasOne(StudentEnrollment::class)
            ->whereHas('session', function ($q) {
                $q->whereIn('status', ['active', 'exam']);
            });
    }

    public function vouchers()
    {
        return $this->hasMany(FeeVoucher::class);
    }

}
