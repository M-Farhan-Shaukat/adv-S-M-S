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
        return $this->hasMany(SchoolSession::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }
    public function currentSession()
    {
        return $this->hasOne(SchoolSession::class)
            ->whereIn('status', ['active', 'exam']);
    }

    public function activeSession()
    {
        return $this->hasOne(SchoolSession::class)
            ->where('status', 'active');
    }
}
