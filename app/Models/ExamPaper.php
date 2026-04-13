<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamPaper extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'subject_id', 'school_class_id', 'created_by',
        'title', 'language', 'total_marks', 'duration_minutes',
        'mcq_count', 'short_count', 'long_count',
        'mcq_marks', 'short_marks', 'long_marks',
        'exam_date', 'instructions',
    ];

    public function subject()     { return $this->belongsTo(Subject::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }

    public function paperQuestions()
    {
        return $this->hasMany(ExamPaperQuestion::class)->orderBy('order');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_paper_questions')
            ->withPivot('order', 'marks')
            ->orderByPivot('order');
    }

    public function mcqs()    { return $this->questions()->where('type', 'mcq'); }
    public function shorts()  { return $this->questions()->where('type', 'short'); }
    public function longs()   { return $this->questions()->where('type', 'long'); }
}
