<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $table = 'school_classes';
    protected $fillable = ['school_session_id', 'school_id', 'name', 'code'];

    public function school()        { return $this->belongsTo(School::class); }
    public function session()       { return $this->belongsTo(SchoolSession::class, 'school_session_id'); }
    public function sections()      { return $this->hasMany(Section::class); }
    public function enrollments()   { return $this->hasMany(StudentEnrollment::class); }
    public function feeStructures() { return $this->hasMany(FeeStructure::class); }
    public function subjects()      { return $this->hasMany(SubjectAssignment::class, 'school_class_id'); }
    public function exams()         { return $this->hasMany(Exam::class, 'school_class_id'); }
    public function feeVouchers()   { return $this->hasMany(FeeVoucher::class, 'school_class_id'); }
}
