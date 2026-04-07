<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseRemark extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'subject_id', 'student_id', 'user_id',
        'remark', 'type', 'teacher_response',
    ];

    public function subject() { return $this->belongsTo(Subject::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function user() { return $this->belongsTo(User::class); }
}
