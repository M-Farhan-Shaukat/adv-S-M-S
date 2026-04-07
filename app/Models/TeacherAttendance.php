<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherAttendance extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'teacher_attendances';
    protected $fillable = [
        'teacher_id', 'school_id', 'school_session_id',
        'date', 'check_in', 'check_out', 'working_minutes', 'status',
    ];

    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function school()  { return $this->belongsTo(School::class); }
    public function session() { return $this->belongsTo(SchoolSession::class, 'school_session_id'); }
}
