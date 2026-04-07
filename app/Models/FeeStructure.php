<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use BelongsToSchool;

    protected $table = 'fee_structures';
    protected $fillable = [
        'school_id',
        'school_class_id',
        'fee_type_id',
        'name',
        'amount',
    ];
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }

    public function type()
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }
}
