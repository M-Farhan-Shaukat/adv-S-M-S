<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSchedule extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'exam_id', 'subject_id', 'section_id',
        'exam_date', 'start_time', 'end_time', 'total_marks', 'passing_marks', 'room',
    ];

    public function exam() { return $this->belongsTo(Exam::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function section() { return $this->belongsTo(Section::class); }
    public function marks() { return $this->hasMany(StudentMark::class); }
}
