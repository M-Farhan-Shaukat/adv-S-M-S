<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionBank extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'subject_id', 'school_class_id', 'created_by',
        'title', 'chapter', 'difficulty', 'language',
        'source_image', 'extracted_text',
    ];

    public function subject()     { return $this->belongsTo(Subject::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'school_class_id'); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
    public function questions()   { return $this->hasMany(Question::class); }
}
