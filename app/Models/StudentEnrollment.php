<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use BelongsToSchool;
    protected $table = 'student_enrollments';

    protected $fillable = [
        'student_id',
        'school_id',
        'school_session_id',
        'school_class_id',
        'section_id',
        'is_current',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function session()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
