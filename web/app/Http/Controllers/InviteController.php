<?php

namespace App\Http\Controllers;

use App\Models\EventParticipant;
use App\Models\ScheduleParticipant;
use Illuminate\Support\Facades\Auth;

class InviteController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $eventInvites = EventParticipant::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'invited'])
            ->with(['event.schedule', 'event.creator'])
            ->latest()
            ->get();

        $scheduleInvites = ScheduleParticipant::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'invited'])
            ->with(['schedule.owner'])
            ->latest()
            ->get();

        return view('invites.index', compact(
            'eventInvites',
            'scheduleInvites'
        ));
    }

    public function acceptEvent(EventParticipant $participant)
    {
        abort_unless($participant->user_id === Auth::id(), 403);

        $participant->update([
            'status' => 'accepted',
        ]);

        return redirect()
            ->route('invites.index')
            ->with('success', 'Event invite accepted.');
    }

    public function declineEvent(EventParticipant $participant)
    {
        abort_unless($participant->user_id === Auth::id(), 403);

        $participant->update([
            'status' => 'declined',
        ]);

        return redirect()
            ->route('invites.index')
            ->with('success', 'Event invite declined.');
    }

    public function acceptSchedule(ScheduleParticipant $participant)
    {
        abort_unless($participant->user_id === Auth::id(), 403);

        $participant->update([
            'status' => 'accepted',
        ]);

        return redirect()
            ->route('invites.index')
            ->with('success', 'Schedule invite accepted.');
    }

    public function declineSchedule(ScheduleParticipant $participant)
    {
        abort_unless($participant->user_id === Auth::id(), 403);

        $participant->update([
            'status' => 'declined',
        ]);

        return redirect()
            ->route('invites.index')
            ->with('success', 'Schedule invite declined.');
    }
}