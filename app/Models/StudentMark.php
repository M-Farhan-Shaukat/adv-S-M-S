<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMark extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'exam_schedule_id', 'student_id',
        'obtained_marks', 'is_absent', 'remarks', 'is_published',
    ];

    public function examSchedule() { return $this->belongsTo(ExamSchedule::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function recheckRequest() { return $this->hasOne(RecheckRequest::class, 'student_marks_id'); }

    public function getGradeAttribute(): string
    {
        $pct = $this->examSchedule ? ($this->obtained_marks / $this->examSchedule->total_marks) * 100 : 0;
        return match(true) {
            $pct >= 90 => 'A+',
            $pct >= 80 => 'A',
            $pct >= 70 => 'B',
            $pct >= 60 => 'C',
            $pct >= 50 => 'D',
            default    => 'F',
        };
    }
}
