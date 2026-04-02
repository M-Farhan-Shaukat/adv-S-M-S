<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    use BelongsToSchool;
    protected $table = 'teacher_attendance';
    protected $fillable = [
        'teacher_id',
        'school_id',
        'school_session_id',
        'date',
        'check_in',
        'check_out',
        'working_minutes',
        'status',
    ];
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function session()
    {
        return $this->belongsTo(SchoolSession::class);
    }
}
