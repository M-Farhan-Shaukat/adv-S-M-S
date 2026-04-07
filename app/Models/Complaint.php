<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complaint extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'user_id', 'subject', 'description',
        'type', 'status', 'resolution', 'resolved_by', 'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function resolver() { return $this->belongsTo(User::class, 'resolved_by'); }
}
