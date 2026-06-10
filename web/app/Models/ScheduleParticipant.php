<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleParticipant extends Model
{
    protected $fillable = [
        'schedule_id',
        'user_id',
        'role',
        'status',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}