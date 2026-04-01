<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';

    protected $fillable = [
        'school_id',
        'name',
        'email',
        'phone',
        'salary',
        'is_active',
    ];
    public function assignments()
    {
        return $this->hasMany(SubjectAssignment::class);
    }
}
