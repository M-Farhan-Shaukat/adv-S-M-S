<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPaperQuestion extends Model
{
    public $timestamps = false;
    protected $fillable = ['exam_paper_id', 'question_id', 'order', 'marks'];

    public function question() { return $this->belongsTo(Question::class); }
    public function paper()    { return $this->belongsTo(ExamPaper::class); }
}
