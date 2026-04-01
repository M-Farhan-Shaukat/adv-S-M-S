<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    protected $fillable = [
        'school_id',
        'name',
    ];
    public function assignments()
    {
        return $this->hasMany(SubjectAssignment::class);
    }
}
