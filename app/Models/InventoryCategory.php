<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = ['school_id', 'name', 'description'];

    public function items() { return $this->hasMany(InventoryItem::class); }
}
