<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'teachers';
    protected $fillable = [
        'school_id', 'user_id', 'name', 'email', 'phone',
        'salary', 'daily_required_minutes', 'is_active',
        'address', 'qualification', 'photo', 'joining_date',
    ];

    public function school()      { return $this->belongsTo(School::class); }
    public function user()        { return $this->belongsTo(User::class); }
    public function assignments() { return $this->hasMany(SubjectAssignment::class); }
    public function attendances() { return $this->hasMany(TeacherAttendance::class); }
    public function payrolls()    { return $this->hasMany(TeacherPayroll::class); }

    // Subjects taught by this teacher (via assignments)
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_assignments', 'teacher_id', 'subject_id');
    }

    // Latest payroll
    public function latestPayroll()
    {
        return $this->hasOne(TeacherPayroll::class)->latestOfMany();
    }
}
