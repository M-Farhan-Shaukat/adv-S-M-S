<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $table = 'fee_types';
    protected $fillable = ['name', 'school_id'];

    public function school()      { return $this->belongsTo(School::class); }
    public function structures()  { return $this->hasMany(FeeStructure::class); }
}
