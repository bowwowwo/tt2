<?php

namespace App\Models;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'is_shared',
    ];

    protected $casts = [
        'is_shared' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    //relations in case i need them for " who owns it"
    public function schedules()
    {
        return $this->hasMany(\App\Models\Schedule::class, 'owner_id');
    }

    public function createdEvents()
    {
        return $this->hasMany(\App\Models\Event::class, 'created_by');
    }

    public function eventParticipants()
    {
        return $this->hasMany(\App\Models\EventParticipant::class);
    }

    public function reminders()
    {
        return $this->hasMany(\App\Models\Reminder::class, 'created_by');
    }
}