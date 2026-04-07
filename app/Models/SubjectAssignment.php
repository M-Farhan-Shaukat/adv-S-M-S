<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectAssignment extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'subject_assignments';
    protected $fillable = [
        'school_id', 'school_session_id', 'school_class_id',
        'section_id', 'subject_id', 'teacher_id',
    ];

    public function school()      { return $this->belongsTo(School::class); }
    public function session()     { return $this->belongsTo(SchoolSession::class, 'school_session_id'); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function section()     { return $this->belongsTo(Section::class); }
    public function subject()     { return $this->belongsTo(Subject::class); }
    public function teacher()     { return $this->belongsTo(Teacher::class); }

    // Aliases
    public function class()   { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function session2() { return $this->belongsTo(SchoolSession::class, 'school_session_id'); }
}
