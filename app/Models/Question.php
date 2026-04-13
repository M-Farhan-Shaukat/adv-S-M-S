<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'question_bank_id', 'type',
        'question_text', 'option_a', 'option_b', 'option_c', 'option_d',
        'correct_answer', 'answer_hint', 'marks', 'difficulty', 'is_approved',
    ];

    public function bank() { return $this->belongsTo(QuestionBank::class, 'question_bank_id'); }
}
