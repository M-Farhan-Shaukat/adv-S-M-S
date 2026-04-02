<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use BelongsToSchool;
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
