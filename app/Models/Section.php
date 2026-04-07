<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'sections';
    protected $fillable = ['school_class_id', 'school_id', 'name'];

    public function school()        { return $this->belongsTo(School::class); }
    public function schoolClass()   { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function class()         { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function enrollments()   { return $this->hasMany(StudentEnrollment::class); }
    public function assignments()   { return $this->hasMany(SubjectAssignment::class); }
    public function examSchedules() { return $this->hasMany(ExamSchedule::class); }
    public function attendances()   { return $this->hasMany(StudentAttendance::class); }

    public function students()
    {
        return $this->hasManyThrough(Student::class, StudentEnrollment::class, 'section_id', 'id', 'id', 'student_id');
    }
}
