<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

    $schedules = Schedule::query()
        ->visibleTo($userId)
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

        return view('schedules.schedules', compact(
            'schedules',
            'selectedSchedule',
            'selectedScheduleId',
            'month',
            'calendarDays',
            'eventsByDate'
        ));
    }

    public function create()
    {
        return view('schedules.create');
    }

    public function store(Request $request)
    {
        $collaboratorIds = array_values(array_filter(
            $request->input('collaborator_user_ids', []),
            fn ($id) => filled($id)
        ));

        $request->merge([
            'collaborator_user_ids' => $collaboratorIds,
        ]);

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'collaboration' => ['nullable', 'boolean'],

                'collaborator_user_ids' => [
                    $request->boolean('collaboration') ? 'required' : 'nullable',
                    'array',
                ],

                'collaborator_user_ids.*' => [
                    'required',
                    'integer',
                    'distinct',
                    'exists:users,id',
                    Rule::notIn([Auth::id()]),
                ],
            ],
            [
                'collaborator_user_ids.required' => 'Must add at least one collaborator.',
                'collaborator_user_ids.*.required' => 'Please enter an ID for each collaborator.',
                'collaborator_user_ids.*.integer' => 'Each collaborator ID must be a number.',
                'collaborator_user_ids.*.distinct' => 'You added the same collaborator more than once.',
                'collaborator_user_ids.*.exists' => 'No user was found with one of the collaborator IDs.',
                'collaborator_user_ids.*.not_in' => 'You cannot add yourself as a collaborator.',
            ]
        );

        $schedule = DB::transaction(function () use ($validated, $request) {
            $schedule = Schedule::create([
                'owner_id' => Auth::id(),
                'name' => $validated['name'],
                'is_shared' => $request->boolean('collaboration'),
            ]);

            $schedule->participants()->create([
                'user_id' => Auth::id(),
                'role' => 'owner',
                'status' => 'accepted',
            ]);

            if ($request->boolean('collaboration')) {
                foreach ($validated['collaborator_user_ids'] as $collaboratorUserId) {
                    $schedule->participants()->create([
                        'user_id' => $collaboratorUserId,
                        'role' => 'participant',
                        'status' => 'pending',
                    ]);
                }
            }

            return $schedule;
        });

        return redirect()
            ->route('schedules.index', ['schedule' => $schedule->id])
            ->with('success', 'Schedule created successfully.');
    }
}