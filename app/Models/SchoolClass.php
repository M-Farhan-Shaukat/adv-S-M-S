<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use SoftDeletes;
    protected $table = 'school_classes';
    protected $fillable = [
        'session_id',
        'name',
        'code',
    ];
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
