<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $schedules = Schedule::query()
            ->where(function ($query) use ($userId) {
                $query->where('owner_id', $userId)
                    ->orWhere('is_shared', true);
            })
            ->orderBy('name')
            ->get();

        $selectedScheduleId = $request->integer('schedule');

        if (!$selectedScheduleId || !$schedules->contains('id', $selectedScheduleId)) {
            $selectedScheduleId = $schedules->first()?->id;
        }

        $selectedSchedule = $schedules->firstWhere('id', $selectedScheduleId);

        try {
            $month = Carbon::createFromFormat('Y-m', $request->query('month', now()->format('Y-m')))
                ->startOfMonth();
        } catch (\Exception $e) {
            $month = now()->startOfMonth();
        }

        $calendarStart = $month->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $calendarDays = collect();

        $day = $calendarStart->copy();

        while ($day <= $calendarEnd) {
            $calendarDays->push($day->copy());
            $day->addDay();
        }

        $eventsByDate = collect();

        if ($selectedScheduleId) {
            $events = Event::query()
                ->where('schedule_id', $selectedScheduleId)
                ->whereBetween('start_time', [
                    $calendarStart->copy()->startOfDay(),
                    $calendarEnd->copy()->endOfDay(),
                ])
                ->with(['participants.user'])
                ->orderBy('start_time')
                ->get();

            $eventsByDate = $events->groupBy(function ($event) {
                return $event->start_time->toDateString();
            });
        }

        return view('schedules', compact(
            'schedules',
            'selectedSchedule',
            'selectedScheduleId',
            'month',
            'calendarDays',
            'eventsByDate'
        ));
    }
}