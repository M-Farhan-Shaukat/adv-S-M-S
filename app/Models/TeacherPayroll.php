<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class TeacherPayroll extends Model
{
    use BelongsToSchool;
    protected $table = 'teacher_payrolls';
    protected $fillable = [
        'teacher_id',
        'school_id',
        'school_session_id',
        'month',
        'year',
        'total_minutes',
        'gross_salary',
        'deduction',
        'net_salary',
        'required_minutes',
        'short_minutes',
    ];
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function session()
    {
        return $this->belongsTo(SchoolSession::class);
    }
}
