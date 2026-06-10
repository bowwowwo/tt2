<?php

namespace App\Models;

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

    public function participants()
    {
        return $this->hasMany(ScheduleParticipant::class);
    }

    public function scopeVisibleTo($query, $userId)
    {
        return $query->where(function ($query) use ($userId) {
            $query->where('owner_id', $userId)
                ->orWhereHas('participants', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('status', 'accepted');
                });
        });
    }
}