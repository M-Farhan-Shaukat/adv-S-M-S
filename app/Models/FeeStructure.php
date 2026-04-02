<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $table = 'fee_structure';
    protected $fillable = [
        'school_id',
        'class_id',
        'name',
        'amount',
    ];
}
