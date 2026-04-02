<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class SubjectAssignment extends Model
{
    use BelongsToSchool;
    protected $table = 'subject_assignments';
    protected $fillable = [
        'school_id',
        'school_session_id',
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
    ];
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function session()
    {
        return $this->belongsTo(SchoolSession::class);
    }
}
