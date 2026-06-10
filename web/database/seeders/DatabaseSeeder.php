<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Reminder;
use App\Models\Schedule;
use App\Models\User;
use App\Models\ScheduleParticipant;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users ---
        $user1 = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'name' => 'Guy1',
            'email' => 'guy@example.com',
            'password' => bcrypt('password'),
        ]);

        $user3 = User::create([
            'name' => 'Charles Charlie Charlatan',
            'email' => 'c@example.com',
            'password' => bcrypt('password'),
        ]);

        // --- Schedules ---
        $schedule1 = Schedule::create([
            'owner_id' => $user1->id,
            'name' => 'Schedule1',
            'is_shared' => true,
        ]);

        $schedule2 = Schedule::create([
            'owner_id' => $user2->id,
            'name' => 'Schedule2',
            'is_shared' => false,
        ]);

        // --- schedule participants ---

        ScheduleParticipant::create([
            'schedule_id' => $schedule1->id,
            'user_id' => $user1->id,
            'role' => 'owner',
            'status' => 'accepted',
        ]);

        ScheduleParticipant::create([
            'schedule_id' => $schedule1->id,
            'user_id' => $user2->id,
            'role' => 'participant',
            'status' => 'accepted',
        ]);

        ScheduleParticipant::create([
            'schedule_id' => $schedule1->id,
            'user_id' => $user3->id,
            'role' => 'participant',
            'status' => 'accepted',
        ]);

        // --- Events ---
        $event1 = Event::create([
            'schedule_id' => $schedule1->id,
            'title' => 'Meeting',
            'description' => 'meeting for a project',
            'start_time' => now()->addDays(2)->setTime(10, 0),
            'end_time' => now()->addDays(2)->setTime(11, 0),
            'created_by' => $user1->id,
            'collaboration' => true,
        ]);

        $event2 = Event::create([
            'schedule_id' => $schedule1->id,
            'title' => 'coffee thingy',
            'description' => null,
            'start_time' => now()->addDays(5)->setTime(14, 0),
            'end_time' => now()->addDays(5)->setTime(15, 30),
            'created_by' => $user1->id,
            'collaboration' => true,
        ]);

        $event3 = Event::create([
            'schedule_id' => $schedule2->id,
            'title' => 'legs',
            'description' => 'leg day',
            'start_time' => now()->addDay()->setTime(18, 0),
            'end_time' => now()->addDay()->setTime(19, 0),
            'created_by' => $user2->id,
            'collaboration' => false,
        ]);

        // --- Event Participants ---
        // Event 1
        EventParticipant::create([
            'event_id' => $event1->id,
            'user_id' => $user1->id,
            'role' => 'owner',
            'status' => 'owner',
        ]);

        EventParticipant::create([
            'event_id' => $event1->id,
            'user_id' => $user2->id,
            'role' => 'viewer',
            'status' => 'invited',
        ]);

        EventParticipant::create([
            'event_id' => $event1->id,
            'user_id' => $user3->id,
            'role' => 'viewer',
            'status' => 'accepted',
        ]);

        // Event 2
        EventParticipant::create([
            'event_id' => $event2->id,
            'user_id' => $user1->id,
            'role' => 'owner',
            'status' => 'accepted',
        ]);

        EventParticipant::create([
            'event_id' => $event2->id,
            'user_id' => $user2->id,
            'role' => 'viewer',
            'status' => 'accepted',
        ]);

        // --- Reminders ---
        Reminder::create([
            'event_id' => $event1->id,
            'when' => $event1->start_time->copy()->subHours(24),
            'type' => 'email',
            'created_by' => $user1->id,
        ]);

        Reminder::create([
            'event_id' => $event1->id,
            'when' => $event1->start_time->copy()->subMinutes(30),
            'type' => 'push',
            'created_by' => $user1->id,
        ]);
    }
}