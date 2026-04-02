<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use SoftDeletes,BelongsToSchool;
    protected $table = 'sessions';
    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }
}
