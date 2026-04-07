<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolSession extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'school_sessions';
    protected $fillable = ['school_id', 'name', 'start_date', 'end_date', 'status'];

    public function school()       { return $this->belongsTo(School::class); }
    public function classes()      { return $this->hasMany(SchoolClass::class, 'school_session_id'); }
    public function enrollments()  { return $this->hasMany(StudentEnrollment::class); }
    public function exams()        { return $this->hasMany(Exam::class); }
    public function payrolls()     { return $this->hasMany(TeacherPayroll::class); }
    public function attendances()  { return $this->hasMany(TeacherAttendance::class); }
}
