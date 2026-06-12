<x-layout>
    <div class="py-3 px-4">

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <h1 class="display-5 mb-0">Schedules</h1>

                {{-- Schedule selector --}}
                <div class="dropdown pt-2">
                    <button
                        class="btn btn-outline-dark dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        {{ $selectedSchedule?->name ?? 'Select schedule' }}
                    </button>

                    {{-- add schedule--}}
                    <a
                        href="{{ route('schedules.create') }}"
                        class="btn btn-dark pt-2"
                    >
                        Add Schedule
                    </a>

                    <a
                        href="{{ route('events.create', ['schedule' => $selectedScheduleId]) }}"
                        class="btn btn-dark"
                    >
                        Create event
                    </a>

                    <button
                        type="button"
                        class="btn btn-outline-dark pt-2"
                        data-bs-toggle="modal"
                        data-bs-target="#scheduleCollaboratorsModal"
                        @disabled(!$selectedSchedule)
                    >
                        See current collaborators
                    </button>

                    <ul class="dropdown-menu">
                        @forelse($schedules as $schedule)
                            <li>
                                <a
                                    class="dropdown-item {{ $selectedScheduleId === $schedule->id ? 'active' : '' }}"
                                    href="{{ route('schedules.index', [
                                        'schedule' => $schedule->id,
                                        'month' => $month->format('Y-m'),
                                    ]) }}"
                                >
                                    {{ $schedule->name }}

                                    @if($schedule->owner_id === auth()->id())
                                        <span class="text-muted small">(Owner)</span>
                                    @elseif($schedule->is_shared)
                                        <span class="text-muted small">(Shared)</span>
                                    @endif
                                </a>
                            </li>
                        @empty
                            <li>
                                <span class="dropdown-item text-muted">
                                    No schedules available
                                </span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Month navigation --}}
            <div class="d-flex align-items-center gap-2 pt-2">
                <a
                    href="{{ route('schedules.index', [
                        'schedule' => $selectedScheduleId,
                        'month' => $month->copy()->subMonth()->format('Y-m'),
                    ]) }}"
                    class="btn btn-outline-dark"
                >
                    Previous
                </a>

                <div class="fw-semibold fs-5 px-2">
                    {{ $month->format('F Y') }}
                </div>

                <a
                    href="{{ route('schedules.index', [
                        'schedule' => $selectedScheduleId,
                        'month' => $month->copy()->addMonth()->format('Y-m'),
                    ]) }}"
                    class="btn btn-outline-dark"
                >
                    Next
                </a>
            </div>
        </div>

        @if(!$selectedScheduleId)
            <div class="alert alert-warning">
                You do not have any schedules yet.
            </div>
        @else

            {{-- Calendar --}}
            <div class="border border-dark border-2">

                {{-- Weekday headings --}}
                <div class="row g-0 text-center fw-bold border-bottom border-dark">
                    <div class="col py-2 border-end border-dark">Sun</div>
                    <div class="col py-2 border-end border-dark">Mon</div>
                    <div class="col py-2 border-end border-dark">Tue</div>
                    <div class="col py-2 border-end border-dark">Wed</div>
                    <div class="col py-2 border-end border-dark">Thu</div>
                    <div class="col py-2 border-end border-dark">Fri</div>
                    <div class="col py-2">Sat</div>
                </div>

                {{-- Calendar weeks --}}
                @foreach($calendarDays->chunk(7) as $week)
                    <div class="row g-0">
                        @foreach($week as $day)
                            @php
                                $dateKey = $day->toDateString();
                                $dayEvents = $eventsByDate->get($dateKey, collect());
                                $isCurrentMonth = $day->month === $month->month;
                                $isToday = $day->isToday();
                            @endphp

                            <div
                                class="col border-end border-bottom border-dark p-2 {{ !$isCurrentMonth ? 'bg-light text-muted' : '' }}"
                                style="min-height: 140px;"
                            >
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span
                                        class="fw-bold {{ $isToday ? 'bg-dark text-white rounded px-2' : '' }}"
                                    >
                                        {{ $day->day }}
                                    </span>
                                </div>

                                {{-- days --}}

                                @if($dayEvents->count())
                                    {{-- Show event previews in the calendar cell --}}
                                    @foreach($dayEvents->take(2) as $event)
                                        <div class="mb-2 p-2 rounded border border-dark bg-info-subtle small">
                                            <div class="fw-semibold">
                                                {{ $event->title }}
                                            </div>

                                            <div>
                                                {{ $event->start_time->format('g:i a') }}
                                            </div>

                                            @if($event->collaboration)
                                                <div class="fst-italic">
                                                    Collaboration
                                                </div>
                                            @endif

                                            @if($event->created_by === auth()->id())
                                                <div class="mt-2">
                                                    <a
                                                        href="{{ route('events.edit', $event) }}"
                                                        class="btn btn-sm btn-outline-dark"
                                                    >
                                                        Edit
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach

                                    {{-- If multiple events, show modal button --}}
                                    @if($dayEvents->count() > 1)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-dark w-100"
                                            data-bs-toggle="modal"
                                            data-bs-target="#eventsModal{{ $day->format('Ymd') }}"
                                        >
                                            View all {{ $dayEvents->count() }} events
                                        </button>
                                    @endif

                                    {{-- Day events modal --}}
                                    <div
                                        class="modal fade"
                                        id="eventsModal{{ $day->format('Ymd') }}"
                                        tabindex="-1"
                                        aria-labelledby="eventsModalLabel{{ $day->format('Ymd') }}"
                                        aria-hidden="true"
                                    >
                                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                                            <div class="modal-content border border-2 border-dark">
                                                <div class="modal-header border-dark">
                                                    <h5
                                                        class="modal-title"
                                                        id="eventsModalLabel{{ $day->format('Ymd') }}"
                                                    >
                                                        Events for {{ $day->format('F j, Y') }}
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close"
                                                    ></button>
                                                </div>

                                                <div class="modal-body">
                                                    @foreach($dayEvents as $event)
                                                        <div class="border border-dark rounded p-3 mb-3 bg-info-subtle">
                                                            <div class="fw-bold fs-5">
                                                                {{ $event->title }}
                                                            </div>

                                                            <div class="small">
                                                                Start:
                                                                {{ $event->start_time->format('g:i a') }}
                                                            </div>

                                                            @if($event->end_time)
                                                                <div class="small">
                                                                    End:
                                                                    {{ $event->end_time->format('g:i a') }}
                                                                </div>
                                                            @endif

                                                            @if($event->description)
                                                                <div class="mt-2">
                                                                    {{ $event->description }}
                                                                </div>
                                                            @endif

                                                            <div class="small fst-italic mt-2">
                                                                Collaboration:
                                                                {{ $event->collaboration ? 'Yes' : 'No' }}
                                                            </div>

                                                            @if($event->created_by === auth()->id())
                                                                <div class="mt-3">
                                                                    <a
                                                                        href="{{ route('events.edit', $event) }}"
                                                                        class="btn btn-sm btn-outline-dark"
                                                                    >
                                                                        Edit event
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="modal-footer border-dark">
                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>

{{-- Schedule collaborators modal --}}
<div
    class="modal fade"
    id="scheduleCollaboratorsModal"
    tabindex="-1"
    aria-labelledby="scheduleCollaboratorsModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border border-2 border-dark">
            <div class="modal-header border-dark">
                <h5 class="modal-title" id="scheduleCollaboratorsModalLabel">
                    Collaborators for {{ $selectedSchedule?->name ?? 'Schedule' }}
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body">
                @if($selectedSchedule)
                    <div class="border border-dark rounded p-3 mb-3 bg-light">
                        <div class="fw-bold">
                            Owner
                        </div>

                        <div>
                            {{ $selectedSchedule->owner?->name ?? 'Unknown owner' }}
                        </div>

                        <div class="small text-muted">
                            User ID: {{ $selectedSchedule->owner_id }}
                        </div>

                        <div class="small text-muted">
                            Email: {{ $selectedSchedule->owner?->email ?? 'No email' }}
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">
                        Collaborators
                    </h6>

                    @php
                        $collaborators = $selectedSchedule->participants
                            ->where('user_id', '!=', $selectedSchedule->owner_id)
                            ->where('status', 'accepted');
                    @endphp

                    @forelse($collaborators as $participant)
                        <div class="border border-dark rounded p-3 mb-3">
                            <div class="fw-semibold">
                                {{ $participant->user?->name ?? 'Unknown user' }}
                            </div>

                            <div class="small">
                                User ID: {{ $participant->user_id }}
                            </div>

                            <div class="small">
                                Email: {{ $participant->user?->email ?? 'No email' }}
                            </div>

                            <div class="small">
                                Role: {{ ucfirst($participant->role) }}
                            </div>

                            <div class="small">
                                Status:
                                @if($participant->status === 'accepted')
                                    <span class="badge bg-success">Accepted</span>
                                @elseif($participant->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($participant->status === 'declined')
                                    <span class="badge bg-danger">Declined</span>
                                @else
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($participant->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">
                            This schedule has no collaborators.
                        </p>
                    @endforelse
                @else
                    <p class="text-muted mb-0">
                        No schedule selected.
                    </p>
                @endif
            </div>

            <div class="modal-footer border-dark">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
</x-layout>