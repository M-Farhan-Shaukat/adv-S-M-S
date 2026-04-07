<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecheckRequest extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'student_id', 'student_marks_id',
        'reason', 'status', 'admin_remarks', 'revised_marks',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function mark() { return $this->belongsTo(StudentMark::class, 'student_marks_id'); }
}
