<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'school_id', 'user_id', 'name', 'email', 'phone',
        'designation', 'salary', 'joining_date', 'is_active',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function salaries() { return $this->hasMany(StaffSalary::class); }
}
