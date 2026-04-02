<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes,BelongsToSchool;
    protected $table = 'sections';
    protected $fillable = [
        'school_class_id',
        'school_id',
        'name'
    ];
    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
