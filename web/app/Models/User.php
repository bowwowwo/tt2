<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isGuest(): bool{
        return $this->role === 'guest';
    }
    public function isRegularUser() {
        return $this->role === 'user';
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'owner_id');
    }

    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function eventParticipants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function scheduleParticipants()
    {
        return $this->hasMany(ScheduleParticipant::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'created_by');
    }
}
