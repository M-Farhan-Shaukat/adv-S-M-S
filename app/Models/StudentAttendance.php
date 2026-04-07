<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAttendance extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'student_id', 'school_session_id', 'section_id',
        'date', 'status', 'remarks',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function section() { return $this->belongsTo(Section::class); }
}
