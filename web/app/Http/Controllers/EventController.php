<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Event;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
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

        $events = collect();

        if ($selectedScheduleId) {
            $events = Event::query()
                ->where('schedule_id', $selectedScheduleId)
                ->where('start_time', '>=', now())
                ->where(function ($query) use ($userId) {
                    $query->where('created_by', $userId)
                        ->orWhereHas('participants', fn ($q) => $q->where('user_id', $userId));
                })
                ->with(['participants.user'])
                ->orderBy('start_time')
                ->get();
        }

        return view('events.index', compact(
            'events',
            'schedules',
            'selectedSchedule',
            'selectedScheduleId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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
            'schedule_id' => ['required', 'exists:schedules,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
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
        ],  // * errors 
        [
            'collaborator_user_ids.required' => 'Must add at least one collaborator.',
            'collaborator_user_ids.*.required' => 'Please enter an ID for each collaborator.',
            'collaborator_user_ids.*.integer' => 'Each collaborator ID must be a number.',
            'collaborator_user_ids.*.distinct' => 'You added the same collaborator more than once.',
            'collaborator_user_ids.*.exists' => 'No user was found with one of the collaborator IDs.',
            'collaborator_user_ids.*.not_in' => 'You cannot add yourself as a collaborator.',
        ]
    );

        $event = Event::create([
            'schedule_id' => $validated['schedule_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'created_by' => Auth::id(),
            'collaboration' => $request->boolean('collaboration'),
        ]);

        $event->participants()->create([
            'user_id' => Auth::id(),
            'role' => 'owner',
            'status' => 'accepted',
        ]);

        if ($request->boolean('collaboration')) {
            foreach ($validated['collaborator_user_ids'] as $collaboratorUserId) {
                $event->participants()->create([
                    'user_id' => $collaboratorUserId,
                    'role' => 'participant',
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()
            ->route('events.index', ['schedule' => $validated['schedule_id']])
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
