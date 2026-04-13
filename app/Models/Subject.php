<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'subjects';
    protected $fillable = ['school_id', 'school_class_id', 'name'];

    public function school()        { return $this->belongsTo(School::class); }
    public function schoolClass()   { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function assignments()   { return $this->hasMany(SubjectAssignment::class); }
    public function examSchedules() { return $this->hasMany(ExamSchedule::class); }
    public function remarks()       { return $this->hasMany(CourseRemark::class); }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_assignments', 'subject_id', 'teacher_id');
    }
}
