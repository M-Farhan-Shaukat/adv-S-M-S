<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffSalary extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'staff_id', 'month', 'year',
        'basic_salary', 'allowances', 'deductions', 'net_salary',
        'status', 'paid_date', 'notes',
    ];

    public function staff() { return $this->belongsTo(Staff::class); }
}
