<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;
    protected $table = 'schools';
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'is_active',
    ];
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }
    public function currentSession()
    {
        return $this->hasOne(Session::class)
            ->whereIn('status', ['active', 'exam']);
    }

    public function activeSession()
    {
        return $this->hasOne(Session::class)
            ->where('status', 'active');
    }
}
