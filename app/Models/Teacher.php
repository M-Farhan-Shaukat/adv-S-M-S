<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';

    protected $fillable = [
        'school_id',
        'name',
        'email',
        'phone',
        'salary',
        'daily_required_minutes',
        'is_active',
    ];
    public function assignments()
    {
        return $this->hasMany(SubjectAssignment::class);
    }

    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(TeacherPayroll::class);
    }
}
