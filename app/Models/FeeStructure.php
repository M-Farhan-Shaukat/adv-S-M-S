<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use BelongsToSchool;

    protected $table = 'fee_structure';
    protected $fillable = [
        'school_id',
        'school_class_id',
        'name',
        'amount',
    ];
}
