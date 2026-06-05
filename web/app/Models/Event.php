<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Event extends Model
{
        protected $fillable = [
        'schedule_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'created_by',
        'collaboration',
    ];

    protected $casts = [
        'start_time'     => 'datetime',
        'end_time'       => 'datetime',
        'collaboration'  => 'boolean',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
