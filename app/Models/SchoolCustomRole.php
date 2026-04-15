<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolCustomRole extends Model
{
    use SoftDeletes;

    protected $fillable = ['school_id', 'name', 'slug', 'description'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function permissions()
    {
        return $this->hasMany(SchoolCustomRolePermission::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'school_custom_role_users');
    }

    public function permissionList(): array
    {
        return $this->permissions()->pluck('permission')->toArray();
    }

    public function syncPermissionList(array $permissions): void
    {
        $this->permissions()->delete();
        foreach (array_filter($permissions) as $perm) {
            $this->permissions()->create(['permission' => $perm]);
        }
    }
}
