<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolCustomRolePermission extends Model
{
    protected $fillable = ['school_custom_role_id', 'permission'];

    public function role()
    {
        return $this->belongsTo(SchoolCustomRole::class, 'school_custom_role_id');
    }
}
