<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    
    protected $fillable = [
        'event_id',
        'when',
        'type',
        'created_by',
    ];

    //casts for easy things
    protected $casts = [
        'when' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
