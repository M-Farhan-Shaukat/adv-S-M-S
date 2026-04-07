<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingSchedule extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id', 'scheduled_by', 'title', 'description',
        'meeting_date', 'duration_minutes', 'venue', 'type', 'status',
    ];

    protected $casts = ['meeting_date' => 'datetime'];

    public function organizer() { return $this->belongsTo(User::class, 'scheduled_by'); }
    public function participants() { return $this->hasMany(MeetingParticipant::class); }
}
