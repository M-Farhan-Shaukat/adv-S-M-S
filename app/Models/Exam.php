<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'school_session_id', 'school_class_id',
        'name', 'type', 'start_date', 'end_date', 'status', 'description',
    ];

    public function session() { return $this->belongsTo(SchoolSession::class, 'school_session_id'); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function schedules() { return $this->hasMany(ExamSchedule::class); }
}
